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
$sortBy    = $_GET['sort_by'] ?? '';
$sortOrder = strtolower($_GET['sort_order'] ?? '');
 
$pageNum = (int)($_GET['page_num'] ?? 1);
$pageNum = max(1, $pageNum);               
 
$limit = 10;
$allowedSortColumns = ['full_name', 'username', 'email', 'created_at', 'role', 'user_code', 'class', 'dob'];
$allowedSortOrders  = ['asc', 'desc'];

$orderBySql = '';
if (in_array($sortBy, $allowedSortColumns)) {
    if (!in_array($sortOrder, $allowedSortOrders)) {
        $sortOrder = 'asc'; 
    }
    $orderBySql = "ORDER BY {$sortBy} " . strtoupper($sortOrder);
}

$searchableFields = [
    'user_code' => 'Mã',
    'full_name' => 'Họ và tên',
    'email'     => 'Email',
    'username'  => 'Tên đăng nhập',
    'class'     => 'Lớp',
];
$allowedSearchFields = array_keys($searchableFields);
$searchIn = $_GET['search_in'] ?? ['user_code', 'full_name', 'email'];
$searchIn = array_intersect($searchIn, $allowedSearchFields);

$whereConditions = ["1=1"];
$params = [];

if ($search !== '' && !empty($searchIn)) {
    $searchTerm = "%{$search}%";
    $searchConditions = [];
    foreach ($searchIn as $field) {
        $searchConditions[] = "$field LIKE ?";
        $params[] = $searchTerm;
    }
    $whereConditions[] = '(' . implode(' OR ', $searchConditions) . ')';
}

if ($role !== '') {
    $whereConditions[] = "role = ?";
    $params[] = $role;
}
if ($status !== '' && in_array($status, ['0', '1'], true)) {
    $whereConditions[] = "is_active = ?";
    $params[] = (int)$status;
}
$whereSql = implode(' AND ', $whereConditions);


$sqlCount = "
    SELECT COUNT(*) AS `total` 
    FROM `users` 
    WHERE {$whereSql}";
$countResult  = DB::fetchOne($sqlCount, $params);
$totalRecords = $countResult['total'] ?? 0;
$totalPages   = max(1, (int)ceil($totalRecords / $limit));

if ($pageNum > $totalPages) {
    $pageNum = $totalPages;
}
$offset = ($pageNum - 1) * $limit;

$sqlListUsers = "
    SELECT `id`, `username`, `email`, `role`, `full_name`, `class`, `dob`, `is_active`, `created_at`, user_code
    FROM `users`
    WHERE {$whereSql}
    {$orderBySql}
    LIMIT {$limit} OFFSET {$offset}
";
$users = DB::fetchAll($sqlListUsers, $params);

$allowedQueryKeys = ['search', 'role', 'status', 'sort_by', 'sort_order'];
$queryParams = [];
foreach ($allowedQueryKeys as $key) {
    if (isset($_GET[$key]) && $_GET[$key] !== '') {
        $queryParams[$key] = $_GET[$key];
    }
}
$queryString = http_build_query($queryParams);

require_once __DIR__ . '/../../../views/layouts/header.php';
require_once __DIR__ . '/../../../views/tai-khoan/danh-sach-tai-khoan.php';