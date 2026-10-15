<?php
namespace App\Models;

use Core\Model;

class Enrollments extends Model
{
    private function paramConditions(int $userId, int $courseId, ?int $mode = 1)
    {
        if ($mode === 1) {
            $params = [
                ['user_id', '=', $userId],
                ['course_id', '=', $courseId]
            ];
        } elseif ($mode === 2) {
            $params = [
                'user_id' => $userId,
                'course_id' => $courseId,
            ];
        }

        return $params;
    }

    public function getEnrollment(int $userId, int $courseId): array|false|null
    {
        $params = $this->paramConditions($userId, $courseId);
        return $this->select(parent::TABLE['ENROLLMENTS'], $params)->first();
    }

    public function insertEnrollment(int $userId, int $courseId)
    {
        $params = $this->paramConditions($userId, $courseId, 2);

        return $this->insert(parent::TABLE['ENROLLMENTS'], $params);
    }

    public function deleteEnrollment(int $userId, int $courseId)
    {
        $params = $this->paramConditions($userId, $courseId);
        return $this->delete(parent::TABLE['ENROLLMENTS'], $params);
    }
}
