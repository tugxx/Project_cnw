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
$sql = "SELECT * 
        FROM `users` 
        WHERE `id` = ?";
$user = DB::fetchOne($sql, [$userId]);
if (!$user || !$user['is_active']) {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không tồn tại hoặc đã bị khoá.', 'dang-nhap', 'error');
}

if ($user['role'] !== 'student') {
    showPopUp('Chức năng này chỉ dành cho sinh viên.', 'trang-chu', 'error');
}

$groupId = $_GET['group_id'] ?? '';
if (empty($groupId)) {
    showPopUp('Thiếu thông tin nhóm.', 'danh-sach-lop-hoc-phan', 'error');
}

$sql = "SELECT g.*, 
               ss.id as section_session_id, 
               ss.section_id, ss.session_id, ss.group_deadline, ss.topic_deadline, ss.max_group
        FROM `groups` g
        JOIN `sections_sessions` ss 
            ON g.section_session_id = ss.id
        WHERE g.id = ?";
$group = DB::fetchOne($sql, [$groupId]);
if (!$group) {
    showPopUp('Nhóm không tồn tại.', 'danh-sach-lop-hoc-phan', 'error');
}

$sectionId = $group['section_id'];
$sessionId = $group['session_id'];
$sectionSessionId = $group['section_session_id'];
$sql = "SELECT `id` 
        FROM `sections_students` 
        WHERE `section_id` = ? AND `student_id` = ?";
$isEnrolled = DB::fetchOne($sql, [$sectionId, $userId]);
if (!$isEnrolled) {
    showPopUp('Bạn không phải là sinh viên thuộc lớp học phần này.', 'danh-sach-lop-hoc-phan', 'error');
}

$sql = "SELECT * FROM `group_members` 
        WHERE `group_id` = ? 
        AND `student_id` = ?";
$myMemberRecord = DB::fetchOne($sql, [$groupId, $userId]);

$isMember = !empty($myMemberRecord);
$isLeader = $isMember && ($myMemberRecord['role'] === 'leader');
if (!$isMember) {
    showPopUp('Bạn không phải thành viên của nhóm này.', "danh-sach-nhom?section_id={$sectionId}&session_id={$sessionId}", 'error');
}

$errors = [];
$successMsg = '';
$now = date('Y-m-d H:i:s');
if (isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
    if (!$isLeader) {
        $errors[] = 'Chỉ có trưởng nhóm mới có quyền thay đổi trạng thái nhóm.';
    } elseif (!empty($group['group_deadline']) && $now > $group['group_deadline']) {
        $errors[] = 'Đã quá thời hạn thay đổi trạng thái nhóm.';
    } else {
        $newStatus = ($group['status'] === 'closed') ? 'open' : 'closed';
        $sql = "UPDATE `groups` 
                SET `status` = ? 
                WHERE `id` = ?";
        DB::execute($sql, [$newStatus, $groupId]);
        header("Location: chi-tiet-nhom-sinh-vien?group_id={$groupId}");
        exit;
    }
}

if (isset($_POST['action']) && $_POST['action'] === 'kick_member') {
    $targetStudentId = trim($_POST['student_id'] ?? '');

    if (!$isLeader) {
        $errors[] = 'Chỉ trưởng nhóm mới có quyền xóa thành viên.';
    } elseif (!empty($group['group_deadline']) && $now > $group['group_deadline']) {
        $errors[] = 'Đã quá thời hạn thay đổi thành viên nhóm.';
    } elseif ($targetStudentId === $userId) {
        $errors[] = 'Bạn không thể tự xóa chính mình bằng chức năng này.';
    } else {
        $sql = "DELETE FROM `group_members` WHERE `group_id` = ? AND `student_id` = ?";
        DB::execute($sql, [$groupId, $targetStudentId]);
        header("Location: chi-tiet-nhom?group_id={$groupId}");
        exit;
    }
}

if (isset($_POST['action']) && $_POST['action'] === 'transfer_leader') {
    $newLeaderId = trim($_POST['new_leader_id'] ?? '');

    if (!$isLeader) {
        $errors[] = 'Chỉ trưởng nhóm mới có quyền nhượng quyền trưởng nhóm.';
    } elseif (empty($newLeaderId) || $newLeaderId === $userId) {
        $errors[] = 'Thành viên được chọn không hợp lệ.';
    } else {
        try {
            DB::beginTransaction();

            $sql = "SELECT `student_id` FROM `group_members` WHERE `group_id` = ? AND `student_id` = ?";
            $checkMember = DB::fetchOne($sql, [$groupId, $newLeaderId]);

            if (!$checkMember) {
                throw new Exception('MEMBER_NOT_FOUND');
            }

            $sql = "UPDATE `group_members` SET `role` = 'member' WHERE `group_id` = ? AND `student_id` = ?";
            DB::execute($sql, [$groupId, $userId]);

            $sql = "UPDATE `group_members` SET `role` = 'leader' WHERE `group_id` = ? AND `student_id` = ?";
            DB::execute($sql, [$groupId, $newLeaderId]);

            DB::commit();
            header("Location: chi-tiet-nhom-sinh-vien?group_id={$groupId}");
            exit;
        } catch (Exception $e) {
            DB::rollBack();
            $errors[] = 'Lỗi trong quá trình chuyển quyền trưởng nhóm.';
        }
    }
}


if (isset($_POST['action']) && $_POST['action'] === 'leave_group') {
    if (!empty($group['group_deadline']) && $now > $group['group_deadline']) {
        $errors[] = 'Đã quá thời hạn rời nhóm.';
    } elseif ($isLeader) {
        $errors[] = 'Trưởng nhóm không thể rời nhóm. Vui lòng nhượng quyền trưởng nhóm hoặc giải tán nhóm.';
    } else {
        $sql = "DELETE FROM `group_members` 
                WHERE `group_id` = ? 
                AND `student_id` = ?";
        DB::execute($sql, [$groupId, $userId]);

        header("Location: /Project_cnw/danh-sach-nhom?section_id={$sectionId}&session_id={$sessionId}");
        exit;
    }
}


if (isset($_POST['action']) && $_POST['action'] === 'disband_group') {
    if (!$isLeader) {
        $errors[] = 'Chỉ trưởng nhóm mới có quyền giải tán nhóm.';
    } elseif (!empty($group['group_deadline']) && $now > $group['group_deadline']) {
        $errors[] = 'Đã quá thời hạn thay đổi nhóm.';
    } else {
        try {
            DB::beginTransaction();

            $sql = "DELETE FROM `group_members` WHERE `group_id` = ?";
            DB::execute($sql, [$groupId]);

            $sql = "DELETE FROM `groups` WHERE `id` = ?";
            DB::execute($sql, [$groupId]);

            DB::commit();
            showPopUp('Đã giải tán nhóm thành công.', "danh-sach-nhom?section_id={$sectionId}&session_id={$sessionId}", 'success');
            exit;
        } catch (Exception $e) {
            DB::rollBack();
            $errors[] = 'Không thể giải tán nhóm. Vui lòng thử lại.';
        }
    }
}


$sql = "SELECT gm.student_id, gm.role, gm.joined_at, u.full_name, u.class, u.user_code 
        FROM `group_members` gm
        JOIN `users` u ON gm.student_id = u.id
        WHERE gm.group_id = ?
        ORDER BY gm.role DESC, gm.joined_at ASC";
$members = DB::fetchAll($sql, [$groupId]) ?: [];


require_once __DIR__ . '/../../../views/layouts/header-student.php';
require_once __DIR__ . '/../../../views/nhom/chi-tiet-nhom-sinh-vien.php';