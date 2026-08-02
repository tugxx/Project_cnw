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

    if ($_SESSION['user']['role'] !== 'lecturer') {
        session_destroy();
        header("Location: dang-nhap");
        exit;
    }
    $sqlMyCourses = "
        SELECT c.id, c.course_code, c.course_name
        FROM courses c
        INNER JOIN courses_lecturers cl ON c.id = cl.course_id
        WHERE cl.lecturer_id = ?
    ";
    $myCourses = DB::fetchAll($sqlMyCourses, [$userId]);

    if (empty($myCourses)) {
        die("Bạn chưa được phân công quản lý học phần nào.");
    }

    $courseId = (int)($_GET['courseId'] ?? $_POST['courseId'] ?? $myCourses[0]['id']);


    $sqlCheckAssigned = "
        SELECT c.* 
        FROM courses c
        INNER JOIN courses_lecturers cl ON c.id = cl.course_id
        WHERE cl.course_id = ? AND cl.lecturer_id = ?
    ";
    $course = DB::fetchOne($sqlCheckAssigned, [$courseId, $userId]);

    if (!$course) {
        die("Bạn không được phân công quản lý học phần này.");
    }

    $sqlSections = "
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

        WHERE s.course_id = ?

        ORDER BY s.section_code
    ";
$sections = DB::fetchAll($sqlSections, [$courseId]);
    $sql = "
    SELECT
        t.id,
        t.topic_name
    FROM courses_topics ct
    INNER JOIN topics t
        ON ct.topic_id = t.id
    WHERE ct.course_id = ?
    ORDER BY t.topic_name
    ";

    $topics = DB::fetchAll($sql, [$courseId]);
    $errors = [];

    $sessionName = '';
    $description = '';
    $startTime = '';
    $endTime = '';
    $groupDeadline = '';
    $topicDeadline = '';
    $maxGroups = '';
    $selectedSections = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $sessionName = trim($_POST['session_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $startTime = $_POST['start_time'] ?? '';
        $endTime = $_POST['end_time'] ?? '';
        $groupDeadline = $_POST['group_deadline'] ?? '';
        $topicDeadline = $_POST['topic_deadline'] ?? '';
        $maxGroups = (int)($_POST['max_groups'] ?? 0);
        $selectedSections = $_POST['sections'] ?? [];
        $selectedTopics = $_POST['topics'] ?? [];
        if(empty($selectedTopics)){
            $errors[]="Phải chọn ít nhất một đề tài.";
        }
        if ($sessionName == '') {
            $errors[] = "Tên đợt đăng ký không được để trống.";
        }

        if (empty($selectedSections)) {
            $errors[] = "Phải chọn ít nhất một lớp học phần.";
        }

        if (!isset($_POST['max_groups']) || !is_numeric($_POST['max_groups']) || $maxGroups <= 0) {
            $errors[] = "Số lượng nhóm tối đa không hợp lệ.";
        }

        if ($startTime == '' || $endTime == '') {
            $errors[] = "Vui lòng nhập đầy đủ thời gian.";
        } else {

            if (strtotime($startTime) >= strtotime($endTime)) {
                $errors[] = "Thời gian bắt đầu phải nhỏ hơn thời gian kết thúc.";
            }

            if (strtotime($endTime) <= time()) {
                $errors[] = "Thời gian kết thúc phải lớn hơn thời gian hiện tại.";
            }
        }

        if ($groupDeadline == '') {
            $errors[] = "Vui lòng nhập hạn lập nhóm.";
        }

        if ($topicDeadline == '') {
            $errors[] = "Vui lòng nhập hạn chọn đề tài.";
        }
        foreach($selectedSections as $sectionId){

            $sql = "
            SELECT id
            FROM sections_sessions
            WHERE section_id = ?
            ";

            if(DB::fetchOne($sql,[$sectionId])){
                $errors[]="Lớp học phần đã thuộc đợt đăng ký khác.";
            }

        }

        if (
            $groupDeadline != '' &&
            $topicDeadline != '' &&
            $startTime != '' &&
            $endTime != ''
        ) {

            if (strtotime($groupDeadline) <= strtotime($startTime)) {
                $errors[] = "Hạn lập nhóm phải sau thời gian bắt đầu.";
            }

            if (strtotime($topicDeadline) <= strtotime($groupDeadline)) {
                $errors[] = "Hạn chọn đề tài phải sau hạn lập nhóm.";
            }

            if (strtotime($topicDeadline) >= strtotime($endTime)) {
                $errors[] = "Hạn chọn đề tài phải trước thời gian kết thúc.";
            }

        }
        if (empty($errors)) {
            echo "<pre>";
            print_r($errors);
            echo "</pre>";
            try {


                DB::beginTransaction();
            $sql = "
            INSERT INTO registration_sessions
            (
                course_id,
                lecturer_id,
                registration_session_name,
                description,
                start_time,
                end_time
            )
            VALUES(?,?,?,?,?,?)";

            $sessionId = DB::insert(
                $sql,
                [
                    $courseId,
                    $userId,
                    $sessionName,
                    $description,
                    $startTime,
                    $endTime
                ]
            );
            var_dump($sessionId);

            foreach($selectedSections as $sectionId){

                $sql = "
                INSERT INTO sections_sessions
                (
                    session_id,
                    section_id,
                    group_deadline,
                    topic_deadline
                )
                VALUES
                (?,?,?,?)
                ";

                $sectionSessionId = DB::insert(
                    $sql,
                    [
                        $sessionId,
                        $sectionId,
                        $groupDeadline,
                        $topicDeadline
                    ]
                );

                foreach($selectedTopics as $topicId){

                    $sql = "
                    INSERT INTO sections_sessions_topics
                    (
                        section_session_id,
                        topic_id
                    )
                    VALUES
                    (?,?)
                    ";

                    DB::execute(
                        $sql,
                        [
                            $sectionSessionId,
                            $topicId
                        ]
                    );

                }
            }

                DB::commit();

                header("Location: index.php?page=chi-tiet-hoc-phan&id=".$courseId);
                exit;

            } catch (Exception $e) {

                DB::rollBack();

                $errors[] = $e->getMessage();
                error_log($e->getMessage());
            }

        }

    }

    
    require_once __DIR__ .'/../../../views/dot-dang-ky/tao-dot-dang-ky.php';
?>