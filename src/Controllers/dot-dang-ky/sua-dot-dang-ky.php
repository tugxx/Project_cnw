<?php

$sessionId = (int)($_GET['session_id'] ?? 0);

if ($sessionId <= 0) {
    die("Đợt đăng ký không tồn tại.");
}

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

if ($_SESSION['user']['role'] !== 'lecturer') {
    session_destroy();
    header("Location: dang-nhap");
    exit;
}


$sql = "
SELECT *
FROM registration_sessions
WHERE id = ?
";

$session = DB::fetchOne($sql, [$sessionId]);

if (!$session) {
    die("Đợt đăng ký không tồn tại.");
}

$courseId = $session['course_id'];

$sql = "
SELECT id
FROM courses_lecturers
WHERE course_id = ?
AND lecturer_id = ?
";

if (!DB::fetchOne($sql, [$courseId, $userId])) {
    die("Bạn không có quyền.");
}

$sql = "
SELECT
    s.id,
    s.section_code,
    s.section_name,
    CASE
        WHEN ss.section_id IS NULL THEN 0
        ELSE 1
    END AS is_used
FROM sections s

LEFT JOIN sections_sessions ss
ON s.id = ss.section_id

WHERE s.course_id=?

ORDER BY s.section_code
";

$sections = DB::fetchAll($sql, [$courseId]);


$sql = "
SELECT
section_id,
group_deadline,
topic_deadline
FROM sections_sessions
WHERE session_id=?
";

$sessionSections = DB::fetchAll($sql, [$sessionId]);

$selectedSections=[];

foreach($sessionSections as $item){
    $selectedSections[]=$item['section_id'];
}


$sessionName=$session['registration_session_name'];
$description=$session['description'];
$startTime = !empty($session['start_time']) ? date('Y-m-d\TH:i', strtotime($session['start_time'])) : '';
$endTime = !empty($session['end_time'])   ? date('Y-m-d\TH:i', strtotime($session['end_time']))   : '';
$maxGroup = $session['max_group'] ?? '';

$groupDeadline='';
$topicDeadline='';

if(!empty($sessionSections)){
    $firstSection  = $sessionSections[0] ?? [];
    $groupDeadline = !empty($firstSection['group_deadline']) ? date('Y-m-d\TH:i', strtotime($firstSection['group_deadline'])) : '';
    $topicDeadline = !empty($firstSection['topic_deadline']) ? date('Y-m-d\TH:i', strtotime($firstSection['topic_deadline'])) : '';
}

$errors=[];


if($_SERVER['REQUEST_METHOD']=="POST"){

    $sessionName=trim($_POST['registration_session_name']);
    $description=trim($_POST['description']);
    $startTime=$_POST['start_time'];
    $endTime=$_POST['end_time'];
    $groupDeadline=$_POST['group_deadline'];
    $topicDeadline=$_POST['topic_deadline'];
    $maxGroup=(int)$_POST['max_group'];
    $selectedSections=$_POST['sections']??[];

    /*
    Validation
    */

    if($sessionName==''){
        $errors[]="Tên đợt đăng ký không được để trống.";
    }

    if(empty($selectedSections)){
        $errors[]="Phải chọn ít nhất một lớp.";
    }

    if($maxGroup<=0){
        $errors[]="Số nhóm tối đa không hợp lệ.";
    }

    if(strtotime($startTime)>=strtotime($endTime)){
        $errors[]="Thời gian không hợp lệ.";
    }

    if(strtotime($groupDeadline)<=strtotime($startTime)){
        $errors[]="Hạn lập nhóm không hợp lệ.";
    }

    if(strtotime($topicDeadline)<=strtotime($groupDeadline)){
        $errors[]="Hạn chọn đề tài không hợp lệ.";
    }

    /*
    Không trùng tên
    */

    $sql="
    SELECT id
    FROM registration_sessions
    WHERE course_id=?
    AND registration_session_name=?
    AND id<>?
    ";

    if(DB::fetchOne($sql,[
        $courseId,
        $sessionName,
        $sessionId
    ])){
        $errors[]="Tên đợt đăng ký đã tồn tại.";
    }

    /*
    Không dùng lớp của đợt khác
    */

    foreach($selectedSections as $sectionId){

        $sql="
        SELECT id
        FROM sections_sessions
        WHERE section_id=?
        AND session_id<>?
        ";

        if(DB::fetchOne($sql,[
            $sectionId,
            $sessionId
        ])){
            $errors[]="Lớp học phần đã thuộc đợt khác.";
        }

    }

    /*
    UPDATE
    */

    if(empty($errors)){

        try{

            DB::beginTransaction();

            $sql="
            UPDATE registration_sessions
            SET
                registration_session_name=?,
                description=?,
                max_group=?,
                start_time=?,
                end_time=?
            WHERE id=?
            ";

            DB::execute($sql,[
                $sessionName,
                $description,
                $maxGroup,
                $startTime,
                $endTime,
                $sessionId
            ]);

            /*
            Xóa lớp cũ
            */

            DB::execute(
                "DELETE FROM sections_sessions WHERE session_id=?",
                [$sessionId]
            );

            /*
            Thêm lại
            */

            foreach($selectedSections as $sectionId){

                DB::execute(
                    "
                    INSERT INTO sections_sessions
                    (
                        session_id,
                        section_id,
                        group_deadline,
                        topic_deadline
                    )
                    VALUES(?,?,?,?)
                    ",
                    [
                        $sessionId,
                        $sectionId,
                        $groupDeadline,
                        $topicDeadline
                    ]
                );

            }

            DB::commit();

            header("Location:index.php?page=chi-tiet-hoc-phan&id=".$courseId);
            exit;

        }catch(Exception $e){

            DB::rollBack();

            $errors[]=$e->getMessage();

        }

    }

}

require_once __DIR__.'/../../../views/dot-dang-ky/sua-dot-dang-ky.php';