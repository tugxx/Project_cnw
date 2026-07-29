<?php

$sql = "
SELECT *
FROM courses
ORDER BY course_id DESC
";

$courses = DB::fetchAll($sql);

require_once __DIR__.'/../../../views/hoc-phan/danh-sach-hoc-phan.php';
?>