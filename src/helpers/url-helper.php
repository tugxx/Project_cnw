<?php
function getSortUrl($columnName, $routeName = 'danh-sach-tai-khoan') {
    $currentSort  = $_GET['sort_by'] ?? '';
    $currentOrder = strtolower($_GET['sort_order'] ?? '');

    $params = $_GET;
    unset($params['page']);
    $params['page_num'] = 1; 
    $direction = 'none';

    if ($currentSort === $columnName) {
        if ($currentOrder === 'asc') {
            $params['sort_by']    = $columnName;
            $params['sort_order'] = 'desc';
            $direction            = 'asc';
        } else {
            $params['sort_by']    = $columnName;
            $params['sort_order'] = 'asc';
            $direction            = 'desc';
        }
    } else {
        $params['sort_by']    = $columnName;
        $params['sort_order'] = 'asc';
    }

    return [
        'url'       => $routeName . '?' . http_build_query($params),
        'direction' => $direction, 
        'active'    => ($currentSort === $columnName)
    ];
}

function renderSortIcon($direction) {
    if ($direction === 'asc') {
        return '<svg class="sort-icon active" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>';
    }
    
    if ($direction === 'desc') {
        return '<svg class="sort-icon active" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>';
    }

    return '<svg class="sort-icon idle" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/></svg>';
}
?>