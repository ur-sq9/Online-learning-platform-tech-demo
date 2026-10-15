<?php
namespace App\Controllers;

use App\Middleware\ValidationMiddleware;
use Core\Controller;

class RegisterController extends Controller
{
    public function __construct()
    {
        $_SESSION['form-step'] = $_SESSION['form-step'] ?? 1;
        ;
        $_SESSION['form-steps'] = $_SESSION['form-steps'] ?? 3;
    }

    public function index()
    {
        // $stepsData = $this->getStepsData();

        // * TOKEN SHOULD ROTATE ON EVERY GET (PAGELOAD) REQUEST
        $token = $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // * RENDER ---------------------------------------------------------------
        $this->view('register', [
            'step' => $_SESSION['form-step'],
            'steps' => $_SESSION['form-steps'],
            'token' => $token
        ]);
    }

    public function store()
    {
        // die(var_dump($_POST, $_SESSION['csrf_token'])); // ? DEBUG

        // * POST ---------------------------------------------------------------
        $errors = [];
        // $stepsDataBefore = $this->getStepsData();  // BEFORE PAGE CHANGE
        // die(var_dump($stepsData));

        // echo ('TOKEN BEFORE POST BLOCK: ' . ($_SESSION['csrf_token'] ?? 'none')) . '<br>'; // ? DEBUG

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // echo ('TOKEN INSIDE POST (should be same as BEFORE): ' . ($_SESSION['csrf_token'] ?? 'none')) . '<br>'; // ? DEBUG
            $validationMiddleware = new ValidationMiddleware();
            $validationMiddleware->validation($_POST);
            $errors = $_SESSION['form-errors'];
        }

        // echo ('TOKEN AFTER POST BLOCK (still old): ' . ($_SESSION['csrf_token'] ?? 'none')) . '<br>'; // ? DEBUG

        // * CSRF TOKEN ---------------------------------------------------------------
        $token = $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // * STEPS DATA ---------------------------------------------------------------
        // $stepsDataAfter = $this->getStepsData();  // AFTER PAGE CHANGE

        // * RENDER ---------------------------------------------------------------
        // die(var_dump($_SESSION['form-data'])); // ? DEBUG
        $formData = $_SESSION['form-data'] ?? [];

        $this->view('register', [
            'step' => $_SESSION['form-step'],
            'steps' => $_SESSION['form-steps'],
            'errors' => $errors,
            'formData' => $formData,
            'token' => $token
        ]);
    }
}
