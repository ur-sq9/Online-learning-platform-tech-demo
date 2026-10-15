<?php
namespace App\Middleware;

use App\Middleware\SanitizeMiddleware;
use App\Models\User;
use App\Models;
use Core\Router;

class ValidationMiddleware
{
    // public static array $errors = [];
    private string $currentUrl;

    /**
     * @param array $post POST ARRAY
     * @param array $stepsData INSERT STEPS DATA
     */
    public function validation(array $post, ?array $stepsData = [])
    {
        // * SETTING UP  ---------------------------------------------------------------

        $errors = [];
        $loginData = [];
        $LoginErrors = [];
        $url = trim($_SERVER['REQUEST_URI'], '/');

        if (!isset($_SESSION['form-data'])) {
            $_SESSION['form-data'] = [];
        }

        // * URL  ---------------------------------------------------------------
        if ($url === 'register' || $url === 'login') {
            $this->currentUrl = $url;
        }

        // * LOOPING THROUGH POST ---------------------------------------------------------------
        foreach ($post as $key => $value) {
            $passedEmpty[$key] = true;

            // * IF EMPTY  ---------------------------------------------------------------

            if ($this->currentUrl === 'register') {
                if (empty($value) && $key !== 'agree' && $key !== 'username_login' && $key !== 'password_login') {
                    $msg = '⊗ You must fill in ' . str_replace('_', ' ', $key) . '.';
                    $errors[$key] = $msg;
                    $passedEmpty[$key] = false;
                    $_SESSION['form-data'][$key] = '';

                    // continue;  // if empty then this $key should be skipped from further validation (as this needs filling in first), otherwise it just checks for further code
                    // if not empty. Continue validation
                    // HOWEVER, causes issues when input is initialy not empty (filled in) and then deleted. It still maintains the previous answer as it skips code below to change value
                    // SO Passed empty system implemented
                }

                // * PERSONALIZED  ---------------------------------------------------------------

                if ($_SESSION['form-step'] === 1) {
                    if (($key === 'username') && $passedEmpty[$key] === true) {
                        $_SESSION['form-data'][$key] = SanitizeMiddleware::sanitize($value, 'string');
                    }
                    if ($key === 'email' && $passedEmpty[$key] === true) {
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$key] = '⊗ Invalid email format';
                            $_SESSION['form-data'][$key] = SanitizeMiddleware::sanitize($value, 'email');
                        } else {
                            $_SESSION['form-data'][$key] = SanitizeMiddleware::sanitize($value, 'email');
                        }
                    }
                } elseif ($_SESSION['form-step'] === 2) {
                    if ($key === 'full_name') {
                        $_SESSION['form-data'][$key] = SanitizeMiddleware::sanitize($value, 'string');
                    }

                    if ($key === 'birthdate' && $passedEmpty[$key] === true) {
                        $_SESSION['form-data'][$key] = date($value);
                    }
                } elseif ($_SESSION['form-step'] === 3) {
                    if ($key === 'password' && $passedEmpty[$key] === true) {
                        if (strlen($value) < 4) {
                            $errors[$key] = '⊗ Password must be atleast 4 characters long.';
                        } else {
                            $_SESSION['form-data'][$key] = $value;  // ? PASSWORDS DO NOT GO THROUGH SANITIZING (they should be hashed before storage)
                        }
                    }
                    if ($key === 'password_confirmation' && $passedEmpty[$key] === true) {
                        if (!isset($_SESSION['form-data']['password']) || $value !== $_SESSION['form-data']['password']) {
                            $errors[$key] = '⊗ Passwords do not match';
                        } else {
                            $_SESSION['form-data'][$key] = $value;  // ? PASSWORDS DO NOT GO THROUGH SANITIZING (they should be hashed before storage)
                        }
                    }
                    // * CHECKBOX  ---------------------------------------------------------------
                    if (!isset($post['agree'])) {
                        // IF NOT CHECKED -> NO VALUE -> MUST CREATE EMPTY FORM DATA FOR 'AGREE' TO RESET FORM DATA
                        $msg = '⊗ You must agree with Terms and Conditions';
                        $errors['agree'] = $msg;
                        $_SESSION['form-data']['agree'] = '';
                    } elseif (isset($post['agree'])) {
                        // IF CHECKED -> ADD VALUE TO 'AGREE'
                        $_SESSION['form-data']['agree'] = $value;
                    }
                }
            } elseif ($this->currentUrl === 'login') {
                if (empty($value)) {
                    $regex = '/_(login)/';
                    $msg = '⊗ You must fill in ' . preg_replace($regex, '', $key) . '.';
                    $LoginErrors[$key] = $msg;
                    $passedEmpty[$key] = false;
                }

                if (($key === 'username_login') && $passedEmpty[$key] === true) {
                    $loginData[$key] = SanitizeMiddleware::sanitize($value, 'string');
                } elseif ($key === 'password_login' && $passedEmpty[$key] === true) {
                    $loginData[$key] = $value;  // ? PASSWORDS DO NOT GO THROUGH SANITIZING (they should be hashed before storage)
                } elseif ($key === 'remember_me') {
                    $loginData[$key] = $value;
                }
            }
        }
        // * ALL ERRORS AFTER FOREACH LOOP ENDED ---------------------------------------------------------------

        $_SESSION['form-errors'] = $errors;
        // die(print_r($_SESSION['form-errors'])); // ? DEBUG
        $loginSession = [$loginData, $LoginErrors];

        // * CSRF VALIDATION ---------------------------------------------------------------
        if (empty($errors) && ($post['csrf_token'] !== $_SESSION['csrf_token']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            echo 'ERROR IF TOKEN DOESNT MATCH :' . 'POST: ' . $_POST['csrf_token'] . '<br>' . 'SESSION TOKEN: ' . $_SESSION['csrf_token'] . '<br>';  // ? DEBUG
            die();
        }

        // * PAGE NAVIGATION VALIDATION - REGISTER ---------------------------------------------------------------
        if ($this->currentUrl === 'register') {
            if (isset($post['back']) && ($_SESSION['form-step'] > 1)) {
                $_SESSION['form-step'] = $_SESSION['form-step'] - 1;
            }

            if (empty($errors) && isset($post['submit_final']) && ($_SESSION['form-step'] === $_SESSION['form-steps'])) {
                // * ADD TO DB
                $newUser = new User();
                $newUser->registerUser($_SESSION['form-data']);
                // * LOG
                $storageMiddleware = new StorageMiddleware();
                $msg = '[NEW USER: ' . $_SESSION['form-data']['username'] . ']' . ' registered';
                $storageMiddleware->storeLogs($msg);
                unset(
                    $_SESSION['form-data'],
                    $_SESSION['errors'],
                    $_SESSION['form-step'],
                    $_SESSION['form-steps']
                );
                Router::redirect('login');
            }

            if (empty($errors) && isset($post['next']) && ($_SESSION['form-step'] < $_SESSION['form-steps'])) {
                $_SESSION['form-step'] = $_SESSION['form-step'] + 1;
            }
        }
        // * USER DB CHECK - LOGIN ---------------------------------------------------------------
        if ($this->currentUrl === 'login') {
            if (!empty($loginSession)) {
                return $loginSession;
            }
        }
    }
}
