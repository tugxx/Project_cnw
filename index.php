<?php
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/storage/logs/app.log');
session_start();

define('ALLOW_ACCESS', true);

$config = require_once __DIR__ . '/config/app_config.php';
require_once __DIR__ . '/src/DB.php';
require_once __DIR__ . '/src/helpers/tai-khoan-helper.php';
require_once __DIR__ . '/src/helpers/hoc-phan-helper.php'; 
require_once __DIR__ . '/src/helpers/url-helper.php'; 
require_once __DIR__ . '/src/helpers/label-helper.php';
require_once __DIR__ . '/src/helpers/dot-dang-ky-helper.php'; 
require_once __DIR__ . '/src/helpers/log-helper.php';

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
    'dang-nhap'             => __DIR__ . '/src/Controllers/tai-khoan/dang-nhap.php',
    'dang-xuat'             => __DIR__ . '/src/Controllers/tai-khoan/dang-xuat.php',
    'doi-mat-khau'          => __DIR__ . '/src/Controllers/tai-khoan/doi-mat-khau.php',
    'quen-mat-khau'         => __DIR__ . '/src/Controllers/tai-khoan/quen-mat-khau.php',
    'dat-lai-mat-khau'      => __DIR__ . '/src/Controllers/tai-khoan/dat-lai-mat-khau.php',
    'ho-so-ca-nhan'         => __DIR__ . '/src/Controllers/tai-khoan/ho-so-ca-nhan.php',
    'chinh-sua-ho-so'       => __DIR__ . '/src/Controllers/tai-khoan/chinh-sua-ho-so.php',
    'them-tai-khoan'        => __DIR__ . '/src/Controllers/tai-khoan/them-tai-khoan.php',
    'sua-tai-khoan'         => __DIR__ . '/src/Controllers/tai-khoan/sua-tai-khoan.php',
    'import-tai-khoan'      => __DIR__ . '/src/Controllers/tai-khoan/import-tai-khoan.php',
    'danh-sach-tai-khoan'   => __DIR__ . '/src/Controllers/tai-khoan/danh-sach-tai-khoan.php',

    // hoc-phan
    'tao-hoc-phan'          => __DIR__.'/src/Controllers/hoc-phan/tao-hoc-phan.php',
    'danh-sach-hoc-phan'    => __DIR__.'/src/Controllers/hoc-phan/danh-sach-hoc-phan.php',
    'chi-tiet-hoc-phan'     => __DIR__.'/src/Controllers/hoc-phan/chi-tiet-hoc-phan.php',

    // lop-hoc-phan
    'tao-lop-hoc-phan'      => __DIR__ . '/src/Controllers/lop-hoc-phan/tao-lop-hoc-phan.php',
    'danh-sach-lop-hoc-phan'=> __DIR__ . '/src/Controllers/lop-hoc-phan/danh-sach-lop-hoc-phan.php',
    'sua-lop-hoc-phan'      => __DIR__ . '/src/Controllers/lop-hoc-phan/sua-lop-hoc-phan.php',

    // dot-dang-ky-lop-hoc-phan
    'sua-dot-dang-ky-lop-hoc-phan'  => __DIR__ . '/src/Controllers/dot-dang-ky-lop-hoc-phan/sua-dot-dang-ky-lop-hoc-phan.php',
    'danh-sach-dot-dang-ky-hoc-phan'    => __DIR__ . '/src/Controllers/dot-dang-ky-lop-hoc-phan/danh-sach-dot-dang-ky-hoc-phan.php',
    'danh-sach-dot-dang-ky-lop-hoc-phan'    => __DIR__ . '/src/Controllers/dot-dang-ky-lop-hoc-phan/danh-sach-dot-dang-ky-lop-hoc-phan.php',
    'chi-tiet-dot-dang-ky-lop-hoc-phan'    => __DIR__ . '/src/Controllers/dot-dang-ky-lop-hoc-phan/chi-tiet-dot-dang-ky-lop-hoc-phan.php',
    'xoa-dot-dang-ky-lop-hoc-phan'    => __DIR__ . '/src/Controllers/dot-dang-ky-lop-hoc-phan/xoa-dot-dang-ky-lop-hoc-phan.php',

    // nhom
    'tao-nhom'              => __DIR__ . '/src/Controllers/nhom/tao-nhom.php',
    'danh-sach-nhom'        => __DIR__ . '/src/Controllers/nhom/danh-sach-nhom.php',
    'chi-tiet-nhom-sinh-vien' => __DIR__ . '/src/Controllers/nhom/chi-tiet-nhom-sinh-vien.php',

    // de tai 
    'dang-ky-de-tai' => __DIR__.'/src/Controllers/de-tai/dang-ky-de-tai.php',
    'tao-de-tai' => __DIR__.'/src/Controllers/de-tai/tao-de-tai.php',
    'sua-de-tai' => __DIR__.'/src/Controllers/de-tai/sua-de-tai.php',
    'xoa-de-tai' => __DIR__.'/src/Controllers/de-tai/xoa-de-tai.php',
    'sua-dang-ky-de-tai'=> __DIR__.'/src/Controllers/de-tai/sua-dang-ky-de-tai.php',
    'danh-sach-de-tai' => __DIR__.'/src/Controllers/de-tai/danh-sach-de-tai.php',
    'huy-dang-ky-de-tai'=> __DIR__.'/src/Controllers/de-tai/huy-dang-ky-de-tai.php',

    'danh-sach-de-tai-sinh-vien' => __DIR__.'/src/Controllers/de-tai/danh-sach-de-tai-sinh-vien.php',

    // them dot dang ki
    'tao-dot-dang-ky'       => __DIR__.'/src/Controllers/dot-dang-ky/tao-dot-dang-ky.php',
    'danh-sach-dot-dang-ky' => __DIR__.'/src/Controllers/dot-dang-ky/danh-sach-dot-dang-ky.php',
    'sua-dot-dang-ky'       => __DIR__.'/src/Controllers/dot-dang-ky/sua-dot-dang-ky.php',
    'xoa-dot-dang-ky'       => __DIR__.'/src/Controllers/dot-dang-ky/xoa-dot-dang-ky.php',

    //
    'trang-chu'             => __DIR__ . '/src/Controllers/trang-chu.php',
];

if (array_key_exists($page, $routes) && file_exists($routes[$page])) {
    require_once $routes[$page];
} else {
    header("HTTP/1.1 404 Not Found");
    require_once __DIR__ . '/views/404.php';
}
?>