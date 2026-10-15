<?php
namespace App\Models;

use App\Middleware\StorageMiddleware;
use App\Models\RememberTokens;
use Core\Model;
use Core\Router;

class User extends Model
{
    public function totalUsers(): int
    {
        $totalUsers = $this->select(parent::TABLE['USERS'], [], 'COUNT(*)');
        return $totalUsers->column();
    }

    public function registerUser(array $data): int
    {
        $userData = [
            'username' => $data['username'],
            'name' => $data['full_name'],
            'email' => $data['email'],
            'birthday' => $data['birthdate'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT)
        ];
        return $this->insert(parent::TABLE['USERS'], $userData);
    }

    /**
     * Undocumented function
     *
     * @param array $data INPUTS: USERNAME AND PASSWORD
     * @return void
     */
    public function loginUser(array $data)
    {
        $storageMiddleware = new StorageMiddleware();
        $userData = [
            ['username', '=', $data['username_login'] ?? '']
        ];
        $userFound = $this->select(parent::TABLE['USERS'], $userData)->first();
        // die(var_dump($userFound)); // ?DEBUG

        if ($userFound && password_verify($data['password_login'] ?? '', $userFound['password_hash'])) {
            // die($userFound['username'] . ' FOUND');  // ? DEBUG
            if (isset($_POST['remember_me'])) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_me', $token, time() + 3600, '/');
                $expiry = date('Y-m-d H:i:s', time() + 3600);

                $rememberToken = new RememberTokens();
                $rememberToken->insertRememberToken($userFound['id'], $token, $expiry);
            }
            $_SESSION['user_id'] = $userFound['id'];
            $_SESSION['logged_username'] = $userFound['username'];

            $msg = '[' . $userFound['username'] . ']' . ' has logged in.';
            $storageMiddleware->storeLogs($msg);

            Router::redirect('home');
        } else {
            $msg = '{ERROR}: Login attempt failed.';
            $storageMiddleware->storeLogs($msg);
            return false;
        }
    }

    public function getUser(int $userId)
    {
        $params = [['id', '=', $userId]];
        return $this->select(parent::TABLE['USERS'], $params)->first();
    }
}
