<?php
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

if (!isset($_SESSION['user']['id'])) {
    header('Location: dang-nhap');
    exit;
}

$userId = $_SESSION['user']['id'];
$sql = "SELECT `id`, `full_name`, `dob`, `avatar`, `is_active`, `email`, `role`
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


$targetUserId = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$targetUserId) {
    showPopUp('Không tìm thấy tài khoản cần sửa.', 'danh-sach-tai-khoan', 'error');
}

$sql = "SELECT * 
        FROM users 
        WHERE id = ?";
$targetUser = DB::fetchOne($sql, [$targetUserId]);
if (!$targetUser) {
    showPopUp('Tài khoản người dùng không tồn tại.', 'danh-sach-tai-khoan', 'error');
}

if ($targetUser['role'] === 'admin') {
    showPopUp('Bạn không có quyền chỉnh sửa tài khoản Admin.', 'danh-sach-tai-khoan', 'error');
}


$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $dob      = $_POST['dob'] ?? '';
    $class    = trim($_POST['class'] ?? '');
    $role     = $_POST['role'] ?? '';
    $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

    if (empty($email)) {
        $errors['email'] = 'Email không được để trống.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email không đúng định dạng.';
    }

    if (empty($username)) {
        $errors['username'] = 'Tên đăng nhập không được để trống.';
    }

    if (empty($fullName)) {
        $errors['full_name'] = 'Họ và tên không được để trống.';
    }

    if ($role === 'admin') {
        $errors['role'] = 'Vai trò sửa không được là admin.';
    }

    if (!empty($dob)) {
        $currentDate = date('Y-m-d');
        if ($dob > $currentDate) {
            $errors['dob'] = 'Ngày sinh không được sau thời điểm hiện tại.';
        }
    }

    if (empty($errors)) {
        $sql = "SELECT id 
                FROM users 
                WHERE email = ? AND id != ?";
        $existingEmail = DB::fetchOne($sql, [$email, $targetUserId]);
        if ($existingEmail) {
            $errors[] = 'Email đã tồn tại trên hệ thống.';
        }

        $sql = "SELECT id 
                FROM users
                WHERE username = ? AND id != ?";
        $existingUsername = DB::fetchOne($sql, [$username, $targetUserId]);
        if ($existingUsername) {
            $errors[] = 'Tên đăng nhập đã tồn tại trên hệ thống.';
        }

        if (empty($errors)) {
            try {
                $dobValue = !empty($dob) ? $dob : null;
                $classValue = !empty($class) ? $class : null;

                if (!empty($password)) {
                    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                    $sql = "UPDATE `users` 
                            SET `username` = ?, `email` = ?, `password` = ?, `role` = ?, `full_name` = ?, `class` = ?, `dob` = ?, `is_active` = ? 
                            WHERE `id` = ?";
                    $params = [$username, $email, $passwordHash, $role, $fullName, $classValue, $dobValue, $isActive, $targetUserId];
                } else {
                    $sql = "UPDATE `users` 
                            SET `username` = ?, `email` = ?, `role` = ?, `full_name` = ?, `class` = ?, `dob` = ?, `is_active` = ?
                            WHERE `id` = ?";
                    $params = [$username, $email, $role, $fullName, $classValue, $dobValue, $isActive, $targetUserId];
                }

                DB::execute($sql, $params);
                header('Location: /Project_cnw/danh-sach-tai-khoan');
                exit;
            } catch (PDOException $e) {
                $errors[] = 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu.';
                error_log($e->getMessage()); 
            }
        }
    }
}

require_once __DIR__ . '/../../../views/tai-khoan/sua-tai-khoan.php';