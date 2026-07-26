<?php

function renderBadge($text, $color = 'blue') {
    $styles = [
        'blue'  => 'background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;',
        'green' => 'background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;',
        'gray'  => 'background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;'
    ];
    $style = $styles[$color] ?? $styles['blue'];

    echo "
    <span style='display: inline-block; font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 9999px; {$style}'>
        " . htmlspecialchars($text) . "
    </span>";
}

?>