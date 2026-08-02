<?php

if (!isLoggedIn()) {
    header("Location: dang-nhap");
    exit;
}

$userId = $_SESSION['user']['id'];

if (!isUserActive($userId)) {
    session_destroy();
    header("Location: dang-nhap");
    exit;
}

if ($_SESSION['user']['role'] != 'lecturer') {
    die("Chỉ giảng viên mới được tạo đề tài.");
}

/*
--------------------------------------------------
Lấy học phần
--------------------------------------------------
*/

$courseId = (int)($_GET['courseId'] ?? $_POST['courseId'] ?? 0);

if ($courseId <= 0) {
    die("Học phần không tồn tại.");
}

/*
--------------------------------------------------
Kiểm tra giảng viên phụ trách học phần
--------------------------------------------------
*/

$sql = "
SELECT c.id,c.course_name
FROM courses c
INNER JOIN courses_lecturers cl
ON c.id = cl.course_id
WHERE c.id = ?
AND cl.lecturer_id = ?
";

$course = DB::fetchOne($sql, [
    $courseId,
    $userId
]);

if (!$course) {
    die("Bạn không được quản lý học phần này.");
}

$errors = [];

$topicName = '';
$description = '';
$status = 'public';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $topicName = trim($_POST['topic_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'public';

    if ($topicName == '') {
        $errors[] = "Tên đề tài không được để trống.";
    }

    /*
    ----------------------------------------
    Kiểm tra trùng tên trong cùng học phần
    ----------------------------------------
    */

    $sql = "
    SELECT ct.id
    FROM courses_topics ct
    INNER JOIN topics t
    ON ct.topic_id=t.id
    WHERE ct.course_id=?
    AND t.topic_name=?
    AND t.deleted_at IS NULL
    LIMIT 1
    ";

    if (DB::fetchOne($sql, [$courseId, $topicName])) {
        $errors[] = "Tên đề tài đã tồn tại.";
    }

    if (empty($errors)) {

        try {

            DB::beginTransaction();

            /*
            ------------------------------------
            Thêm vào topics
            ------------------------------------
            */

            $sql = "
            INSERT INTO topics
            (
                topic_name,
                description,
                created_by,
                status
            )
            VALUES
            (
                ?,?,?,?
            )
            ";

            $topicId = DB::insert($sql, [
                $topicName,
                $description,
                $userId,
                $status
            ]);

            /*
            ------------------------------------
            Liên kết học phần
            ------------------------------------
            */

            $sql = "
            INSERT INTO courses_topics
            (
                course_id,
                topic_id
            )
            VALUES
            (
                ?,?
            )
            ";

            DB::execute($sql, [
                $courseId,
                $topicId
            ]);

            DB::commit();

            header("Location:index.php?page=danh-sach-de-tai&courseId=".$courseId);
            exit;

        } catch(Exception $e) {

            DB::rollBack();

            $errors[] = $e->getMessage();
        }

    }

}

require_once __DIR__.'/../../../views/de-tai/tao-de-tai.php';