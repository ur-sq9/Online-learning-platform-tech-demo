<?php
namespace App\Controllers;

use App\Middleware\ValidationMiddleware;
use App\Models\User;
use Core\Controller;
use Traits\Controllers\ResetRegisterFormTrait;

class LoginController extends Controller
{
    use ResetRegisterFormTrait;

    public function __construct()
    {
        $this->resetForm();
    }

    public function index()
    {
        // * TOKEN SHOULD ROTATE ON EVERY GET (PAGELOAD) REQUEST
        $token = $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $this->view('login', [
            'token' => $token
        ]);
    }

    public function store()
    {
        // * POST ---------------------------------------------------------------
        $errors = [];
        // die(var_dump($_POST)); // ? DEBUG
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validationMiddleware = new ValidationMiddleware();

            $result = $validationMiddleware->validation($_POST);
            [$loginData, $errors] = $result;
            // ? -> SAME AS $loginData = $result[0]
            // ? -> SAME AS $errors = $result[1]

            if (empty($errors)) {
                $user = new User();
                $data = [
                    'username_login' => $loginData['username_login'],
                    'password_login' => $loginData['password_login']
                ];

                // * TRY TO LOGIN USER
                $loginResult = $user->loginUser($data);

                if ($loginResult === false) {
                    $errors['login'] = '⊗ Username or password do not match/exist.';
                }
            }
        }

        // * CSRF TOKEN ---------------------------------------------------------------
        $token = $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        // die($loginData['username_login']); // ? DEBUG
        // * RENDER ---------------------------------------------------------------
        $this->view('login', [
            'username' => $loginData['username_login'] ?? '',
            'password' => $loginData['password_login'] ?? '',
            'errors' => $errors,
            'token' => $token
        ]);
    }
}
