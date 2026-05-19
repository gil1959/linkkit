<?php
/*
 * Push Notifications Plugin - Cron.php
 */

defined('ALTUMCODE') || die();

/* We'll send up to X push notifications per run */
$max_batch_size = 50;

/* Fetch a push notification in "processing" status */
$push_notification = db()->where('status', 'processing')->getOne('push_notifications');
if(!$push_notification) {
    return;
}

$push_notification->push_subscribers_ids = json_decode($push_notification->push_subscribers_ids ?? '[]', true);
$push_notification->sent_push_subscribers_ids = json_decode($push_notification->sent_push_subscribers_ids ?? '[]', true);
$push_notification->settings = json_decode($push_notification->settings ?? '[]');

/* Find which subscribers are left to process */
$remaining_subscriber_ids = array_values(array_diff($push_notification->push_subscribers_ids, $push_notification->sent_push_subscribers_ids));

/* If no one is left, mark as "sent" and exit */
if(empty($remaining_subscriber_ids)) {
    $sent_push_notifications_count = count($push_notification->sent_push_subscribers_ids);
    db()->where('push_notification_id', $push_notification->push_notification_id)->update('push_notifications', [
        'sent_push_notifications'   => $sent_push_notifications_count,
        'sent_push_subscribers_ids' => json_encode($push_notification->sent_push_subscribers_ids),
        'status'                    => 'sent',
        'last_sent_datetime'        => get_date(),
    ]);
    return;
}

/* Get all batch subscribers at once in one go */
$subscriber_ids_for_this_run = array_slice($remaining_subscriber_ids, 0, $max_batch_size);

$subscribers = db()
    ->where('push_subscriber_id', $subscriber_ids_for_this_run, 'IN')
    ->get('push_subscribers', null, ['push_subscriber_id', 'endpoint', 'keys']);

$subscribers_ids = array_column($subscribers, 'push_subscriber_id');

/* Non existing subscribers in this batch */
$missing_subscriber_ids = array_diff($subscriber_ids_for_this_run, $subscribers_ids);

/* Mark non existing subscribers as processed (sent) */
$push_notification->sent_push_subscribers_ids = array_merge($push_notification->sent_push_subscribers_ids, $missing_subscriber_ids);

/* Send push notifications only for existing subscribers */
if(!empty($subscribers)) {

    /* Prepare content */
    $content = [
        'title' => $push_notification->title,
        'description' => $push_notification->description,
        'url' => $push_notification->url
    ];

    foreach($subscribers as $subscriber) {
        $result = \Altum\Helpers\PushNotifications::send($content, $subscriber);

        /* If the push failed (e.g. invalid endpoint/unsubscribed), delete from db */
        if(!$result) {
            db()->where('push_subscriber_id', $subscriber->push_subscriber_id)->delete('push_subscribers');
        }

        /* Track who we just processed */
        $push_notification->sent_push_subscribers_ids[] = $subscriber->push_subscriber_id;
    }
}

/* Total "sent" (processed) */
$sent_push_notifications_count = count($push_notification->sent_push_subscribers_ids);

/* Check if all subscribers (existing or not) have been processed */
$all_subscribers_processed = empty(array_diff($push_notification->push_subscribers_ids, $push_notification->sent_push_subscribers_ids));

/* Update push notification once for the entire batch */
db()->where('push_notification_id', $push_notification->push_notification_id)->update('push_notifications', [
    'sent_push_notifications'   => $sent_push_notifications_count,
    'sent_push_subscribers_ids' => json_encode($push_notification->sent_push_subscribers_ids),
    'status'                    => $all_subscribers_processed ? 'sent' : 'processing',
    'last_sent_datetime'        => get_date(),
]);

if(DEBUG) {
    echo '<br />' . 'push_notifications() - push_notification_id - ' . $push_notification->push_notification_id;
}
