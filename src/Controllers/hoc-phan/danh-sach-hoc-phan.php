<?php

$userId = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];

if ($role == 'admin') {

    $sql = "
    SELECT *
    FROM courses
    ORDER BY id DESC
    ";

    $courses = DB::fetchAll($sql);

} else {

    $sql = "
    SELECT c.*
    FROM courses c
    INNER JOIN courses_lecturers cl
        ON c.id = cl.course_id
    WHERE cl.lecturer_id = ?
    ORDER BY c.id DESC
    ";

    $courses = DB::fetchAll($sql, [$userId]);

}

require_once __DIR__.'/../../../views/hoc-phan/danh-sach-hoc-phan.php';
?>