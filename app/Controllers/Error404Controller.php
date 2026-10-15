<?php
namespace App\Controllers;

use Core\Controller;

class Error404Controller extends Controller
{
    public function index()
    {
        $this->view('404');
    }
}
