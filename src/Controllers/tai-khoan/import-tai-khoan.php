<?php
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

if (!isset($_SESSION['user'])) {
    header('Location: dang-nhap');
    exit;
}

use Shuchkin\SimpleXLSX;

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
    if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Bạn chưa chọn file';
    } elseif ($_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Quá trình tải file bị lỗi';
    } else {
        $fileTmpPath = $_FILES['excel_file']['tmp_name'];
        $fileName    = $_FILES['excel_file']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, ['xlsx', 'xls'])) {
            $errors[] = 'Chỉ chấp nhận file định dạng Excel (.xlsx, .xls).';
        } else {
            if ($xlsx = SimpleXLSX::parse($fileTmpPath)) {
                DB::beginTransaction();
                try {
                    $insertedCount = 0;
                    $today = date('Y-m-d');
                    $lineNumber = 0;

                    foreach ($xlsx->rows() as $rowIndex => $row) {
                        /** @var array $row */
                        if ($rowIndex === 0 || empty(array_filter($row))) {
                            continue;
                        }

                        $lineNumber = $rowIndex + 1;

                        $code = trim($row[0] ?? '');
                        $email = trim($row[1] ?? '');
                        $username = $code;
                        $fullName = trim($row[2] ?? '');
                        $dob = !empty($row[3]) ? trim($row[3]) : null; 
                        $class = trim($row[4] ?? '');
                        $role = !empty($row[5]) ? strtolower(trim($row[5])) : 'student';

                        if (empty($code)) {
                            throw new Exception("Dòng {$lineNumber}: Mã không được để trống.");
                        } elseif (!ctype_digit($code)) {
                            throw new Exception("Dòng {$lineNumber}: Mã '{$code}' không hợp lệ (Chỉ được là số).");
                        }

                        if (empty($email)) {
                            throw new Exception("Dòng {$lineNumber}: Email không được để trống.");
                        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            throw new Exception("Dòng {$lineNumber}: Email '{$email}' không hợp lệ.");
                        }

                        if (empty($fullName)) {
                            throw new Exception("Dòng {$lineNumber}: Họ và tên không được để trống.");
                        }

                        $allowedRoles = ['student', 'teacher'];
                        if (!in_array($role, $allowedRoles, true)) {
                            throw new Exception("Dòng {$lineNumber}: Vai trò '{$role}' không hợp lệ.");
                        }

                        if (!empty($dob) && strtotime($dob) > strtotime($today)) {
                            throw new Exception("Dòng {$lineNumber}: Ngày sinh '{$dob}' không được sau ngày hiện tại.");
                        }

                        $sql = "SELECT `id` 
                                FROM `users` 
                                WHERE `user_code` = ?";
                        $existingCode = DB::fetchOne($sql, [$code]);
                        if ($existingCode) {
                            throw new Exception("Dòng {$lineNumber}: Mã '{$code}' đã tồn tại trên hệ thống.");
                        }

                        $sql = "SELECT `id` 
                                FROM `users` 
                                WHERE `email` = ?";
                        $existingEmail = DB::fetchOne($sql, [$email]);
                        if ($existingEmail) {
                            throw new Exception("Dòng {$lineNumber}: Email '{$email}' đã tồn tại trên hệ thống.");
                        }

                        $sql = "SELECT id 
                                FROM users 
                                WHERE username = ?";
                        $existingUsername = DB::fetchOne($sql, [$username]);
                        if ($existingUsername) {
                            throw new Exception("Dòng {$lineNumber}: Tên đăng nhập '{$username}' đã tồn tại trên hệ thống.");
                        }

                        $hashedPassword = password_hash("1", PASSWORD_DEFAULT);
                        $isActive = 1;
                        $sqlInsert = "INSERT INTO `users` (`user_code`, `username`, `email`, `password`, `role`, `full_name`, `class`, `dob`, `is_active`) 
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
                        $result = DB::execute($sqlInsert, $params);
                        if (!$result) {
                            throw new Exception("Dòng {$lineNumber}: Lỗi hệ thống, không thể lưu dữ liệu.");
                        }
                        $insertedCount++;
                    }

                    if ($insertedCount === 0) {
                        throw new Exception("File Excel không có dữ liệu để import.");
                    }

                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    $errors[] = $e->getMessage();
                }

                showPopUp("Import thành công {$insertedCount} tài khoản!", 'danh-sach-tai-khoan', 'success');
            } else {
                $errors[] = 'Không thể đọc file Excel. Vui lòng kiểm tra lại cấu trúc file: ' . SimpleXLSX::parseError();
            }
        }
    }
}

require_once __DIR__ . '/../../../views/layouts/header-admin.php';
require_once __DIR__ . '/../../../views/tai-khoan/import-tai-khoan.php';