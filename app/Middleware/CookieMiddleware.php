<?php
namespace App\Middleware;

use App\Models\RememberTokens;
use DateTime;

class CookieMiddleware
{
    public function checkCookieExists()
    {
        if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
            $remeberToken = new RememberTokens();
            $row = $remeberToken->findRememberToken($_COOKIE['remember_me']);
            if ($row) {
                $expiry = new DateTime($row['expiry']);
                $currentTime = new DateTime();
                if ($currentTime < $expiry) {
                    $_SESSION['user_id'] = $row['user_id'];
                } else {
                    setcookie('remember_me', '', time() - 3600, '/');
                }
            }
        }
    }
}
