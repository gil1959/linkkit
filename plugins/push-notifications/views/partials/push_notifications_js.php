<?php defined('ALTUMCODE') || die() ?>

<script>
    /* Service Worker & Push Notifications */
    if ('serviceWorker' in navigator && 'PushManager' in window) {
        let isSubscribed = false;
        let swRegistration = null;

        const applicationServerPublicKey = '<?= settings()->push_notifications->public_key ?>';

        function urlB64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }

        navigator.serviceWorker.register('<?= SITE_URL ?>sw.js')
            .then(function(swReg) {
                swRegistration = swReg;
                swRegistration.pushManager.getSubscription()
                    .then(function(subscription) {
                        isSubscribed = !(subscription === null);

                        if (!isSubscribed) {
                            subscribeUser();
                        }
                    });
            })
            .catch(function(error) {
                console.error('Service Worker Error', error);
            });

        function subscribeUser() {
            if(Notification.permission === 'denied') return;

            const applicationServerKey = urlB64ToUint8Array(applicationServerPublicKey);
            swRegistration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: applicationServerKey
            })
            .then(function(subscription) {
                isSubscribed = true;
                updateSubscriptionOnServer(subscription, 'subscribe');
            })
            .catch(function(err) {
                console.log('Failed to subscribe the user: ', err);
            });
        }

        function updateSubscriptionOnServer(subscription, type) {
            const subscriptionJson = subscription.toJSON();
            const endpoint = subscriptionJson.endpoint;
            const p256dh = subscriptionJson.keys.p256dh;
            const auth = subscriptionJson.keys.auth;

            const data = new URLSearchParams();
            data.append('endpoint', endpoint);
            data.append('p256dh', p256dh);
            data.append('auth', auth);
            data.append('global_token', '<?= \Altum\Csrf::get() ?>');

            fetch('<?= SITE_URL ?>push-subscribers/' + (type === 'subscribe' ? 'create_ajax' : 'delete_ajax'), {
                method: 'POST',
                body: data,
            });
        }
    }
</script>
