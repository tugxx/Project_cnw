<?php
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

if (!isset($_SESSION['user'])) {
    header('Location: dang-nhap');
    exit;
}

$userId = $_SESSION['user']['id'];
$sql = "SELECT `id`, `email`, `full_name`, `dob`, `class`, `role`, `avatar`, `is_active`, `user_code`
        FROM users 
        WHERE id = ?";
$user = DB::fetchOne($sql, [$userId]);

if (!$user || !$user["is_active"]) {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không tồn tại hoặc đã bị khoá.', 'dang-nhap', 'error');
}

if ($user['role'] === 'admin') {
    require_once __DIR__ . '/../../../views/layouts/header-admin.php';
} elseif ($user['role'] === 'lecturer') {
    require_once __DIR__ . '/../../../views/layouts/header-lecturer.php';
} else {
    require_once __DIR__ . '/../../../views/layouts/header-student.php';
}
require_once __DIR__ . '/../../../views/tai-khoan/ho-so-ca-nhan.php';
?>