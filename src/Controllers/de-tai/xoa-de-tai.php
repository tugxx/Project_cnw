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

$topicId = (int)($_GET['id'] ?? 0);

if ($topicId <= 0) {
    die("Đề tài không tồn tại.");
}

/*
---------------------------------------
Lấy đề tài
---------------------------------------
*/

$sql = "
SELECT
    id,
    created_by,
    deleted_at
FROM topics
WHERE id = ?
";

$topic = DB::fetchOne($sql, [$topicId]);

if (!$topic) {
    die("Đề tài không tồn tại.");
}

if ($topic['deleted_at'] != null) {
    die("Đề tài đã bị xóa.");
}

/*
---------------------------------------
Kiểm tra quyền
---------------------------------------
*/

if (
    $_SESSION['user']['role'] != 'admin'
    &&
    $topic['created_by'] != $userId
) {
    die("Bạn không có quyền xóa đề tài này.");
}

/*
---------------------------------------
Không cho xóa nếu đã áp dụng
---------------------------------------
*/

$sql = "
SELECT id
FROM sections_sessions_topics
WHERE topic_id = ?
LIMIT 1
";

$used = DB::fetchOne($sql, [$topicId]);

if ($used) {
    die("Đề tài đã được áp dụng cho đợt đăng ký nên không thể xóa.");
}

/*
---------------------------------------
Xóa mềm
---------------------------------------
*/

$sql = "
UPDATE topics
SET deleted_at = NOW()
WHERE id = ?
";

DB::execute($sql, [$topicId]);

header("Location:index.php?page=danh-sach-de-tai");
exit;