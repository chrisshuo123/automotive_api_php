<?php
// Simpan URL Absolut, atau DB disini
// Awal yang berada pada core/constant, dipindah semua ke config/config

// Database Configuration
define('DB_DRIVER', 'pgsql');
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'automotive_api');
define('DB_USER', 'postgres');
define('DB_PASS', 'root');

// Base URL (without trailing slash)
// define('BASEURL', 'http://localhost/automotive_api/admin/public');
// define('BASEURL', 'http://localhost/automotive_api/user/public');
define('BASEURL', 'http://localhost/automotive_api_php/public');

// Define Paths
define('APP_PATH', dirname(__DIR__)); // /path/to/automotive_api/app
define('PUBLIC_PATH', dirname(APP_PATH) . '/public'); // /path/to/automotive_api/public

// No need to define section-specific URLs here
// They will be handled in the routing

?>