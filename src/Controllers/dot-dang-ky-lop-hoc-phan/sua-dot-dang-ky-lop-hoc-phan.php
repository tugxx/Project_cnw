<?php
$userId = $_SESSION['user']['id'] ?? "";
$sql = "SELECT `id`, `is_active`, `role` 
        FROM `users`
        WHERE `id` = ?";
$user = DB::fetchOne($sql, [$userId]);

if (!$user || !$user['is_active']) {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không tồn tại hoặc đã bị khoá.', 'dang-nhap', 'error');
    exit;
}

if ($user['role'] !== 'lecturer') {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không có quyền thực hiện chức năng này.', 'dang-nhap', 'error');
    exit;
}

$sectionId = $_GET['section_id'] ?? "";
$sessionId = $_GET['session_id'] ?? "";
$sql = "SELECT s.id AS section_id, s.section_code, s.section_name, s.course_id
        FROM `sections` s
        WHERE s.id = ?";
$section = DB::fetchOne($sql, [$sectionId]);
if (!$section) {
    showPopUp('Lớp học phần không tồn tại.', 'danh-sach-hoc-phan', 'error');
    exit;
}

$sql = "SELECT `id` 
        FROM `courses_lecturers` 
        WHERE `course_id` = ? AND `lecturer_id` = ?";
$isAssigned = DB::fetchOne($sql, [$section['course_id'], $userId]);
if (!$isAssigned) {
    showPopUp('Bạn không có quyền quản lý đợt đăng ký của lớp học phần này.', 'danh-sach-hoc-phan', 'error');
    exit;
}

$sql = "SELECT rs.id AS session_id, rs.registration_session_name, rs.start_time, rs.end_time, rs.course_id,
               ss.id AS sections_session_id, ss.group_deadline, ss.topic_deadline, ss.max_group
        FROM `registration_sessions` rs
        LEFT JOIN `sections_sessions` ss 
            ON ss.session_id = rs.id AND ss.section_id = ?
        WHERE rs.id = ? AND rs.course_id = ?";
$sessionData = DB::fetchOne($sql, [$sectionId, $sessionId, $section['course_id']]);
if (!$sessionData) {
    showPopUp('Đợt đăng ký không tồn tại hoặc không thuộc học phần này.', 'danh-sach-hoc-phan', 'error');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groupDeadline = $_POST['group_deadline'] ?? null;
    $topicDeadline = $_POST['topic_deadline'] ?? null;
    $maxGroup = isset($_POST['max_group']) ? (int)$_POST['max_group'] : 0;

    $startTime = $sessionData['start_time'];
    $endTime = $sessionData['end_time'];

    if (empty($groupDeadline) || empty($topicDeadline) || $maxGroup <= 0) {
        $errors[] = "Vui lòng nhập đầy đủ và chính xác các thông tin cấu hình!";
    } 

    $groupTime = $groupDeadline ? strtotime($groupDeadline) : false;
    $topicTime = $topicDeadline ? strtotime($topicDeadline) : false;
    if (empty($errors) && (!$groupTime || !$topicTime)) {
        $errors[] = "Định dạng ngày tháng không hợp lệ!";
    }

    if (empty($errors)) {
        $groupDeadlineFormatted = date('Y-m-d H:i:s', $groupTime);
        $topicDeadlineFormatted = date('Y-m-d H:i:s', $topicTime);

        $oldGroupDeadline = $sessionData['group_deadline'] ?? null;
        $oldTopicDeadline = $sessionData['topic_deadline'] ?? null;

        $oldGroupDeadlineFormatted = $oldGroupDeadline ? date('Y-m-d H:i:s', strtotime($oldGroupDeadline)) : null;
        $oldTopicDeadlineFormatted = $oldTopicDeadline ? date('Y-m-d H:i:s', strtotime($oldTopicDeadline)) : null;
    
        $now = date('Y-m-d H:i:s');
        if ($groupDeadlineFormatted !== $oldGroupDeadlineFormatted && $groupDeadlineFormatted < $now) {
            $errors[] = "Hạn chót chọn nhóm mới không được thiết lập trong quá khứ!";
        }

        if ($topicDeadlineFormatted !== $oldTopicDeadlineFormatted && $topicDeadlineFormatted < $now) {
            $errors[] = "Hạn chót chọn đề tài mới không được thiết lập trong quá khứ!";
        }

        if ($groupDeadlineFormatted < $startTime || $groupDeadlineFormatted > $endTime) {
            $errors[] = "Hạn chót chọn nhóm phải nằm trong khoảng thời gian diễn ra đợt đăng ký chung (" . date('d/m/Y H:i', strtotime($startTime)) . " - " . date('d/m/Y H:i', strtotime($endTime)) . ")!";
        }

        if ($topicDeadlineFormatted < $startTime || $topicDeadlineFormatted > $endTime) {
            $errors[] = "Hạn chót chọn đề tài phải nằm trong khoảng thời gian diễn ra đợt đăng ký chung (" . date('d/m/Y H:i', strtotime($startTime)) . " - " . date('d/m/Y H:i', strtotime($endTime)) . ")!";
        }

        if ($groupDeadlineFormatted > $topicDeadlineFormatted) {
            $errors[] = "Hạn chót chọn nhóm không thể sau hạn chót chọn đề tài!";
        }
    }

    if (empty($errors) && $sessionData['sections_session_id']) {
        $sectionsSessionId = $sessionData['sections_session_id'];
        $sql = "SELECT COUNT(`id`) AS total_created_groups 
                FROM `groups` 
                WHERE `section_session_id` = ?";
        $groupStat = DB::fetchOne($sql, [$sectionsSessionId]);
        $totalCreatedGroups = (int)($groupStat['total_created_groups'] ?? 0);

        if ($maxGroup < $totalCreatedGroups) {
            $errors[] = "Hiện đã có {$totalCreatedGroups} nhóm được khởi tạo! Hãy xoá các nhóm trước khi giảm số lượng";
        }
    }

    if (empty($errors)) {
        if ($sessionData['sections_session_id']) {
            $sql = "UPDATE `sections_sessions`
                    SET `group_deadline` = ?, `topic_deadline` = ?, `max_group` = ? 
                    WHERE `id` = ?";
            DB::execute($sql, [$groupDeadlineFormatted, $topicDeadlineFormatted, $maxGroup, $sessionData['sections_session_id']]);
        } else {
            $sqlInsert = "INSERT INTO `sections_sessions` (`section_id`, `session_id`, `group_deadline`, `topic_deadline`, `max_group`) 
                          VALUES (?, ?, ?, ?, ?)";
            DB::execute($sqlInsert, [$sectionId, $sessionId, $groupDeadlineFormatted, $topicDeadlineFormatted, $maxGroup]);
        }
        header("Location: /Project_cnw/danh-sach-nhom?section_id={$sectionId}&session_id={$sessionId}");
        exit;
    }
}


require_once __DIR__ . '/../../../views/layouts/header-lecturer.php';
require_once __DIR__ . '/../../../views/dot-dang-ky-lop-hoc-phan/sua-dot-dang-ky-lop-hoc-phan.php';
?>
