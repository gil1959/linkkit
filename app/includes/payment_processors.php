<?php
/*
 * Copyright (c) 2025 AltumCode (https://altumcode.com/)
 *
 * This software is licensed exclusively by AltumCode and is sold only via https://altumcode.com/.
 * Unauthorized distribution, modification, or use of this software without a valid license is not permitted and may be subject to applicable legal actions.
 *
 * 🌍 View all other existing AltumCode projects via https://altumcode.com/
 * 📧 Get in touch for support or general queries via https://altumcode.com/contact
 * 📤 Download the latest version via https://altumcode.com/downloads
 *
 * 🐦 X/Twitter: https://x.com/AltumCode
 * 📘 Facebook: https://facebook.com/altumcode
 * 📸 Instagram: https://instagram.com/altumcode
 */

defined('ALTUMCODE') || die();

return [
    'paypal' => [
        'payment_type' => ['one_time', 'recurring'],
        'icon' => 'fab fa-paypal',
        'color' => '#3b7bbf',
        'dark_color' => '#7fb3ff',
    ],
    'offline_payment' => [
        'payment_type' => ['one_time'],
        'icon' => 'fas fa-university',
        'color' => '#393f4a',
        'dark_color' => '#9aa1ad',
    ],
    'midtrans' => [
        'payment_type' => ['one_time'],
        'icon' => 'fas fa-grip-vertical',
        'color' => '#002855',
        'dark_color' => '#6f9fd1',
    ],
    'tripay' => [
        'payment_type' => ['one_time'],
        'icon' => 'fas fa-money-bill-wave',
        'color' => '#e74c3c',
        'dark_color' => '#ff8a7a',
    ],
];
