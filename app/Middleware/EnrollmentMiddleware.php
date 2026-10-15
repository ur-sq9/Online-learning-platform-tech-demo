<?php
namespace App\Middleware;

use App\Middleware\StorageMiddleware;
use App\Models\Courses;
use App\Models\Enrollments;
use App\Models\User;

class EnrollmentMiddleware
{
    private Enrollments $enrollment;
    private StorageMiddleware $storageMiddleware;
    private User $user;
    private Courses $course;
    private int $userId;
    private int $courseId;

    public function __construct(int $userId, ?int $courseId = 0)
    {
        $this->enrollment = new Enrollments();
        $this->storageMiddleware = new StorageMiddleware($userId, $courseId);
        $this->user = new User();
        $this->course = new Courses();
        $this->userId = $userId;
        $this->courseId = $courseId;
    }

    public function enrollmentStatus(): bool
    {
        $enrolled = $this->enrollment->getEnrollment($this->userId, $this->courseId);
        if ($enrolled) {
            return true;
        } else {
            return false;
        }
    }

    public function enroll(): void
    {
        $user = $this->user->getUser($this->userId);
        $course = $this->course->getCourse($this->courseId);
        $enroll = $this->enrollment->insertEnrollment($this->userId, $this->courseId);
        if ($enroll === 0) {  // INCASE THE TABLE DOESNT HAVE MAIN INSERT ID COLUMN. OTHERWISE LAST INSERT ID RETURN 0
            $msg = '[' . $user['username'] . '] has enrolled for ' . $course['title'];
            $this->storageMiddleware->storeLogs($msg);
        }
    }

    public function withdraw(): void
    {
        $user = $this->user->getUser($this->userId);
        $course = $this->course->getCourse($this->courseId);
        $witdraw = $this->enrollment->deleteEnrollment($this->userId, $this->courseId);
        if ($witdraw) {
            $msg = '[' . $user['username'] . '] has withdrawn from ' . $course['title'];
            $this->storageMiddleware->storeLogs($msg);
        }
    }
}
