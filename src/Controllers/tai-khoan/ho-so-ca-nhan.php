<?php
if (!defined('ALLOW_ACCESS')) {
    exit('Truy cập trực tiếp không được phép.');
}

if (!isset($_SESSION['user'])) {
    header('Location: dang-nhap');
    exit;
}

$userId = $_SESSION['user']['id'];
$sql = "SELECT `id`, `email`, `name`, `dob`, `class`, `role`, `avatar`, `is_active` 
        FROM users 
        WHERE id = ?";
$user = DB::fetchOne($sql, [$userId]);

if (!$user || !$user["is_active"]) {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không tồn tại hoặc đã bị khoá.', 'dang-nhap', 'error');
}

require_once __DIR__ . '/../../../views/layouts/header.php';
require_once __DIR__ . '/../../../views/tai-khoan/ho-so-ca-nhan.php';
?>