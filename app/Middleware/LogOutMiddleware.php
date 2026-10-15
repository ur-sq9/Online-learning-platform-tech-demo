<?php
namespace App\Middleware;

use App\Middleware\StorageMiddleware;
use App\Models\RememberTokens;
use App\Models\User;
use Core\Router;

class LogOutMiddleware
{
    private StorageMiddleware $storageMiddleware;
    private User $user;

    public function __construct()
    {
        $this->storageMiddleware = new StorageMiddleware();
        $this->user = new User();
    }

    public function logOut()
    {
        $userId = $_SESSION['user_id'];
        if (isset($userId)) {
            // echo 'logout user in process with cookie' . '<br>'; // ? DEBUG

            // * DELETING REMEMBER TOKENS IN DB
            $rememberToken = new RememberTokens();
            $rememberToken->deleteRememberToken($userId);

            // * IF COOKIE EXISTS - DELETE IT
            if (isset($_COOKIE['remember_me'])) {
                setcookie('remember_me', '', time() - 3600, '/');
            }

            // * LOGGING TO LOG FILE
            $user = $this->user->getUser($userId);
            $msg = '[' . $user['username'] . ']' . ' logged off';
            $this->storageMiddleware->storeLogs($msg);

            session_unset();
            session_destroy();

            Router::redirect('home');
        }
    }
}
