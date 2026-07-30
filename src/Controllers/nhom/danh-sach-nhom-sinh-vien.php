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
        FROM `section_students` 
        WHERE `section_id` = ? AND `student_id` = ?";
$isEnrolled = DB::fetchOne($sql, [$sectionId, $userId]);
if (!$isEnrolled) {
    showPopUp('Bạn không phải là sinh viên thuộc lớp học phần này.', 'danh-sach-lop-hoc-phan', 'error');
}


$sectionSessionId = $sectionSession['id'];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groupId = trim($_POST['group_id'] ?? '');
    $now = date('Y-m-d H:i:s');
    if (!empty($sectionSession['group_deadline']) && $now > $sectionSession['group_deadline']) {
        $errors[] = 'Đã quá thời hạn lập/gia nhập nhóm cho đợt đăng ký này.';
    }

    $sql = "SELECT * 
            FROM `groups` 
            WHERE `id` = ? AND `section_session_id` = ?";
    $group = DB::fetchOne($sql, [$groupId, $sectionSessionId]);
    if (!$group) {
        $errors[] = 'Nhóm không tồn tại hoặc không thuộc lớp học phần này.';
    }

    if (empty($errors)) {
        try {
            DB::beginTransaction();
            $sql = "SELECT `id`, `status` 
                    FROM `groups` 
                    WHERE `id` = ? 
                    FOR UPDATE";
            $lockedGroup = DB::fetchOne($sql, [$groupId]);

            if ($lockedGroup['status'] !== 'open') {
                throw new Exception('GROUP_NOT_OPEN');
            }

            $sql = "SELECT g.id 
                    FROM `groups` g
                    JOIN `group_members` gm ON g.id = gm.group_id
                    WHERE g.section_session_id = ? AND gm.student_id = ?";
            $existingGroup = DB::fetchOne($sql, [$sectionSessionId, $userId]);
            if ($existingGroup) {
                throw new Exception('ALREADY_IN_GROUP');
            }

            if (!empty($sectionSession['max_members'])) {
                $sql = "SELECT COUNT(*) as total 
                        FROM `group_members` 
                        WHERE `group_id` = ?";
                $memberCount = DB::fetchOne($sql, [$groupId]);
                if ($memberCount && $memberCount['total'] >= $sectionSession['max_members']) {
                    throw new Exception('GROUP_FULL');
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
            if ($e->getMessage() === "GROUP_NOT_OPEN") {
                $errors[] = 'Nhóm này hiện đang không mở tiếp nhận thêm thành viên.';
            } elseif ($e->getMessage() === "ALREADY_IN_GROUP") {
                $errors[] = 'Bạn đã tham gia một nhóm khác trong lớp học phần - đợt đăng ký này.';
            } elseif ($e->getMessage() === "GROUP_FULL") {
                $errors[] = 'Nhóm đã đủ số lượng thành viên tối đa.';
            } else {
                $errors[] = 'Đã xảy ra lỗi trong quá trình gia nhập nhóm. Vui lòng thử lại.';
                error_log('Join Group Error: ' . $e->getMessage());
            }
        }
    }
}

$sql = "SELECT `id`, `section_session_id`, `group_name`, `created_at`, `status` 
        FROM `groups` 
        WHERE `section_session_id` = ? 
        ORDER BY `created_at` ASC";
$groups = DB::fetchAll($sql, [$sectionSessionId]) ?: [];

$sql = "SELECT g.id 
        FROM groups g
        JOIN group_members gm 
            ON g.id = gm.group_id
        WHERE g.section_session_id = ? AND gm.student_id = ?";
$myGroup = DB::fetchOne($sql, [$sectionSessionId, $userId]);
$myGroupId = $myGroup ? $myGroup['id'] : "";

$sql = "SELECT gm.group_id, gm.student_id, gm.role, gm.joined_at,
            u.full_name, u.class
        FROM group_members gm
        JOIN users u 
            ON gm.student_id = u.id
        JOIN groups g 
            ON gm.group_id = g.id
        WHERE g.section_session_id = ?
        ORDER BY gm.role DESC, gm.joined_at ASC";
$allMembers = DB::fetchAll($sql, [$sectionSessionId]) ?: [];

$membersByGroup = [];
foreach ($allMembers as $member) {
    $membersByGroup[$member['group_id']][] = $member;
}

foreach ($groups as $key => $group) {
    $groupId = $group['id'];
    $members = $membersByGroup[$groupId] ?? [];
    
    $groups[$key]['members'] = $members;
    $groups[$key]['total_members'] = count($members);
}

require_once __DIR__ . '/../../../views/layouts/header.php';
require_once __DIR__ . '/../../../views/nhom/danh-sach-nhom.php';