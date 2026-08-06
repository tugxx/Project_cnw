<?php 
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

if (isset($_SESSION['user'])) {
    header('Location: /Project_cnw');
    exit;
}

$errors = []; 
$loginInput = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginInput = trim($_POST['login_input'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($loginInput)) {
        $errors['login_input'] = 'Vui lòng nhập Email hoặc Tên đăng nhập.';
    }

    if (empty($password)) {
        $errors['password'] = 'Vui lòng nhập mật khẩu.';
    }

    if (empty($errors)) {
        try {
            if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
                $loginType = 'email';
            } else {
                $loginType = 'username';
            }

            $sql = "SELECT `id`, `username`, `email`, `password`, `role`, `is_active`, `full_name`, `class`
                    FROM `users` 
                    WHERE `{$loginType}` = ? 
                    LIMIT 1";
            $user = DB::fetchOne($sql, [$loginInput]);

            if (!$user || !password_verify($password, $user['password'])) {
                $errors['auth'] = 'Tên đăng nhập hoặc mật khẩu không chính xác.';
            } elseif (!$user['is_active']) {
                $errors['auth'] = 'Tài khoản của bạn đã bị khóa.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'role' => $user['role'],
                    'full_name' => $user['full_name'],
                    'class' => $user['class'],
                    'code' => $user['user_code'],
                    'avatar' => $user['avatar'] ?? "/Project_cnw/assets/media/default-avatar.jpg"
                ];
                header('Location: /Project_cnw');
                exit;
            }
        } catch (PDOException $e) {
            $errors['system'] = 'Lỗi hệ thống, vui lòng thử lại sau.';
            error_log($e->getMessage());
        }
    }
} 

// echo password_hash('1', PASSWORD_DEFAULT);

require_once __DIR__ . '/../../../views/tai-khoan/dang-nhap.php';
?>