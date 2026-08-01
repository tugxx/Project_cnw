<?php

if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit();
}


/**
 * Kiểm tra tên học phần đã tồn tại chưa
 */
function courseNameExists($courseName)
{
    $sql = "
        SELECT id
        FROM courses
        WHERE course_name = ?
        LIMIT 1
    ";

    return DB::fetchOne($sql, [$courseName]);
}

/**
 * Sinh mã học phần
 */
function generateCourseCode()
{
    $sql = "
        SELECT id
        FROM courses
        ORDER BY id DESC
        LIMIT 1
    ";

    $course = DB::fetchOne($sql);

    if (!$course) {
        return 'HP0001';
    }

    $next = $course['id'] + 1;

    return 'HP' . str_pad($next, 4, '0', STR_PAD_LEFT);
}

/**
 * Kiểm tra tài khoản có tồn tại và còn hoạt động hay không
 *
 * @param int $userId
 * @return bool
 */
function isUserActive($userId)
{
    $sql = "
        SELECT is_active
        FROM users
        WHERE id = ?
        LIMIT 1
    ";

    $user = DB::fetchOne($sql, [$userId]);

    if (!$user) {
        return false;
    }

    return (bool)$user['is_active'];
}

/**
 * Kiểm tra người dùng có phải Admin hay không
 *
 * @return bool
 */
function isAdmin()
{
    return isset($_SESSION['user'])
        && $_SESSION['user']['role'] === 'admin';
}
/**
 * Kiểm tra người dùng đã đăng nhập hay chưa
 *
 * @return bool
 */
function isLoggedIn()
{
    return isset($_SESSION['user']);
}