<?php 
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

$envPath = __DIR__ . '/../.env.php';
$env = file_exists($envPath) ? parse_ini_file($envPath) : [];
return $env;
?>