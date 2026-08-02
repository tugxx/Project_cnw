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

if (!$user || !$user['is_active']) {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không tồn tại hoặc đã bị khoá.', 'dang-nhap', 'error');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $currentAvatar = $user['avatar'];
    $newAvatarPath = $currentAvatar;

    if (empty($fullName)) {
        $error = 'Họ và tên không được để trống.';
    }

    if (empty($error) && !empty($dob)) {
        $today = date('Y-m-d');
        $d = DateTime::createFromFormat('Y-m-d', $dob);
        
        if (!($d && $d->format('Y-m-d') === $dob)) {
            $error = 'Định dạng ngày sinh không hợp lệ.';
        } elseif ($dob > $today) {
            $error = 'Ngày sinh không thể lớn hơn ngày hiện tại.';
        }
    }

    if (empty($error)) {
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $allowedExtensions)) {
                $error = 'Chỉ chấp nhận các định dạng ảnh: JPG, JPEG, PNG, WEBP.';
            } elseif ($file['size'] > 2 * 1024 * 1024) { 
                $error = 'Dung lượng ảnh tối đa là 2MB.';
            } else {
                $fileName = 'avatar_' . $userId . '_' . time() . '.' . $fileExtension;
                $uploadDir = __DIR__ . '/../../../storage/uploads/avatars/';
                $targetPath = $uploadDir . $fileName;
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    if (!empty($currentAvatar) && file_exists($uploadDir . $currentAvatar)) {
                        @unlink($uploadDir . $currentAvatar);
                    }
                    $newAvatarPath = $fileName;
                } else {
                    $error = 'Lỗi trong quá trình lưu tệp ảnh.';
                }
            }
        }
    }

    if (empty($error)) {
        $sql = "UPDATE users 
                        SET `full_name` = ?, `dob` = ?, `avatar` = ? 
                        WHERE id = ?";
        $updated = DB::execute($sql, [$fullName, !empty($dob) ? $dob : null, $newAvatarPath, $userId]);

        if ($updated) {
            $_SESSION['user']['full_name'] = $fullName;
            header("Location: /Project_cnw/ho-so-ca-nhan");
            exit;
        } else {
            $error = 'Cập nhật thất bại. Vui lòng thử lại sau.';
        }
    }
}

if ($user['role'] === 'admin') {
    require_once __DIR__ . '/../../../views/layouts/header-admin.php';
} elseif ($user['role'] === 'lecturer') {
    require_once __DIR__ . '/../../../views/layouts/header-lecturer.php';
} else {
    require_once __DIR__ . '/../../../views/layouts/header-student.php';
}

require_once __DIR__ . '/../../../views/tai-khoan/chinh-sua-ho-so.php';
