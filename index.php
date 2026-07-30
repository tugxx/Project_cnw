<?php
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../storage/logs/app.log');
session_start();

define('ALLOW_ACCESS', true);

$config = require_once __DIR__ . '/config/app_config.php';
require_once __DIR__ . '/src/DB.php';
require_once __DIR__ . '/src/helpers/tai-khoan-helper.php';
require_once __DIR__ . '/src/helpers/hoc-phan-helper.php'; 
require_once __DIR__ . '/src/helpers/url-helper.php'; 
require_once __DIR__ . '/src/helpers/label-helper.php'; 
require_once __DIR__ . '/src/libs/SimpleXLSX.php';

require_once __DIR__ . '/views/components/pop-up.php';
require_once __DIR__ . '/views/components/input.php';
require_once __DIR__ . '/views/components/alert.php';
require_once __DIR__ . '/views/components/button.php';
require_once __DIR__ . '/views/components/card.php';
require_once __DIR__ . '/views/components/badge.php';
require_once __DIR__.'/views/components/course-card.php';

DB::getConnection($config);

$page = trim($_GET['page'] ?? 'trang-chu', '/');
$routes = [
    // tai-khoan
    'dang-nhap'         => __DIR__ . '/src/Controllers/tai-khoan/dang-nhap.php',
    'dang-xuat'         => __DIR__ . '/src/Controllers/tai-khoan/dang-xuat.php',
    'doi-mat-khau'      => __DIR__ . '/src/Controllers/tai-khoan/doi-mat-khau.php',
    'quen-mat-khau'     => __DIR__ . '/src/Controllers/tai-khoan/quen-mat-khau.php',
    'dat-lai-mat-khau'  => __DIR__ . '/src/Controllers/tai-khoan/dat-lai-mat-khau.php',
    'ho-so-ca-nhan'     => __DIR__ . '/src/Controllers/tai-khoan/ho-so-ca-nhan.php',
    'chinh-sua-ho-so'       => __DIR__ . '/src/Controllers/tai-khoan/chinh-sua-ho-so.php',
    'them-tai-khoan'        => __DIR__ . '/src/Controllers/tai-khoan/them-tai-khoan.php',
    'sua-tai-khoan'         => __DIR__ . '/src/Controllers/tai-khoan/sua-tai-khoan.php',
    'import-tai-khoan'      => __DIR__ . '/src/Controllers/tai-khoan/import-tai-khoan.php',
    'danh-sach-tai-khoan'   => __DIR__ . '/src/Controllers/tai-khoan/danh-sach-tai-khoan.php',

    // lop-hoc-phan
    'tao-lop-hoc-phan'   => __DIR__ . '/src/Controllers/lop-hoc-phan/tao-lop-hoc-phan.php',
    'danh-sach-lop-hoc-phan' => __DIR__ . '/src/Controllers/lop-hoc-phan/danh-sach-lop-hoc-phan.php',

    // them moi
    'tao-hoc-phan' => __DIR__.'/src/Controllers/hoc-phan/tao-hoc-phan.php',
    'danh-sach-hoc-phan' =>__DIR__.'/src/Controllers/hoc-phan/danh-sach-hoc-phan.php',

    //
    'trang-chu'         => __DIR__ . '/src/Controllers/trang-chu.php',
];

if (array_key_exists($page, $routes) && file_exists($routes[$page])) {
    require_once $routes[$page];
} else {
    header("HTTP/1.1 404 Not Found");
    require_once __DIR__ . '/views/404.php';
}

?>