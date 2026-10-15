<?php
namespace App\Models;

use Core\Model;

class Courses extends Model
{
    public function getCourses(?array $params = [], ?array $pagination = [], ?bool $match = false, ?array $orderBy = [])
    {
        return $this->select(parent::TABLE['COURSES'], $params, '*', $match, $orderBy, $pagination)->get();
    }

    public function getTotalCourses(?array $params = [], ?bool $match = false)
    {
        return $this->select(parent::TABLE['COURSES'], $params, 'COUNT(*)', $match)->column();
    }

    public function getCourse(int $id)
    {
        return $this->select(parent::TABLE['COURSES'], [['id', '=', $id]])->first();
    }
}
