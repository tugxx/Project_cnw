<?php
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: dang-nhap');
    exit;
}

$userId = $_SESSION['user']['id'];
$sql = "SELECT * 
        FROM `users` 
        WHERE `id` = ?";
$user = DB::fetchOne($sql, [$userId]);
if (!$user || !$user['is_active']) {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không tồn tại hoặc đã bị khoá.', 'dang-nhap', 'error');
}

if ($user['role'] !== 'student') {
    showPopUp('Chức năng này chỉ dành cho sinh viên.', 'dang-nhap', 'error');
}

$sectionId = trim($_GET['section_id'] ?? '');
$sessionId = trim($_GET['session_id'] ?? '');
if (empty($sectionId) || empty($sessionId)) {
    showPopUp('Thiếu thông tin lớp học phần, đợt đăng ký.', 'danh-sach-lop-hoc-phan', 'error');
}

$sql = "SELECT * 
        FROM `sections_sessions` 
        WHERE `section_id` = ? AND `session_id` = ?";
$sectionSession = DB::fetchOne($sql, [$sectionId, $sessionId]);
if (!$sectionSession) {
    showPopUp('Lớp học phần - Đợt đăng ký không tồn tại.', 'danh-sach-lop-hoc-phan', 'error');
}

$sql = "SELECT `id` 
        FROM `sections_students` 
        WHERE `section_id` = ? AND `student_id` = ?";
$isEnrolled = DB::fetchOne($sql, [$sectionId, $userId]);
if (!$isEnrolled) {
    showPopUp('Bạn không phải là sinh viên thuộc lớp học phần này.', 'danh-sach-lop-hoc-phan', 'error');
}


$sectionSessionId = $sectionSession['id'];
$errors = [];
if (isset($_POST['join_group'])) {
    $groupId = trim($_POST['join_group']);
    $now = date('Y-m-d H:i:s');
    if (!empty($sectionSession['group_deadline']) && $now > $sectionSession['group_deadline']) {
        $errors[] = 'Đã quá thời hạn lập/gia nhập nhóm cho đợt đăng ký này.';
    }

    if (empty($errors)) {
        try {
            DB::beginTransaction();
            $sql = "SELECT `id`, `status`, `section_session_topic_id`
                    FROM `groups` 
                    WHERE `id` = ? AND `section_session_id` = ?
                    FOR UPDATE";
            $groupLocked = DB::fetchOne($sql, [$groupId, $sectionSessionId]);

            if (!$groupLocked) {
                throw new Exception('GROUP_NOT_FOUND');
            }

            if ($groupLocked['status'] === 'closed') {
                throw new Exception('GROUP_CLOSED');
            }

            $sql = "SELECT g.id 
                    FROM `groups` g
                    JOIN `group_members` gm 
                        ON g.id = gm.group_id
                    WHERE g.section_session_id = ? AND gm.student_id = ?";
            $existingGroup = DB::fetchOne($sql, [$sectionSessionId, $userId]);
            if ($existingGroup) {
                throw new Exception('ALREADY_IN_GROUP');
            }

            if (!empty($groupLocked['section_session_topic_id'])) {
                $sql = "SELECT `max_member` 
                        FROM `sections_sessions_topics` 
                        WHERE `id` = ?";
                $topic = DB::fetchOne($sql, [$groupLocked['section_session_topic_id']]);

                if ($topic && !empty($topic['max_member'])) {
                    $sql = "SELECT COUNT(*) as total 
                            FROM `group_members` 
                            WHERE `group_id` = ?";
                    $memberCount = DB::fetchOne($sql, [$groupId]);

                    if ($memberCount && $memberCount['total'] >= (int)$topic['max_member']) {
                        throw new Exception('GROUP_FULL');
                    }
                }
            }

            $joinedAt = date('Y-m-d H:i:s');
            $sql = "INSERT INTO `group_members` (`group_id`, `student_id`, `role`, `joined_at`) 
                    VALUES (?, ?, 'member', ?)";
            DB::execute($sql, [$groupId, $userId, $joinedAt]);
            DB::commit();
            header("Location: danh-sach-nhom?section_id={$sectionId}&session_id={$sessionId}");
            exit;
        } catch (Exception $e) {
            DB::rollBack();
            
            if ($e->getMessage() === "GROUP_NOT_FOUND") {
                $errors[] = 'Nhóm không tồn tại hoặc không thuộc lớp học phần này.';
            } elseif ($e->getMessage() === "GROUP_CLOSED") {
                $errors[] = 'Nhóm này hiện đang đóng, không thể tham gia.';
            } elseif ($e->getMessage() === "ALREADY_IN_GROUP") {
                $errors[] = 'Bạn đã tham gia một nhóm khác trong lớp học phần - đợt đăng ký này.';
            } elseif ($e->getMessage() === "GROUP_FULL") {
                $errors[] = 'Nhóm đã đạt số lượng thành viên tối đa cho đề tài này.';
            } else {
                $errors[] = 'Đã xảy ra lỗi trong quá trình gia nhập nhóm. Vui lòng thử lại.';
                error_log('Join Group Error: ' . $e->getMessage());
            }
        }
    }
}


