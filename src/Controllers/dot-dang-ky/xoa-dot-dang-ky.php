<?php

$sessionId = (int)($_GET['id'] ?? 0);

if ($sessionId <= 0) {
    die("Đợt đăng ký không tồn tại.");
}

if (!isLoggedIn()) {
    header("Location:dang-nhap");
    exit;
}

$userId = $_SESSION['user']['id'];

if (!isUserActive($userId)) {
    session_destroy();
    header("Location:dang-nhap");
    exit;
}

if ($_SESSION['user']['role'] != 'lecturer') {
    die("Không có quyền.");
}

/*
Lấy thông tin đợt đăng ký
*/

$sql="
SELECT *
FROM registration_sessions
WHERE id=?
";

$session=DB::fetchOne($sql,[$sessionId]);

if(!$session){
    die("Đợt đăng ký không tồn tại.");
}

$courseId=$session['course_id'];

/*
Kiểm tra giảng viên có quyền
*/

$sql="
SELECT id
FROM courses_lecturers
WHERE course_id=?
AND lecturer_id=?
";

if(!DB::fetchOne($sql,[
    $courseId,
    $userId
])){
    die("Bạn không có quyền.");
}

try{

    DB::beginTransaction();

    /*
    Xóa các lớp áp dụng
    */

    DB::execute(
        "
        DELETE FROM session_sections
        WHERE session_id=?
        ",
        [$sessionId]
    );

    /*
    Xóa đợt đăng ký
    */

    DB::execute(
        "
        DELETE FROM registration_sessions
        WHERE id=?
        ",
        [$sessionId]
    );

    DB::commit();

    header("Location:index.php?page=chi-tiet-hoc-phan&id=".$courseId);
    exit;

}catch(Exception $e){

    DB::rollBack();

    die($e->getMessage());

}