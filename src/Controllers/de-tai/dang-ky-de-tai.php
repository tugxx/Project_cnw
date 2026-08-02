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

if ($_SESSION['user']['role'] != 'student') {
    die("Chỉ sinh viên mới được đăng ký đề tài.");
}

$groupId = (int)($_GET['groupId'] ?? $_POST['groupId'] ?? 0);

if ($groupId <= 0) {
    die("Nhóm không tồn tại.");
}

/*
====================================================
Kiểm tra thành viên nhóm
====================================================
*/

$sql = "
SELECT
    gm.role,
    g.section_session_id,
    g.section_session_topic_id
FROM group_members gm
INNER JOIN groups g
ON gm.group_id = g.id
WHERE gm.group_id = ?
AND gm.student_id = ?
";

$member = DB::fetchOne($sql, [$groupId, $userId]);

if (!$member) {
    die("Bạn không thuộc nhóm này.");
}

if ($member['role'] != 'leader') {
    die("Chỉ nhóm trưởng mới được đăng ký đề tài.");
}

$sectionSessionId = $member['section_session_id'];

if ($member['section_session_topic_id']) {
    die("Nhóm đã đăng ký đề tài.");
}

/*
====================================================
Kiểm tra hạn chọn đề tài
====================================================
*/

$sql = "
SELECT topic_deadline
FROM sections_sessions
WHERE id = ?
";

$sectionSession = DB::fetchOne($sql, [$sectionSessionId]);

if (!$sectionSession) {
    die("Đợt đăng ký không tồn tại.");
}

if (
    $sectionSession['topic_deadline'] != null &&
    strtotime($sectionSession['topic_deadline']) < time()
) {
    die("Đã hết thời gian đăng ký đề tài.");
}


$sql = "
SELECT COUNT(*) total
FROM group_members
WHERE group_id = ?
";

$totalMember = DB::fetchOne($sql, [$groupId]);
$totalMember = $totalMember['total'];


$sql = "
SELECT
    sst.id,
    t.topic_name,
    t.description,
    sst.min_member,
    sst.max_member,
    sst.max_group
FROM sections_sessions_topics sst
INNER JOIN topics t
ON t.id = sst.topic_id
WHERE sst.section_session_id = ?
ORDER BY t.topic_name
";

$topics = DB::fetchAll($sql, [$sectionSessionId]);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $sectionSessionTopicId = (int)($_POST['section_session_topic_id'] ?? 0);

    if ($sectionSessionTopicId <= 0) {
        $errors[] = "Vui lòng chọn đề tài.";
    }

    if (empty($errors)) {


        $sql = "
        SELECT
            min_member,
            max_member,
            max_group
        FROM sections_sessions_topics
        WHERE id = ?
        ";

        $topic = DB::fetchOne($sql, [$sectionSessionTopicId]);

        if (!$topic) {

            $errors[] = "Đề tài không tồn tại.";

        } else {


            if (
                $topic['min_member'] != null &&
                $totalMember < $topic['min_member']
            ) {
                $errors[] = "Nhóm chưa đủ số thành viên tối thiểu.";
            }

            if (
                $topic['max_member'] != null &&
                $totalMember > $topic['max_member']
            ) {
                $errors[] = "Nhóm vượt quá số thành viên cho phép.";
            }


            $sql = "
            SELECT COUNT(*) total
            FROM groups
            WHERE section_session_topic_id = ?
            ";

            $count = DB::fetchOne($sql, [$sectionSessionTopicId]);

            if (
                $topic['max_group'] != null &&
                $count['total'] >= $topic['max_group']
            ) {

                $errors[] = "Đề tài đã đủ số nhóm.";

            }

        }

    }

    if (empty($errors)) {

        $sql = "
        UPDATE groups
        SET section_session_topic_id = ?
        WHERE id = ?
        ";

        DB::execute($sql, [
            $sectionSessionTopicId,
            $groupId
        ]);

        header("Location: index.php?page=chi-tiet-nhom&id=".$groupId);
        exit;
    }

}

require_once __DIR__.'/../../../views/de-tai/dang-ky-de-tai.php';