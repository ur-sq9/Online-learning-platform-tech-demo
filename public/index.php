<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentUrl = trim($_SERVER['REQUEST_URI'], '/');
// echo 'Testing url: ' . $currentUrl . '<br>';  // ? DEBUG
// * DEFINING CONSTANTS
define('BASE_DIR', dirname(__DIR__));
require_once BASE_DIR . '/config/paths.php';
// * AUTOLOADING
require_once BASE_DIR . '/vendor/autoload.php';

// * CORE
use Core\Router;
// * USE
use App\Middleware\CookieMiddleware;

// * CHECKING IF COOKIE EXISTS BUT NO USER SESSION
$checkCookieMiddleware = new CookieMiddleware();
$checkCookieMiddleware->checkCookieExists();

// * ADDING ROUTES
require_once BASE_DIR . '/routes/get.php';
require_once BASE_DIR . '/routes/post.php';

Router::dispatch($currentUrl, $_SERVER['REQUEST_METHOD']);
