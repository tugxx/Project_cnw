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

    $courseId = (int)($_GET['course_id'] ?? $_POST['courseId'] ?? $myCourses[0]['id']);


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
        t.topic_name,
        t.description
    FROM courses_topics ct
    INNER JOIN topics t
        ON ct.topic_id = t.id
    WHERE ct.course_id = ? AND t.deleted_at IS NULL
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
    $maxGroup = 1;
    $minMember = 1;
    $maxMember = 5;
    $maxGroupPerTopic = 1;
    $selectedSections = [];
    $selectedTopics = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $sessionName = trim($_POST['session_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $startTime = $_POST['start_time'] ?? '';
        $endTime = $_POST['end_time'] ?? '';
        $groupDeadline = $_POST['group_deadline'] ?? '';
        $topicDeadline = $_POST['topic_deadline'] ?? '';
        $maxGroup = (int)($_POST['max_group'] ?? 0);
        $minMember = (int)($_POST['min_member'] ?? 1);
        $maxMember = (int)($_POST['max_member'] ?? 1);
        $maxGroupPerTopic = (int)($_POST['max_group_per_topic'] ?? 1);

        $selectedSections = $_POST['sections'] ?? [];
        $selectedTopics = $_POST['topics'] ?? [];
        
        if ($sessionName == '') {
            $errors[] = "Tên đợt đăng ký không được để trống.";
        }

        if ($maxGroup <= 0) {
            $errors[] = "Số lượng nhóm tối đa cho mỗi lớp phải lớn hơn 0.";
        }

        if ($minMember <= 0 || $maxMember < $minMember) {
            $errors[] = "Số thành viên tối thiểu/tối đa của nhóm không hợp lệ.";
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
            try {


                DB::beginTransaction();
            $sql = "
            INSERT INTO registration_sessions
            (course_id, registration_session_name, description, start_time, end_time)
            VALUES (?, ?, ?, ?, ?)
            ";

            $sessionId = DB::insert($sql, [
                $courseId,
                $sessionName,
                $description,
                $startTime,
                $endTime
            ]);

            if (!empty($selectedSections)) {
                foreach($selectedSections as $sectionId){

                    $sql = "
                    INSERT INTO sections_sessions
                    (section_id, session_id, group_deadline, topic_deadline, max_group)
                    VALUES (?, ?, ?, ?, ?)
                    ";

                    $sectionSessionId = DB::insert($sql, [
                        $sectionId,
                        $sessionId,
                        $groupDeadline,
                        $topicDeadline,
                        $maxGroup
                    ]);

                    if (!empty($selectedTopics)) {
                        foreach($selectedTopics as $topicId){

                            $sql = "
                            INSERT INTO sections_sessions_topics
                                (section_session_id, topic_id, min_member, max_member, max_group)
                                VALUES (?, ?, ?, ?, ?)
                            ";
                            DB::execute($sql, [
                                $sectionSessionId,
                                $topicId,
                                $minMember,
                                $maxMember,
                                $maxGroupPerTopic
                            ]);
                        }
                    }
                }
            }
            DB::commit();

            header("Location: /Project_cnw/danh-sach-lop-hoc-phan&course_id=".$courseId);
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