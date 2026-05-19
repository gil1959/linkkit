-- ============================================================
-- SQL Migration: Plugin Tables untuk linkkit.id
-- Jalankan ini di cPanel phpMyAdmin pada database Anda
-- ============================================================

-- Tabel untuk Push Notifications
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

-- Tabel untuk Push Subscribers
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

-- Tabel untuk Image Optimizations
CREATE TABLE IF NOT EXISTS `image_optimizations` (
    `image_optimization_id` int(11) NOT NULL AUTO_INCREMENT,
    `original_format` varchar(32) DEFAULT NULL,
    `original_size` bigint(20) DEFAULT '0',
    `new_size` bigint(20) DEFAULT '0',
    `percentage_difference` decimal(10,2) DEFAULT '0.00',
    `file` varchar(512) DEFAULT NULL,
    `path` varchar(512) DEFAULT NULL,
    `datetime` datetime DEFAULT NULL,
    PRIMARY KEY (`image_optimization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
