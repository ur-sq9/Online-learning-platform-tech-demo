<?php
namespace App\Middleware;

use App\Middleware\SanitizeMiddleware;

class SortingMiddleware
{
    public static function getSortingData()
    {
        $allowedColumns = ['title', 'popularity', 'created_at'];
        $allowedOrders = ['asc', 'desc'];
        $sortColumn = isset($_GET['sort']) && in_array(SanitizeMiddleware::sanitize($_GET['sort'], 'url'), $allowedColumns) ? (string) SanitizeMiddleware::sanitize($_GET['sort'], 'url') : 'title';;
        $sortOrder = isset($_GET['order']) && in_array(SanitizeMiddleware::sanitize($_GET['order'], 'url'), $allowedOrders) ? (string) SanitizeMiddleware::sanitize($_GET['order'], 'url') : 'asc';;
        return ["$sortColumn", $sortOrder];
    }
}
