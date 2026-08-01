<?php
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

if (!isset($_SESSION['user'])) {
    header('Location: dang-nhap');
    exit;
}

$userId = $_SESSION['user']['id'];

$sql = "SELECT * 
        FROM users 
        WHERE id = ?";
$user = DB::fetchOne($sql, [$userId]);
if (!$user || !$user["is_active"]) {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không tồn tại hoặc đã bị khoá.', 'dang-nhap', 'error');
}

if ($user['role'] !== 'admin') {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không phải admin', 'dang-nhap', 'error');
}


$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['user_code'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $dob = $_POST['dob'] ?? null;
    $class = trim($_POST['class'] ?? '');
    $role = $_POST['role'] ?? 'student';

    if (empty($code)) {
        $errors[] = 'Chưa nhập mã.';
    } elseif (!ctype_digit($code)) {
        $errors[] = 'Mã không hợp lệ';
    }

    if (empty($email)) {
        $errors[] = 'Email không được để trống.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ.';
    }

    if (empty($username)) {
        $errors[] = 'Tên đăng nhập không được để trống.';
    }

    if (empty($password)) {
        $errors[] = 'Mật khẩu không được để trống.';
    }

    if (empty($fullName)) {
        $errors[] = 'Họ và tên không được để trống.';
    }

    if (empty($errors)) {
        $sql = "SELECT `id` 
                FROM `users` 
                WHERE `user_code` = ?";
        $existingCode = DB::fetchOne($sql, [$code]);
        if ($existingCode) {
            $errors[] = 'Mã đã tồn tại trên hệ thống.';
        }

        $sql = "SELECT `id` 
                FROM `users` 
                WHERE `email` = ?";
        $existingEmail = DB::fetchOne($sql, [$email]);
        if ($existingEmail) {
            $errors[] = 'Email đã tồn tại trên hệ thống.';
        }

        $sql = "SELECT `id` 
                FROM `users` 
                WHERE `username` = ?";
        $existingUsername = DB::fetchOne($sql, [$username]);
        if ($existingUsername) {
            $errors[] = 'Tên đăng nhập đã tồn tại trên hệ thống.';
        }

        $allowedRoles = ['student', 'lecturer'];
        if (!in_array($role, $allowedRoles, true)) {
            $errors[] = 'Vai trò không hợp lệ.';
        }

        if (empty($errors)) {
            try {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $isActive = 1;
                $createdAt = date('Y-m-d H:i:s');
                $sql = "INSERT INTO `users` (`user_code` , `username`, `email`, `password`, `role`, `full_name`, `class`, `dob`, `is_active`) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $params = [
                    $code,
                    $username,
                    $email,
                    $hashedPassword,
                    $role,
                    $fullName,
                    !empty($class) ? $class : null,
                    !empty($dob) ? $dob : null,
                    $isActive
                ];
                $result = DB::execute($sql, $params);
                header('Location: /Project_cnw/danh-sach-tai-khoan');
                exit;
            } catch (Exception $e) {
                $errors[] = 'Đã xảy ra lỗi trong quá trình lưu dữ liệu. Vui lòng thử lại.';
                error_log($e->getMessage()); 
            }     
        }
    }
}

require_once __DIR__ . '/../../../views/layouts/header-admin.php';
require_once __DIR__ . '/../../../views/tai-khoan/them-tai-khoan.php';