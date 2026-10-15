<?php
namespace App\Controllers;

use Core\Controller;
use Traits\Controllers\ResetRegisterFormTrait;

class HomeController extends Controller
{
    use ResetRegisterFormTrait;

    public function __construct()
    {
        $this->resetForm();
    }

    public function index()
    {
        $this->view('home');
    }
}
