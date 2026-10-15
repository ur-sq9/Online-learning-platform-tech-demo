<?php
namespace App\Controllers;

use App\Middleware\LogOutMiddleware;
use Core\Controller;

class LogOutController extends Controller
{
    private LogOutMiddleware $logOutMiddleware;

    public function __construct()
    {
        $this->logOutMiddleware = new LogOutMiddleware();
    }

    public function index()
    {
        // * LOGGIN USER OUT
        $this->logOutMiddleware->logOut();
    }
}
