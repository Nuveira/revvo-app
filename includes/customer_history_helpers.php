<?php

const CUSTOMER_HISTORY_PER_PAGE = 10;

function normalize_history_page($page)
{
    if (!is_numeric($page)) {
        return 1;
    }

    $page = (int) $page;

    return $page > 0 ? $page : 1;
}

function history_offset_for_page($page)
{
    return (normalize_history_page($page) - 1) * CUSTOMER_HISTORY_PER_PAGE;
}

function normalize_history_motor_filter($motorId, array $ownedMotorIds)
{
    if ($motorId === null || $motorId === '' || !is_numeric($motorId)) {
        return null;
    }

    $motorId = (int) $motorId;

    return in_array($motorId, array_map('intval', $ownedMotorIds), true) ? $motorId : null;
}

function normalize_history_sort($sort)
{
    $allowedSorts = ['latest', 'motor_asc', 'motor_desc'];

    return in_array($sort, $allowedSorts, true) ? $sort : 'latest';
}

function history_total_pages($totalRows)
{
    $totalRows = max(0, (int) $totalRows);

    return max(1, (int) ceil($totalRows / CUSTOMER_HISTORY_PER_PAGE));
}

function history_status_label($status)
{
    return match ($status) {
        'ready_for_pickup' => 'SIAP DIAMBIL',
        'completed' => 'SELESAI',
        'in_progress' => 'DIKERJAKAN',
        'queued' => 'ANTRI',
        'cancelled' => 'DIBATALKAN',
        default => strtoupper((string) $status),
    };
}

function history_status_class($status)
{
    return match ($status) {
        'ready_for_pickup' => 'bg-green-50 text-green-700 border-green-200',
        'completed' => 'bg-blue-50 text-blue-700 border-blue-200',
        'in_progress' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
        'queued' => 'bg-gray-50 text-gray-700 border-gray-200',
        'cancelled' => 'bg-red-50 text-red-700 border-red-200',
        default => 'bg-gray-50 text-gray-500 border-gray-200',
    };
}
