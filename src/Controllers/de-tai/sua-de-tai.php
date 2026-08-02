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

$topicId = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

if ($topicId <= 0) {
    die("Đề tài không tồn tại.");
}

/*
-------------------------------------------------------
Lấy đề tài
-------------------------------------------------------
*/

$sql = "
SELECT
    t.id,
    t.topic_name,
    t.description,
    t.status,
    t.created_by,
    ct.course_id
FROM topics t
INNER JOIN courses_topics ct
ON t.id = ct.topic_id
WHERE t.id = ?
AND t.deleted_at IS NULL
";

$topic = DB::fetchOne($sql, [$topicId]);

if (!$topic) {
    die("Đề tài không tồn tại.");
}


if (
    $_SESSION['user']['role'] != 'admin'
    &&
    $topic['created_by'] != $userId
) {
    die("Bạn không có quyền sửa đề tài này.");
}

$topicName = $topic['topic_name'];
$description = $topic['description'];
$status = $topic['status'];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $topicName = trim($_POST['topic_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'private';
    $courseId = $topic['course_id'];

    if ($topicName == '') {
        $errors[] = "Tên đề tài không được để trống.";
    }

    if ($description == '') {
        $errors[] = "Mô tả không được để trống.";
    }

    if (!in_array($status, ['public','private'])) {
        $errors[] = "Trạng thái không hợp lệ.";
    }

    if (empty($errors)) {

        $sql = "
        UPDATE topics
        SET
            topic_name=?,
            description=?,
            status=?
        WHERE id=?
        ";

        DB::execute($sql,[
            $topicName,
            $description,
            $status,
            $topicId
        ]);

        header("Location:index.php?page=danh-sach-de-tai&courseId=".$courseId);
        exit;
    }

}

require_once __DIR__.'/../../../views/de-tai/sua-de-tai.php';