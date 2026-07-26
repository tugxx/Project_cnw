<?php
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

$errors  = [];
define('RESET_TOKEN_TTL', 15 * 60);
$token = $_GET['token'] ?? ($_POST['token'] ?? '');

$sql = "SELECT rt.email, rt.token, rt.created_at, u.is_active
        FROM `reset_tokens` rt
        JOIN `users` u ON u.email = rt.email
        WHERE rt.token = ?
        LIMIT 1";
$resetRecord = DB::fetchOne($sql, [$token]);

// if (!$resetRecord || (strtotime($resetRecord['created_at']) + RESET_TOKEN_TTL) < time()) {
//     showPopUp('Liên kết khôi phục mật khẩu không hợp lệ hoặc đã hết hạn.', 'dang-nhap', 'error');
// }

// if (!$resetRecord['is_active']) {
//     showPopUp('Tài khoản của bạn đang bị khóa.', 'dang-nhap', 'error');
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
    $newPassword     = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if ($newPassword == "" || $confirmPassword == "") {
        $errors[] = 'Phải nhập đẩy đủ mật khẩu và xác nhận.';
    }

    if ($newPassword !== $confirmPassword) {
        $errors[] = 'Xác nhận mật khẩu không khớp.';
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE `users` 
                SET `password` = ?
                WHERE `email` = ?";
        DB::execute($sql, [$hashedPassword, $resetRecord['email']]);

        $sql = "UPDATE `reset_tokens` 
                SET `token` = 'USED'
                WHERE `email` = ?";
        DB::execute($sql, [$resetRecord['email']]);
        forceLogout();
        showPopUp('Đặt lại mật khẩu thành công.', 'dang-nhap', 'success');
    }
}

require_once __DIR__ . '/../../../views/tai-khoan/dat-lai-mat-khau.php';