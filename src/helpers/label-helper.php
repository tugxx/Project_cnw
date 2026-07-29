<?php
function getMultiselectLabel(array $selected, array $all): string {
    $total = count($all);
    $count = count($selected);

    if ($count === 0) return '-- Chọn trường --';
    if ($count === $total) return 'Tất cả';
    if ($count === 1) return $all[$selected[0]] ?? $selected[0];
    return "Đã chọn {$count} trường";
}
?>