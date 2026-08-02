<?php
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

if (!isset($_SESSION['user'])) {
    header('Location: dang-nhap');
    exit;
}

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

$sectionId = $_POST['section_id'] ?? '';
$sessionId = $_POST['session_id'] ?? '';
if (empty($sectionId) || empty($sessionId)) {
    showPopUp('Thông tin không hợp lệ.', 'trang-chu', 'error');
    exit;
}

$sql = "SELECT ss.id AS section_session_id, s.course_id 
        FROM `sections_sessions` ss
        JOIN `sections` s 
            ON ss.section_id = s.id
        WHERE ss.section_id = ? AND ss.session_id = ?";
$sectionSession = DB::fetchOne($sql, [$sectionId, $sessionId]);
if (!$sectionSession) {
    showPopUp('Liên kết đợt đăng ký hoặc lớp học phần không tồn tại.', "chi-tiet-dot-dang-ky-lop-hoc-phan?session_id={$sessionId}", 'error');
    exit;
}

$sql = "SELECT `id` 
        FROM `courses_lecturers` 
        WHERE `course_id` = ? AND `lecturer_id` = ?";
$isAssigned = DB::fetchOne($sql, [$sectionSession['course_id'], $userId]);
if (!$isAssigned) {
    showPopUp('Bạn không có quyền quản lý đợt đăng ký của lớp học phần này.', 'trang-chu', 'error');
    exit;
}

$sectionSessionId = $sectionSession['section_session_id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        DB::beginTransaction();
        $sql = "SELECT id 
                FROM `groups` 
                WHERE `section_session_id` = ?";
        $groups = DB::fetchAll($sql, [$sectionSessionId]);

        if (!empty($groups)) {
            $groupIds = array_column($groups, 'id');
            $inClause = implode(',', array_fill(0, count($groupIds), '?'));

            $sql = "DELETE FROM `group_members`
                    WHERE `group_id` IN ($inClause)";
            DB::execute($sql, $groupIds);

            $sql = "DELETE FROM `groups` 
                    WHERE `section_session_id` = ?";
            DB::execute($sql, [$sectionSessionId]);
        }

        $sql = "DELETE FROM `sections_sessions_topics` 
                WHERE `section_session_id` = ?";
        DB::execute($sql, [$sectionSessionId]);

        $sql = "DELETE FROM `sections_sessions` 
                WHERE `id` = ?";
        DB::execute($sql, [$sectionSessionId]);
        DB::commit();

        showPopUp('Đã xóa đợt đăng ký khỏi lớp học phần.', "chi-tiet-dot-dang-ky-lop-hoc-phan?session_id={$sessionId}", 'success');
        exit;
    } catch (Exception $e) {
        DB::rollBack();
        error_log("Lỗi khi xoá section session: " . $e->getMessage());
        showPopUp('Có lỗi xảy ra khi xóa đợt đăng ký.', "chi-tiet-dot-dang-ky-lop-hoc-phan?session_id={$sessionId}", 'error');
    }
}
?>