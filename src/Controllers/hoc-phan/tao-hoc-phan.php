<?php

if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

if (!isLoggedIn()) {
    header('Location: dang-nhap');
    exit;
}

$userId = $_SESSION['user']['id'];

if (!isUserActive($userId)) {
    session_destroy();
    header('Location: dang-nhap');
    exit;
}

if (!isAdmin()) {
    header('Location: dang-nhap');
    exit;
}

$errors = [];
$success = '';
$course_name = '';
$description = '';
$sql = "
    SELECT
        id,
        username,
        full_name
    FROM users
    WHERE role = 'lecturer'
      AND is_active = 1
    ORDER BY full_name ASC
";

$lecturers = DB::fetchAll($sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $course_name = trim($_POST['course_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $selectedLecturers = $_POST['lecturers'] ?? [];

    if (empty($course_name)) {
        $errors['course_name'] = "Tên học phần không được để trống.";
    } 

    if (courseNameExists($course_name)) {
        $errors['course_name'] = "Tên học phần đã tồn tại.";
    } 

    if (empty($selectedLecturers)) {
        $errors['lecturers'] = "Vui lòng chọn ít nhất một giảng viên.";
    } 

    if (empty($errors)) {

        $pdo = DB::getConnection();

        try {

            $pdo->beginTransaction();

            $course_code = generateCourseCode();

            $sql = "
                INSERT INTO courses(course_code, course_name, description)
                VALUES (?, ?, ?)
            ";

            DB::execute($sql, [
                $course_code,
                $course_name,
                $description
            ]);

            $courseId = DB::lastInsertId();

            foreach ($selectedLecturers as $lecturerId) {

                $sql = "
                    INSERT INTO courses_lecturers(course_id, lecturer_id)
                    VALUES (?, ?)
                ";

                DB::execute($sql, [
                    $courseId,
                    $lecturerId
                ]);
            }

            $pdo->commit();
            $course_name = '';
            $description = '';
            $selectedLecturers = [];

            header("Location:index.php?page=danh-sach-hoc-phan");
            exit;

            

        } catch (Exception $e) {

            $pdo->rollBack();

            $errors['system'] = $e->getMessage();
        }
    }

}

require_once __DIR__ . '/../../../views/layouts/header-admin.php';
require_once __DIR__ . '/../../../views/hoc-phan/tao-hoc-phan.php';
?>