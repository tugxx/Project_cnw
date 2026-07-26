<?php
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../storage/logs/app.log');
session_start();

define('ALLOW_ACCESS', true);

$config = require_once __DIR__ . '/config/app_config.php';
require_once __DIR__ . '/src/DB.php';
require_once __DIR__ . '/src/helpers/tai-khoan-helper.php'; 
require_once __DIR__ . '/views/components/pop-up.php';

DB::getConnection($config);

$page = $_GET['page'] ?? 'dang-nhap';
$routes = [
    'trang-chu'         => __DIR__ . '/src/Controllers/trang-chu.php',
    'dang-nhap'         => __DIR__ . '/src/Controllers/tai-khoan/dang-nhap.php',
    'dang-xuat'         => __DIR__ . '/src/Controllers/tai-khoan/dang-xuat.php',
    'doi-mat-khau'      => __DIR__ . '/src/Controllers/tai-khoan/doi-mat-khau.php',
    'quen-mat-khau'     => __DIR__ . '/src/Controllers/tai-khoan/quen-mat-khau.php',
    'dat-lai-mat-khau'  => __DIR__ . '/src/Controllers/tai-khoan/dat-lai-mat-khau.php',
];

if (array_key_exists($page, $routes) && file_exists($routes[$page])) {
    require_once $routes[$page];
} else {
    header("HTTP/1.1 404 Not Found");
    require_once __DIR__ . '/views/404.php';
}

?>