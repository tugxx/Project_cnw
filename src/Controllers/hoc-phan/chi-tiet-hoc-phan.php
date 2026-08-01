<?php
$courseId = (int)($_GET['course_id'] ?? 0);

if (!isLoggedIn()) {
    header("Location: dang-nhap");
    exit;
}

$userId = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];

if (!isUserActive($userId)) {
    session_destroy();
    header("Location: dang-nhap");
    exit;
}

/*
Lấy học phần
*/
$sql = "
SELECT *
FROM courses
WHERE id = ?
";

$course = DB::fetchOne($sql, [$courseId]);

if (!$course) {
    die("Học phần không tồn tại.");
}



if ($role == 'lecturer') {

    $sql = "
    SELECT id
    FROM courses_lecturers
    WHERE course_id = ?
    AND lecturer_id = ?
    ";

    $check = DB::fetchOne($sql, [
        $courseId,
        $userId
    ]);

    if (!$check) {
        die("Bạn không có quyền truy cập học phần này.");
    }
}

$sql = "
SELECT
    rs.id,
    rs.registration_session_name,
    rs.start_time,
    rs.end_time,
    MAX(ss.max_group) AS max_group,
    GROUP_CONCAT(
        CONCAT(s.section_code,' - ',s.section_name)
        ORDER BY s.section_code
        SEPARATOR '<br>'
    ) AS sections
FROM registration_sessions rs

LEFT JOIN sections_sessions ss
ON rs.id = ss.session_id

LEFT JOIN sections s
ON ss.section_id = s.id

WHERE rs.course_id = ?

GROUP BY rs.id

ORDER BY rs.created_at DESC
";

$registrationSessions = DB::fetchAll($sql, [$courseId]);


require_once __DIR__.'/../../../views/layouts/header-lecturer.php'; 
require_once __DIR__.'/../../../views/hoc-phan/chi-tiet-hoc-phan.php';
?>