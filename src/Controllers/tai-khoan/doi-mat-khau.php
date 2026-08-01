<?php
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

if (!isset($_SESSION['user'])) {
    header('Location: /Project_cnw/dang-nhap');
    exit;
}

$errors = [];
$success = "";
$userId = $_SESSION['user']['id'];
$sql = "SELECT `id`, `password`, `is_active`, `role` 
        FROM `users` 
        WHERE `id` = ?";
$user = DB::fetchOne($sql, [$userId]);
if (!$user || !$user["is_active"]) {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không tồn tại hoặc đã bị khoá.', 'dang-nhap', 'error');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPassword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? ''; 

    if ($oldPassword === '' || $newPassword === '' || $confirmPassword === '') {
        $errors[] = 'Vui lòng nhập đầy đủ thông tin.';
    }

    if (empty($errors) && !password_verify($oldPassword, $user['password'])) {
        $errors[] = 'Mật khẩu cũ không đúng.';
    }

    if (empty($errors) && $newPassword !== $confirmPassword) {
        $errors[] = 'Mật khẩu mới và mật khẩu xác nhận không khớp.';
    }

    if (empty($errors) && password_verify($newPassword, $user['password'])) {
        $errors[] = 'Mật khẩu mới không được trùng với mật khẩu cũ.';
    }

    if (empty($errors)) {
        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE `users` 
                SET `password` = ? 
                WHERE `id` = ?";
        $updated = DB::execute($sql, [$newHashedPassword, $userId]);

        if ($updated) {
            $success = 'Đổi mật khẩu thành công.';
        } else {
            $errors[] = 'Có lỗi xảy ra khi cập nhật mật khẩu, vui lòng thử lại.';
        }
        
    }
} 

if ($user['role'] === 'admin') {
    require_once __DIR__ . '/../../../views/layouts/header.php';
} elseif ($user['role'] === 'lecturer') {
    require_once __DIR__ . '/../../../views/layouts/header-lecturer.php';
} else {
    require_once __DIR__ . '/../../../views/layouts/header-student.php';
}
require_once __DIR__ . '/../../../views/tai-khoan/doi-mat-khau.php';