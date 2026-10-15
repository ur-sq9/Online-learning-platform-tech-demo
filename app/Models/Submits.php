<?php
namespace App\Models;

use Core\Model;

class Submits extends Model
{
    public function insertSubmits(int $userId, int $courseId, string $filepath)
    {
        $params = [
            'user_id' => $userId,
            'course_id' => $courseId,
            'file_path' => $filepath
        ];
        $this->insert(parent::TABLE['SUBMITS'], $params);
    }

    public function getSubmits(int $userId, int $courseId)
    {
        $params = [
            ['user_id', '=', $userId],
            ['course_id', '=', $courseId]
        ];
        return $this->select(parent::TABLE['SUBMITS'], $params)->first();
    }
}
