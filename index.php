<?php
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../storage/logs/app.log');
session_start();

define('ALLOW_ACCESS', true);

$config = require_once __DIR__ . '/config/app_config.php';
require_once __DIR__ . '/src/DB.php';

DB::getConnection($config);
$page = $_GET['page'] ?? 'trang-chu';
$routes = [
    'trang-chu'  => __DIR__ . '/src/Controllers/trang-chu.php',
    'dang-nhap'  => __DIR__ . '/src/Controllers/tai-khoan/dang-nhap.php',
];

if (array_key_exists($page, $routes) && file_exists($routes[$page])) {
    require_once $routes[$page];
} else {
    header("HTTP/1.1 404 Not Found");
    require_once __DIR__ . '/views/404.php';
}

?>