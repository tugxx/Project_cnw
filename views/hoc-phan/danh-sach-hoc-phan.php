<?php
require_once __DIR__.'/../layouts/header.php';
?>

<h2>Danh sách học phần</h2>

<div style="
display:grid;
grid-template-columns:repeat(auto-fill,minmax(320px,1fr));
gap:20px;
">

<?php

$sql = "
SELECT *
FROM courses
ORDER BY course_id DESC
";

$courses = DB::fetchAll($sql);

foreach($courses as $course){

    renderCourseCard($course);

}

?>

</div>

<?php
require_once __DIR__.'/../layouts/footer.php';
?>