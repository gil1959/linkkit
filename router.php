<?php
// Router script for PHP built-in server to mimic .htaccess behavior
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files directly (images, css, js, etc.)
$file = __DIR__ . $uri;
if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    return false; // serve the file as-is
}

// For directories, check for index files
if (is_dir($file)) {
    foreach (['index.php', 'index.html'] as $index) {
        if (file_exists($file . '/' . $index)) {
            $_SERVER['SCRIPT_FILENAME'] = $file . '/' . $index;
            require $file . '/' . $index;
            return true;
        }
    }
}

// Route everything else to index.php with altum query param
$path = trim($uri, '/');
if (!empty($path)) {
    $_GET['altum'] = $path;
    $_SERVER['QUERY_STRING'] = 'altum=' . $path;
    if (isset($_SERVER['QUERY_STRING']) && strpos($_SERVER['REQUEST_URI'], '?') !== false) {
        $queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        if ($queryString) {
            parse_str($queryString, $extraParams);
            $_GET = array_merge($_GET, $extraParams);
        }
    }
}

require __DIR__ . '/index.php';
