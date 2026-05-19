<?php
/*
 * Image Optimizer Plugin - Using PHP GD (built-in, no external API needed)
 * Implements \Altum\Plugin\ImageOptimizer::optimize() as required by the main app.
 */

defined('ALTUMCODE') || die();

namespace Altum\Plugin;

class ImageOptimizer {

    /**
     * Optimize/compress an image using PHP GD (built-in).
     *
     * @param string $source_path  Absolute path to the source/temp file
     * @param string $new_name     New filename (with extension)
     * @param string $original_name  Original filename (used for extension detection)
     * @param string $upload_path  Relative upload sub-path (e.g. 'avatars/')
     * @return bool  True if optimization was applied, false otherwise
     */
    public static function optimize($source_path, $new_name, $original_name, $upload_path) {

        /* Only process if GD is available */
        if (!extension_loaded('gd')) {
            return false;
        }

        /* Determine the file extension */
        $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        /* Only optimize raster images */
        $supported = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        if (!in_array($extension, $supported)) {
            return false;
        }

        /* Get quality setting (default 75) */
        $quality = (int)(settings()->image_optimizer->quality ?? 75);
        $quality = max(30, min(100, $quality));

        /* Load the image based on type */
        $image = null;
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $image = @imagecreatefromjpeg($source_path);
                break;
            case 'png':
                $image = @imagecreatefrompng($source_path);
                break;
            case 'gif':
                $image = @imagecreatefromgif($source_path);
                break;
            case 'webp':
                $image = @imagecreatefromwebp($source_path);
                break;
            case 'bmp':
                $image = function_exists('imagecreatefrombmp') ? @imagecreatefrombmp($source_path) : null;
                break;
        }

        if (!$image) {
            return false;
        }

        /* Optionally resize if image is too large */
        $max_dimension = (int)(settings()->image_optimizer->max_dimension ?? 2048);
        $width = imagesx($image);
        $height = imagesy($image);

        if ($max_dimension > 0 && ($width > $max_dimension || $height > $max_dimension)) {
            if ($width >= $height) {
                $new_width  = $max_dimension;
                $new_height = (int)round($height * $max_dimension / $width);
            } else {
                $new_height = $max_dimension;
                $new_width  = (int)round($width * $max_dimension / $height);
            }

            $resized = imagecreatetruecolor($new_width, $new_height);

            /* Preserve transparency for PNG/GIF/WebP */
            if (in_array($extension, ['png', 'gif', 'webp'])) {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
                imagefilledrectangle($resized, 0, 0, $new_width, $new_height, $transparent);
            }

            imagecopyresampled($resized, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        /* Save optimized image back to source_path (in-place, before the main code moves it) */
        $success = false;
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($image, $source_path, $quality);
                $success = true;
                break;
            case 'png':
                /* PNG quality is 0-9, convert from 0-100 */
                $png_quality = (int)round((100 - $quality) / 11.111);
                imagepng($image, $source_path, $png_quality);
                $success = true;
                break;
            case 'gif':
                imagegif($image, $source_path);
                $success = true;
                break;
            case 'webp':
                imagewebp($image, $source_path, $quality);
                $success = true;
                break;
            case 'bmp':
                if (function_exists('imagebmp')) {
                    imagebmp($image, $source_path);
                    $success = true;
                }
                break;
        }

        imagedestroy($image);
        return $success;
    }
}