if (isset($_POST['toggle_status'])) {
    $groupId = trim($_POST['toggle_status']);

    $now = date('Y-m-d H:i:s');
    if (!empty($sectionSession['group_deadline']) && $now > $sectionSession['group_deadline']) {
        $errors[] = 'Đã quá thời hạn thay đổi trạng thái nhóm.';
    }

    $sql = "SELECT * 
            FROM `groups` 
            WHERE `id` = ? AND `section_session_id` = ?";
    $group = DB::fetchOne($sql, [$groupId, $sectionSessionId]);
    if (!$group) {
        $errors[] = 'Nhóm không tồn tại hoặc không thuộc lớp học phần này.';
    }
        
    if (empty($errors)) {
        $sql = "SELECT `group_id` 
                FROM `group_members` 
                WHERE `group_id` = ? AND `student_id` = ? AND `role` = 'leader'";
        $isLeader = DB::fetchOne($sql, [$groupId, $userId]);
        if (!$isLeader) {
            $errors[] = 'Chỉ có trưởng nhóm mới có quyền đổi trạng thái nhóm.';
        } else {
            $newStatus = ($group['status'] === 'closed') ? 'open' : 'closed';
            
            $sql = "UPDATE `groups` 
                    SET `status` = ? 
                    WHERE `id` = ?";
            DB::execute($sql, [$newStatus, $groupId]);
            header("Location: /Project_cnw/danh-sach-nhom?section_id=" . $sectionId . "&session_id=" . $sessionId);
            exit;
        }
    }
}


$sql = "SELECT `id`, `section_session_id`, `group_name`, `created_at`, `status` 
        FROM `groups` 
        WHERE `section_session_id` = ? 
        ORDER BY `created_at` ASC";
$groups = DB::fetchAll($sql, [$sectionSessionId]) ?: [];

$sql = "SELECT g.id
        FROM `groups` g
        JOIN `group_members` gm
            ON g.id = gm.group_id
        WHERE g.section_session_id = ? AND gm.student_id = ?";
$myGroup = DB::fetchOne($sql, [$sectionSessionId, $userId]);
$myGroupId = $myGroup ? $myGroup['id'] : "";

$sql = "SELECT gm.group_id, gm.student_id, gm.role, gm.joined_at,
            u.full_name, u.class
        FROM `group_members` gm
        JOIN `users` u
            ON gm.student_id = u.id
        JOIN `groups` g
            ON gm.group_id = g.id
        WHERE g.section_session_id = ?
        ORDER BY gm.role DESC, gm.joined_at ASC";
$allMembers = DB::fetchAll($sql, [$sectionSessionId]) ?: [];

$membersByGroup = [];
foreach ($allMembers as $member) {
    $membersByGroup[$member['group_id']][] = $member;
}

$myGroupData = null;
$otherGroups = [];
foreach ($groups as $index => $group) {
    $groupId = $group['id'];
    $members = $membersByGroup[$groupId] ?? [];
    $leader = (!empty($members) && $members[0]['role'] === 'leader') ? $members[0] : null;
    
    $group['stt'] = $index + 1;
    $group['members'] = $members;
    $group['total_members'] = count($members);
    $group['leader_id'] = $leader ? $leader['student_id'] : null;
    $group['leader_name'] = $leader ? $leader['full_name'] : '';

    if (!empty($myGroupId) && $groupId == $myGroupId) {
        $myGroupData = $group;
    } else {
        $otherGroups[] = $group;
    }
}

if ($myGroupData !== null) {
    array_unshift($otherGroups, $myGroupData);
}
$groups = $otherGroups;

require_once __DIR__ . '/../../../views/layouts/header.php';
require_once __DIR__ . '/../../../views/nhom/danh-sach-nhom-sinh-vien.php';