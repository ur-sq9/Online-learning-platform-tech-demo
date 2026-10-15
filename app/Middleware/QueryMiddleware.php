<?php

namespace App\Middleware;

use App\Middleware\SanitizeMiddleware;

class QueryMiddleware
{
    /**
     * @param array $fulltext Which FULLTEXT column(s)
     * @param string|null $mode Which search mode. NATURAL MODE is default
     * @return void
     */
    public static function getQueryData(?array $fulltext = [], ?string $mode = 'NATURAL')
    {
        $query = isset($_GET['query']) ? (string) SanitizeMiddleware::sanitize($_GET['query'], 'url') : '';
        if (!empty($query)) {
            if ($mode === 'BOOLEAN') {
                $regex = '/[\*\+]/';
                if (!preg_match($regex, $query)) {
                    $query = "+$query*";
                }
            }

            $params = [$fulltext, $query, $mode];
            $match = true;
            return [$query, $params, $match];
        } else {
            $params = [];
            $match = false;
            return [$query, $params, $match];
        }
    }
}
