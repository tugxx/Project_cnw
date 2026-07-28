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

                    foreach ($xlsx->rows() as $rowIndex => $rawRow) {
                        /** @var array $row */
                        if ($rowIndex === 0 || empty(array_filter($row))) {
                            continue;
                        }

                        $lineNumber = $rowIndex + 1;

                        $email    = trim($row[0] ?? '');
                        $username = trim($row[1] ?? '');
                        $password = trim($row[2] ?? '');
                        $fullName = trim($row[3] ?? '');
                        $dob      = !empty($row[4]) ? trim($row[4]) : null; 
                        $class    = trim($row[5] ?? '');
                        $role     = !empty($row[6]) ? strtolower(trim($row[6])) : 'student';

                        if (empty($email)) {
                            throw new Exception("Dòng {$lineNumber}: Email không được để trống.");
                        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            throw new Exception("Dòng {$lineNumber}: Email '{$email}' không hợp lệ.");
                        }

                        if (empty($username)) {
                            throw new Exception("Dòng {$lineNumber}: Tên đăng nhập không được để trống.");
                        }

                        if (empty($password)) {
                            throw new Exception("Dòng {$lineNumber}: Mật khẩu không được để trống.");
                        }

                        if (empty($fullName)) {
                            throw new Exception("Dòng {$lineNumber}: Họ và tên không được để trống.");
                        }

                        if ($role === 'admin') {
                            throw new Exception("Dòng {$lineNumber}: Không được nhập tài khoản với vai trò Admin.");
                        }

                        if (!empty($dob) && strtotime($dob) > strtotime($today)) {
                            throw new Exception("Dòng {$lineNumber}: Ngày sinh '{$dob}' không được sau ngày hiện tại.");
                        }

                        $sql = "SELECT id 
                                FROM users 
                                WHERE email = ?";
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

                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $isActive = 1;
                        $sqlInsert = "INSERT INTO `users` (`username`, `email`, `password`, `role`, `full_name`, `class`, `dob`, `is_active`) 
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
                    showPopUp("Import thành công {$insertedCount} tài khoản!", 'danh-sach-tai-khoan', 'success');
                } catch (Exception $e) {
                    DB::rollBack();
                    $errors[] = $e->getMessage();
                }
            } else {
                $errors[] = 'Không thể đọc file Excel. Vui lòng kiểm tra lại cấu trúc file: ' . SimpleXLSX::parseError();
            }
        }
    }
}

require_once __DIR__ . '/../../../views/layouts/header.php';
require_once __DIR__ . '/../../../views/tai-khoan/import-tai-khoan.php';