<?php
namespace App\Middleware;

use App\Middleware\SanitizeMiddleware;

class PaginationMiddleware
{
    private $limit;
    private $totalCourses;
    private $currentPage;

    /**
     * @param int $limit How many data per page show
     * @param int $totalCourses Total count data in table
     * @param int $currentPage Current page GET request
     */
    public function __construct(int $limit, int $totalCourses, int $currentPage)
    {
        $this->limit = SanitizeMiddleware::sanitize($limit, 'int');
        $this->totalCourses = SanitizeMiddleware::sanitize($totalCourses, 'int');
        $this->currentPage = SanitizeMiddleware::sanitize($currentPage, 'int');
    }

    public function getPagination()
    {
        $limit = $this->limit;
        $totalPages = ceil($this->totalCourses / $this->limit);
        $offset = ($this->currentPage - 1) * $this->limit;
        $currentPage = $this->currentPage;
        return [
            'limit' => $limit,
            'totalPages' => $totalPages,
            'offset' => $offset,
            'currentPage' => $currentPage
        ];
    }
}
