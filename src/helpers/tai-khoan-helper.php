<?php
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

function destroyUserSession(): void
{
    session_unset();
    session_destroy();
}
?>