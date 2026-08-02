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

$sql = "
SELECT *
FROM users
WHERE id = ?
";

$user = DB::fetchOne($sql, [$userId]);

if (!$user || !$user['is_active']) {
    destroyUserSession();
    showPopUp(
        'Tài khoản không tồn tại hoặc đã bị khóa.',
        'dang-nhap',
        'error'
    );
}

if ($user['role'] != 'lecturer') {
    showPopUp(
        'Chỉ giảng viên mới được sử dụng chức năng này.',
        'trang-chu',
        'error'
    );
}

$sectionId = (int)($_GET['section_id'] ?? 0);
$sessionId = (int)($_GET['session_id'] ?? 0);

if ($sectionId <= 0 || $sessionId <= 0) {
    showPopUp(
        'Thiếu thông tin lớp học phần.',
        'trang-chu',
        'error'
    );
}

/*
-----------------------------------------
Kiểm tra giảng viên phụ trách học phần
-----------------------------------------
*/

$sql = "
SELECT
ss.id,
s.section_name,
c.course_name

FROM sections_sessions ss

INNER JOIN sections s
ON ss.section_id=s.id

INNER JOIN courses c
ON s.course_id=c.id

INNER JOIN courses_lecturers cl
ON c.id=cl.course_id

WHERE
ss.section_id=?
AND
ss.session_id=?
AND
cl.lecturer_id=?
";

$sectionSession = DB::fetchOne($sql,[
    $sectionId,
    $sessionId,
    $userId
]);

if(!$sectionSession){

    showPopUp(
        'Bạn không được quản lý lớp học phần này.',
        'trang-chu',
        'error'
    );
}
$sectionSessionId = $sectionSession['id'];

$sql = "
    SELECT 
        g.id,
        g.group_name,
        g.created_at,
        g.status,
        t.topic_name,
        COUNT(gm.student_id) AS total_members
    FROM `groups` g
    LEFT JOIN `group_members` gm 
        ON g.id = gm.group_id
    LEFT JOIN `sections_sessions_topics` sst 
        ON g.section_session_topic_id = sst.id
    LEFT JOIN `topics` t 
        ON sst.topic_id = t.id
    WHERE g.section_session_id = ?
    GROUP BY 
        g.id,
        g.group_name,
        g.created_at,
        g.status,
        g.section_session_topic_id,
        sst.id,
        sst.topic_id,
        t.id,
        t.topic_name
    ORDER BY g.created_at ASC
    ";

$groups = DB::fetchAll($sql, [$sectionSessionId]);


/*
-----------------------------------------
Danh sách thành viên các nhóm
-----------------------------------------
*/

$sql = "
SELECT
    gm.group_id,
    gm.role,
    u.full_name,
    u.class
FROM group_members gm
INNER JOIN `users` u
    ON gm.student_id = u.id
INNER JOIN `groups` g
    ON gm.group_id = g.id
WHERE g.section_session_id = ?
ORDER BY gm.role DESC, u.full_name ASC
";

$members = DB::fetchAll($sql, [$sectionSessionId]);



$membersByGroup=[];

foreach($members as $member){

    $membersByGroup[$member['group_id']][]=$member;

}

foreach($groups as &$group){

    $group['members']=$membersByGroup[$group['id']] ?? [];

}

unset($group);

require_once __DIR__ . '/../../../views/layouts/header.php';
require_once __DIR__ . '/../../../views/nhom/danh-sach-nhom-giang-vien.php';