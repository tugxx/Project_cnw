<?php
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

class DB {
    private static $pdo = null;

    public static function getConnection($config = null) {
        if (self::$pdo === null) {

            if ($config === null) {
                throw new Exception("Database chưa được khởi tạo.");
            }

            try {
                $dsn = "mysql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']};charset=utf8mb4";

                self::$pdo = new PDO(
                    $dsn,
                    $config['DB_USER'],
                    $config['DB_PASS'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );

            } catch (PDOException $e) {
                die("Lỗi kết nối CSDL: " . $e->getMessage());
            }
        }

        return self::$pdo;
    }

    public static function fetchAll($sql, $params = []) {
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function fetchOne($sql, $params = []) {
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public static function execute($sql, $params = []) {
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute($params);
    }
    public static function lastInsertId(){
        return self::$pdo->lastInsertId();
    }
}

?>