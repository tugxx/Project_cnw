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

$groupId = (int)($_GET['group_id'] ?? $_POST['groupId'] ?? 0);
$isLeader = false;
$hasGroup = false;
$sectionSessionId = 0;
$totalMember = 0;
$registrationNotice = '';

$myTopicId = 0;

if ($groupId > 0) {
    $sql = "
        SELECT
            gm.role,
            g.section_session_id,
            g.section_session_topic_id
        FROM `group_members` gm
        INNER JOIN `groups` g 
            ON gm.group_id = g.id
        WHERE gm.`group_id` = ? AND gm.`student_id` = ?
    ";

    $member = DB::fetchOne($sql, [$groupId, $userId]);

    if ($member) {
        $hasGroup = true;
        $sectionSessionId = $member['section_session_id'];
        $myTopicId = $member['section_session_topic_id'];
        
        if ($member['role'] === 'leader') {
            $isLeader = true;
        } else {
            $registrationNotice = "Chỉ nhóm trưởng mới có quyền đăng ký đề tài.";
        }
    }
}

$sectionId = $_GET['section_id'] ?? 0;
$sessionId = $_GET['session_id'] ?? 0;
if (!$sectionSessionId) {
    $sectionSessionId = (int)($_GET['sectionSessionId'] ?? $_POST['sectionSessionId'] ?? 0);
}

if ($sectionSessionId <= 0) {
    die("Không tìm thấy thông tin đợt đăng ký.");
}

$sql = "SELECT `topic_deadline`
        FROM `sections_sessions`
        WHERE `id` = ?
";

$sectionSession = DB::fetchOne($sql, [$sectionSessionId]);

if (!$sectionSession) {
    die("Đợt đăng ký không tồn tại.");
}

$isExpired = false;
if ($sectionSession['topic_deadline'] != null && strtotime($sectionSession['topic_deadline']) < time()) {
    $isExpired = true;
    $registrationNotice = "Đã hết thời hạn đăng ký đề tài.";
}

if ($hasGroup) {
    $sql = "
        SELECT COUNT(*) total
        FROM group_members
        WHERE group_id = ?
    ";
    $countResult = DB::fetchOne($sql, [$groupId]);
    $totalMember = (int)($countResult['total'] ?? 0);
}

$sql = "SELECT
        sst.id,
        t.topic_name,
        t.description,
        sst.min_member,
        sst.max_member,
        sst.max_group,
        (SELECT COUNT(*) FROM `groups` g WHERE g.section_session_topic_id = sst.id) AS `current_groups`,
        (SELECT GROUP_CONCAT(g.group_name SEPARATOR ', ')
         FROM `groups` g 
         WHERE g.section_session_topic_id = sst.id) AS `registered_group_names`
    FROM `sections_sessions_topics` sst
    INNER JOIN `topics` t 
        ON t.id = sst.topic_id
    WHERE sst.section_session_id = ?
    ORDER BY t.topic_name
";

$topics = DB::fetchAll($sql, [$sectionSessionId]);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!$hasGroup) {
        $errors[] = "Bạn chưa tham gia nhóm nào.";
    } elseif (!$isLeader) {
        $errors[] = "Chỉ nhóm trưởng mới được đăng ký đề tài.";
    } elseif ($isExpired) {
        $errors[] = "Đã hết thời gian đăng ký đề tài.";
    }

    $sectionSessionTopicId = (int)($_POST['section_session_topic_id'] ?? 0);

    if ($sectionSessionTopicId <= 0) {
        $errors[] = "Vui lòng chọn đề tài.";
    }

    if (empty($errors)) {
        $sql = "
        SELECT
            `min_member`,
            `max_member`,
            `max_group`
        FROM `sections_sessions_topics`
        WHERE `id` = ?
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

            $sql = "SELECT COUNT(*) total
                FROM `groups`
                WHERE `section_session_topic_id` = ?
            ";
            $count = DB::fetchOne($sql, [$sectionSessionTopicId]);

            if ($topic['max_group'] != null && $count['total'] >= $topic['max_group']) {
                $errors[] = "Đề tài đã đủ số nhóm đăng ký.";
            }

        }

    }

    if (empty($errors)) {

        $sql = "
        UPDATE `groups`
        SET `section_session_topic_id` = ?
        WHERE `id` = ?
        ";

        DB::execute($sql, [
            $sectionSessionTopicId,
            $groupId
        ]);

        header("Location: /Project_cnw/danh-sach-de-tai-sinh-vien?section_id=" . $sectionId . "&session_id=" . $sessionId . "&group_id=" . $groupId);
        exit;
    }

}

$canSubmit = $hasGroup && $isLeader && !$isExpired;
require_once __DIR__.'/../../../views/de-tai/dang-ky-de-tai.php';