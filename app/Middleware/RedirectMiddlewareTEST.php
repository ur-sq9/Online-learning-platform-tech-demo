<?php

namespace App\Middleware;

use App\Models, Core\Router;

class RedirectMiddleware
{
    // ROUTES THAT DONT REQUIRE SESSION CHECK. CHECK SESSION ONLY IF LOGGED IN
    private static array $noAuthRoutes = ['login', 'register'];

    public function __construct($currentUrl)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $totalUsers = call_user_func([new Models\User(), 'totalUsers']);
        // IF USER IS AT LOGIN OR REGISTER BUT NO USER FOUND IN DATABASE

        if (in_array($currentUrl, self::$noAuthRoutes)) {
            if ($totalUsers === 0 && $currentUrl !== 'register') {  // SO IF ROUTE IS LOGIN AND DB USERS = 0
                Router::redirect('register');
                return;  // EXITING EARLY SO SESSION CHECK DOESNT OCCUR
            }
            return;
        }

        // IF USER EXIST IN DATABASE BUT IS NOT LOGGED IN. THEN REDIRECT TO LOGIN PAGE
        if (!isset($_SESSION['user'])) {
            Router::redirect('login');
            return;
        }
    }
}
