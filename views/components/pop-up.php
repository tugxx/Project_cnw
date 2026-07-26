<?php
function showPopUp(string $message, string $redirect, string $type = 'success'): void
{
    $bgColor = $type === 'success' ? '#22c55e' : '#ef4444';
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>Thông báo</title>
        <style>
            body {
                margin: 0; height: 100vh; display: flex;
                align-items: center; justify-content: center;
                background: #f3f4f6; font-family: sans-serif;
            }
            .popup {
                background: #fff; padding: 24px 32px; border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0,0,0,.15); text-align: center;
                max-width: 360px;
            }
            .popup .icon {
                width: 48px; height: 48px; border-radius: 50%;
                background: <?= $bgColor ?>; margin: 0 auto 16px;
                display: flex; align-items: center; justify-content: center;
                color: #fff; font-size: 24px;
            }
            .popup button {
                margin-top: 16px; padding: 8px 24px; border: none;
                border-radius: 6px; background: <?= $bgColor ?>;
                color: #fff; cursor: pointer; font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="popup">
            <div class="icon"><?= $type === 'success' ? '✓' : '✕' ?></div>
            <p><?= htmlspecialchars($message) ?></p>
            <a class="btn" href="<?= htmlspecialchars($redirect) ?>">Thoát</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}


?>