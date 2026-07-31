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

    // 1. LẤY DANH SÁCH CÁC HỌC PHẦN ĐƯỢC PHÂN CÔNG CHO GIẢNG VIÊN NÀY
    $sqlMyCourses = "
        SELECT c.id, c.course_code, c.course_name
        FROM courses c
        INNER JOIN courses_lecturers cl ON c.id = cl.course_id
        WHERE cl.lecturer_id = ?
    ";
    $myCourses = DB::fetchAll($sqlMyCourses, [$userId]);

    // Nếu giảng viên chưa được phân công môn nào
    if (empty($myCourses)) {
        die("Bạn chưa được phân công quản lý học phần nào.");
    }

    // 2. XÁC ĐỊNH $courseId DỰA TRÊN THÔNG TIN ĐÃ TRUY VẤN
    // Ưu tiên lấy từ URL/POST, nếu không có thì lấy môn học ĐẦU TIÊN trong danh sách phân công
    $courseId = (int)($_GET['courseId'] ?? $_POST['courseId'] ?? $myCourses[0]['id']);

    // 3. KIỂM TRA XEM $courseId HIỆN TẠI CÓ HỢP LỆ VỚI GIẢNG VIÊN NÀY KHÔNG
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

    // 4. LẤY DANH SÁCH LỚP HỌC PHẦN THUỘC HỌC PHẦN ĐƯỢC CHỌN
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

        LEFT JOIN session_sections ss
        ON s.id = ss.section_id

        WHERE s.course_id = ?

        ORDER BY s.section_code
    ";
$sections = DB::fetchAll($sqlSections, [$courseId]);
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
            FROM session_sections
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
                session_name,
                description,
                max_groups,
                start_time,
                end_time
            )
            VALUES(?,?,?,?,?,?,?)";

            $sessionId = DB::insert(
                $sql,
                [
                    $courseId,
                    $userId,
                    $sessionName,
                    $description,
                    $maxGroups,
                    $startTime,
                    $endTime
                ]
            );
            var_dump($sessionId);

            foreach($selectedSections as $sectionId){
                $sql = "
                SELECT id
                FROM registration_sessions
                WHERE course_id = ?
                AND session_name = ?
                ";

                $exists = DB::fetchOne($sql, [
                    $courseId,
                    $sessionName
                ]);

                if ($exists) {
                    $errors[] = "Tên đợt đăng ký đã tồn tại.";
                }

                $sql = "
                INSERT INTO session_sections
                (
                    session_id,
                    section_id,
                    group_deadline,
                    topic_deadline
                )
                VALUES
                (?,?,?,?)";

                DB::execute(
                    $sql,
                    [
                        $sessionId,
                        $sectionId,
                        $groupDeadline,
                        $topicDeadline
                    ]
                );

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