<?php

/* Configuration of the site */
define('DATABASE_SERVER',   'localhost');
define('DATABASE_USERNAME', 'root');
define('DATABASE_PASSWORD', '');
define('DATABASE_NAME',     'linkkit_BiolinkPr0');
define('SITE_URL',          'http://127.0.0.1:8000/');

/* Cache driver */
define('CACHE_DRIVER', 'files'); // Available values: files, redis, apcu

/* Only modify this if you want to use redis for caching instead of the default file system caching */
define('REDIS_IS_ENABLED', 0);
define('REDIS_SOCKET_PATH', null);
define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', 6379);
define('REDIS_PASSWORD', '');
define('REDIS_DATABASE', 0);
define('REDIS_TIMEOUT', 2);
define('ALTUMCODE_LICENSE_TYPE', 'extended');