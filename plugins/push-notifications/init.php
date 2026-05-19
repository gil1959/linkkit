<?php
/*
 * Push Notifications Plugin - init.php
 * This file is auto-loaded by Plugin::initialize() when the plugin is active,
 * and also called during install/uninstall/activate/disable.
 */
namespace Altum\Plugin;

defined('ALTUMCODE') || die();

class Pushnotifications {

    public static function install() {
        /* Create push_notifications table */
        database()->query("
            CREATE TABLE IF NOT EXISTS `push_notifications` (
                `push_notification_id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(256) DEFAULT NULL,
                `description` varchar(512) DEFAULT NULL,
                `url` varchar(512) DEFAULT NULL,
                `segment` varchar(64) DEFAULT 'all',
                `settings` text,
                `push_subscribers_ids` longtext,
                `sent_push_subscribers_ids` longtext,
                `sent_push_notifications` int(11) DEFAULT '0',
                `total_push_notifications` int(11) DEFAULT '0',
                `status` varchar(64) DEFAULT 'draft',
                `datetime` datetime DEFAULT NULL,
                `last_datetime` datetime DEFAULT NULL,
                `last_sent_datetime` datetime DEFAULT NULL,
                PRIMARY KEY (`push_notification_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        /* Create push_subscribers table */
        database()->query("
            CREATE TABLE IF NOT EXISTS `push_subscribers` (
                `push_subscriber_id` int(11) NOT NULL AUTO_INCREMENT,
                `subscriber_id` varchar(64) NOT NULL,
                `user_id` int(11) DEFAULT NULL,
                `endpoint` varchar(512) NOT NULL,
                `keys` text NOT NULL,
                `ip` varchar(64) DEFAULT NULL,
                `city_name` varchar(256) DEFAULT NULL,
                `country_code` varchar(32) DEFAULT NULL,
                `continent_code` varchar(32) DEFAULT NULL,
                `os_name` varchar(64) DEFAULT NULL,
                `browser_name` varchar(64) DEFAULT NULL,
                `browser_language` varchar(32) DEFAULT NULL,
                `device_type` varchar(32) DEFAULT NULL,
                `datetime` datetime DEFAULT NULL,
                PRIMARY KEY (`push_subscriber_id`),
                UNIQUE KEY `subscriber_id` (`subscriber_id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        /* Write status to settings.json */
        file_put_contents(__DIR__ . '/settings.json', json_encode(['status' => 'installed']));
    }

    public static function uninstall() {
        file_put_contents(__DIR__ . '/settings.json', json_encode(['status' => 'uninstalled']));
    }

    public static function activate() {
        file_put_contents(__DIR__ . '/settings.json', json_encode(['status' => 'active']));
    }

    public static function disable() {
        file_put_contents(__DIR__ . '/settings.json', json_encode(['status' => 'installed']));
    }
}
