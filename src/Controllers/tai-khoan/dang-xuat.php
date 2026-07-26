<?php 
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

if (isset($_SESSION['user'])) {
    destroyUserSession();
}

header('Location: dang-nhap');
exit;
?>
