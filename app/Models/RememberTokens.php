<?php
namespace App\Models;

use Core\Model;

class RememberTokens extends Model
{
    public function findRememberToken(string $token)
    {
        return $this->select(parent::TABLE['REMEMBER_TOKENS'], [['token', '=', hash('sha256', $token)]])->first();
    }

    public function insertRememberToken(int $user_Id, string $token, $expiry)
    {
        $params = [
            'user_id' => $user_Id,
            'token' => hash('sha256', $token),
            'expiry' => $expiry
        ];
        $this->insert(parent::TABLE['REMEMBER_TOKENS'], $params);
    }

    public function deleteRememberToken(int $userId)
    {
        $this->delete(parent::TABLE['REMEMBER_TOKENS'], [['user_id', '=', $userId]]);
    }
}
