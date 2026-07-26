<?php
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

function forceLogout(): void
{
    if (isset($_SESSION['user']['id'])) {
        $sql = "UPDATE `users` 
                SET `remember_token` = NULL 
                WHERE `id` = ?";
        DB::execute($sql, [$_SESSION['user']['id']]);
    } elseif (isset($_COOKIE['remember_token'])) {
        $sql = "UPDATE `users` 
                SET `remember_token` = NULL 
                WHERE `remember_token` = ?";
        DB::execute($sql, [$_COOKIE['remember_token']]);
    }

    session_unset();
    session_destroy();

    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', [
            'expires' => time() - 3600,
            'path'    => '/'
        ]);
    }
}
?>