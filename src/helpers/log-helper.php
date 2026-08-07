<?php
function logActivity(
    int $userId,
    string $action,
    string $entityType,
    int $entityId,
    array $oldValue,
    array $newValue
): void {
    $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    if (str_contains($ipAddress, ',')) {
        $ipAddress = trim(explode(',', $ipAddress)[0]);
    }
    $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN', 0, 255);

    $sql = "INSERT INTO `logs` (
                `user_id`, 
                `action`, 
                `entity_type`, 
                `entity_id`, 
                `old_value`, 
                `new_value`, 
                `ip_address`, 
                `user_agent`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    try {
        DB::execute($sql, [
            $userId,
            strtoupper($action),
            strtolower($entityType),
            $entityId,
            $oldValue ? json_encode($oldValue, JSON_UNESCAPED_UNICODE) : null,
            $newValue ? json_encode($newValue, JSON_UNESCAPED_UNICODE) : null,
            $ipAddress,
            $userAgent
        ]);
    } catch (Exception $e) {
        error_log("Lỗi ghi log hệ thống: " . $e->getMessage());
    }
}