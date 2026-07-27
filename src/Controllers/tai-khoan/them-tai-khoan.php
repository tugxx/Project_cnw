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
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email     = trim($_POST['email'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = trim($_POST['password'] ?? '');
    $fullName  = trim($_POST['full_name'] ?? '');
    $dob       = $_POST['dob'] ?? null;
    $class     = trim($_POST['class'] ?? '');
    $role      = $_POST['role'] ?? 'student';

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
        $existingEmail = DB::fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existingEmail) {
            $errors[] = 'Email đã tồn tại trên hệ thống.';
        }

        $existingUsername = DB::fetchOne("SELECT id FROM users WHERE username = ?", [$username]);
        if ($existingUsername) {
            $errors[] = 'Tên đăng nhập đã tồn tại trên hệ thống.';
        }

        if ($role=="admin") {
            $errors[] = 'Không được tạo được admin.';
        }

        if (empty($errors)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $isActive = 1;
            $createdAt = date('Y-m-d H:i:s');
            $sql = "INSERT INTO `users` (`username`, `email`, `password`, `role`, `full_name`, `class`, `dob`, `is_active`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [
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

            if ($result) {
                $successMessage = 'Thêm tài khoản thành công!';
            } else {
                $errors[] = 'Đã xảy ra lỗi trong quá trình lưu dữ liệu. Vui lòng thử lại.';
            }
        }
    }
}

require_once __DIR__ . '/../../../views/layouts/header.php';
require_once __DIR__ . '/../../../views/tai-khoan/them-tai-khoan.php';