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

$role = $_SESSION['user']['role'];
$canCreate = ($role == 'admin' || $role == 'lecturer');

if ($role != 'admin' && $role != 'lecturer') {
    die("Bạn không có quyền truy cập.");
}

if ($role == 'admin') {

    $sql = "
    SELECT
        t.id,
        t.topic_name,
        t.description,
        t.status,
        t.created_at,
        t.created_by,

        c.id AS course_id,
        c.course_name,

        u.full_name AS lecturer_name

    FROM topics t

    INNER JOIN courses_topics ct
        ON t.id = ct.topic_id

    INNER JOIN courses c
        ON ct.course_id = c.id

    INNER JOIN users u
        ON t.created_by = u.id

    WHERE t.deleted_at IS NULL

    ORDER BY
        c.course_name,
        t.created_at DESC
    ";

    $topics = DB::fetchAll($sql);

} else {
    $courseId = $_GET["course_id"] ?? "";

    $sql = "
    SELECT
        t.id,
        t.topic_name,
        t.description,
        t.status,
        t.created_at,
        t.created_by,

        c.id AS course_id,
        c.course_name,

        u.full_name AS lecturer_name

    FROM topics t

    INNER JOIN courses_topics ct
        ON t.id = ct.topic_id

    INNER JOIN courses c
        ON ct.course_id = c.id

    INNER JOIN users u
        ON t.created_by = u.id

    LEFT JOIN courses_lecturers cl
        ON c.id = cl.course_id

    WHERE
        t.deleted_at IS NULL
    AND
    (
        t.created_by = ?
        OR
        (
            t.status = 'public'
            AND cl.lecturer_id = ?
        )
    )

    ORDER BY
        c.course_name,
        t.created_at DESC
    ";

    $topics = DB::fetchAll($sql, [
        $userId,
        $userId
    ]);
}


if ($_SESSION['user']['role'] === 'admin') {
    require_once __DIR__ . '/../../../views/layouts/header-admin.php';
} elseif ($_SESSION['user']['role'] === 'lecturer') {
    require_once __DIR__ . '/../../../views/layouts/header-lecturer.php';
} else {
    require_once __DIR__ . '/../../../views/layouts/header-student.php';
}
require_once __DIR__.'/../../../views/de-tai/danh-sach-de-tai.php';