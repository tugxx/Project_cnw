<?php

if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

if (!isLoggedIn()) {
    header("Location: dang-nhap");
    exit;
}

$userId = $_SESSION['user']['id'];

if (!isUserActive($userId)) {
    destroyUserSession();
    header("Location: dang-nhap");
    exit;
}

if ($_SESSION['user']['role'] != 'student') {
    die("Chỉ sinh viên mới được thực hiện chức năng này.");
}

$groupId = (int)($_GET['groupId'] ?? $_POST['groupId'] ?? 0);

if ($groupId <= 0) {
    die("Nhóm không tồn tại.");
}

/*
-------------------------------------------------------
Kiểm tra leader và lấy thông tin nhóm
-------------------------------------------------------
*/

$sql = "
SELECT
    g.id,
    g.section_session_id,
    g.section_session_topic_id,
    gm.role
FROM groups g
INNER JOIN group_members gm
    ON g.id = gm.group_id
WHERE g.id = ?
AND gm.student_id = ?
";

$group = DB::fetchOne($sql, [$groupId, $userId]);

if (!$group) {
    die("Bạn không thuộc nhóm.");
}

if ($group['role'] != 'leader') {
    die("Chỉ nhóm trưởng mới được đổi đề tài.");
}

/*
-------------------------------------------------------
Kiểm tra nhóm đã có đề tài
-------------------------------------------------------
*/

if (empty($group['section_session_topic_id'])) {
    die("Nhóm chưa đăng ký đề tài.");
}

/*
-------------------------------------------------------
Kiểm tra hạn chọn đề tài
-------------------------------------------------------
*/

$sql = "
SELECT topic_deadline
FROM sections_sessions
WHERE id = ?
";

$sectionSession = DB::fetchOne(
    $sql,
    [$group['section_session_id']]
);

if (
    !empty($sectionSession['topic_deadline']) &&
    strtotime(date("Y-m-d H:i:s")) >
    strtotime($sectionSession['topic_deadline'])
) {
    die("Đã hết thời gian đổi đề tài.");
}

/*
-------------------------------------------------------
Danh sách đề tài
-------------------------------------------------------
*/

$sql = "
SELECT

sst.id,
sst.topic_id,
sst.max_group,
t.topic_name,
t.description,

(
    SELECT COUNT(*)
    FROM groups g
    WHERE g.section_session_topic_id = sst.id
) total_group

FROM sections_sessions_topics sst

INNER JOIN topics t
ON sst.topic_id=t.id

WHERE sst.section_session_id=?

ORDER BY t.topic_name
";

$topics = DB::fetchAll(
    $sql,
    [$group['section_session_id']]
);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $sectionSessionTopicId =
        (int)($_POST['section_session_topic_id'] ?? 0);

    if ($sectionSessionTopicId <= 0) {
        $errors[] = "Vui lòng chọn đề tài.";
    }

    $sql = "
    SELECT
        max_group
    FROM sections_sessions_topics
    WHERE id = ?
    ";

    $topic = DB::fetchOne(
        $sql,
        [$sectionSessionTopicId]
    );

    if (!$topic) {

        $errors[] = "Đề tài không tồn tại.";

    } else {

        $sql = "
        SELECT COUNT(*) total
        FROM groups
        WHERE section_session_topic_id = ?
        ";

        $count = DB::fetchOne(
            $sql,
            [$sectionSessionTopicId]
        );

        if (
            !empty($topic['max_group']) &&
            $count['total'] >= $topic['max_group']
        ) {

            $errors[] = "Đề tài đã đủ số nhóm.";

        }

    }

    if (empty($errors)) {

        $sql = "
        UPDATE groups
        SET section_session_topic_id=?
        WHERE id=?
        ";

        DB::execute(
            $sql,
            [
                $sectionSessionTopicId,
                $groupId
            ]
        );

        header(
            "Location:index.php?page=chi-tiet-nhom&id=".$groupId
        );
        exit;
    }

}

require_once __DIR__ .
"/../../../views/de-tai/sua-dang-ky-de-tai.php";