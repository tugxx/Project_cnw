<?php
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

if (!isset($_SESSION['user'])) {
    header('Location: dang-nhap');
    exit;
}

$userId = $_SESSION['user']['id'];
$sql = "SELECT * 
        FROM `users` 
        WHERE `id` = ?";
$currentUser = DB::fetchOne($sql, [$userId]);

if (!$currentUser || !$currentUser["is_active"]) {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không tồn tại hoặc đã bị khoá.', 'dang-nhap', 'error');
}

if ($currentUser['role'] !== 'admin') {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không phải admin', 'dang-nhap', 'error');
}

$search    = trim($_GET['search'] ?? '');
$role      = trim($_GET['role'] ?? '');
$status    = $_GET['status'] ?? '';
$page_num  = max(1, (int)($_GET['page_num'] ?? 1));
$limit     = 10;

$sortBy    = $_GET['sort_by'] ?? 'created_at';
$sortOrder = strtolower($_GET['sort_order'] ?? 'desc');

$allowedSorts = ['full_name', 'username', 'email', 'created_at', 'role'];
if (!in_array($sortBy, $allowedSorts, true)) $sortBy = 'created_at';
if (!in_array($sortOrder, ['asc', 'desc'], true)) $sortOrder = 'desc';

$orderBySql = "{$sortBy} " . strtoupper($sortOrder);

$whereConditions = ["1=1"];
$params = [];

if ($search !== '') {
    $whereConditions[] = "(full_name LIKE ? OR username LIKE ? OR email LIKE ?)";
    $searchTerm = "%{$search}%";
    array_push($params, $searchTerm, $searchTerm, $searchTerm);
}

if ($role !== '') {
    $whereConditions[] = "role = ?";
    $params[] = $role;
}

if ($status !== '' && in_array($status, ['0', '1'], true)) {
    $whereConditions[] = "is_active = ?";
    $params[] = (int)$status;
}

$whereSql = implode(" AND ", $whereConditions);

$totalRecords = DB::fetchOne("SELECT COUNT(*) as total FROM users WHERE {$whereSql}", $params)['total'] ?? 0;
$totalPages   = max(1, (int)ceil($totalRecords / $limit));

if ($page_num > $totalPages) $page_num = $totalPages;
$offset = ($page_num - 1) * $limit;

$sqlListUsers = "SELECT id, username, email, role, full_name, class, dob, is_active, created_at 
                 FROM users 
                 WHERE {$whereSql} 
                 ORDER BY {$orderBySql} 
                 LIMIT {$limit} OFFSET {$offset}";

$users = DB::fetchAll($sqlListUsers, $params);

$queryParams = $_GET;
unset($queryParams['page'], $queryParams['page_num']);
$queryString = http_build_query($queryParams);

require_once __DIR__ . '/../../../views/layouts/header.php';
require_once __DIR__ . '/../../../views/tai-khoan/danh-sach-tai-khoan.php';