<?php
/*
 * Image Optimizer Plugin - init.php
 * This file is auto-loaded by Plugin::initialize() when the plugin is active,
 * and also called during install/uninstall/activate/disable.
 */
namespace Altum\Plugin;

defined('ALTUMCODE') || die();

/* Load the ImageOptimizer class */
require_once __DIR__ . '/ImageOptimizer.php';

class Imageoptimizer {

    public static function install() {
        /* Create the image_optimizations table */
        database()->query("
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
