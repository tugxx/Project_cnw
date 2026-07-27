<?php
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

require_once __DIR__ . '/../../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

global $config;
$errors  = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Vui lòng nhập địa chỉ email hợp lệ.';
    }

    if (empty($errors)) {
        $sql = "SELECT id, username, email, is_active, `full_name`
                FROM users 
                WHERE email = ? 
                LIMIT 1";
        $user = DB::fetchOne($sql, [$email]);

        if ($user) {
            if (!$user['is_active']) {
                $errors[] = 'Tài khoản đang bị khóa.';
            } else {
                $token = bin2hex(random_bytes(32));
                $sql = "INSERT INTO `reset_tokens` (`email`, `token`)
                        VALUES (?, ?)
                        ON DUPLICATE KEY UPDATE `token` = VALUES(`token`), `created_at` = CURRENT_TIMESTAMP";
                DB::execute($sql, [$email, $token]);

                $resetLink = rtrim($config['APP_URL'] ?? '', '/')
                    . '/dat-lai-mat-khau&token=' . urlencode($token);

                guiEmailKhoiPhucMatKhau($email, $user['full_name'], $resetLink, $config);
            }
        }

        if (empty($errors)) {
            $success = 'Nếu email tồn tại trong hệ thống, hướng dẫn khôi phục mật khẩu đã được gửi vào hộp thư của bạn.';
        }
    }
}


function guiEmailKhoiPhucMatKhau($email, $fullName, $resetLink, $config)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $config['MAIL_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['MAIL_USER'];
        $mail->Password   = $config['MAIL_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config['MAIL_PORT'];

        $mail->setFrom($config['MAIL_USER'], 'Hệ thống');
        $mail->addAddress($email, $fullName);
        $mail->Subject = 'Yêu cầu khôi phục mật khẩu';
        $mail->Body = "Xin chào {$fullName},\n\n"
            . "Nhấn vào liên kết sau để đặt lại mật khẩu (hiệu lực trong 15 phút):\n"
            . "{$resetLink}\n\n"
            . "Nếu bạn không yêu cầu việc này, vui lòng bỏ qua email.";
        $mail->send();
    } catch (Exception $e) {
        error_log("Gửi email thất bại: {$mail->ErrorInfo}");
    }
}

require_once __DIR__ . '/../../../views/tai-khoan/quen-mat-khau.php';