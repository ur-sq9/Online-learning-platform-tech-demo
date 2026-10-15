<?php
namespace App\Controllers;

use App\Middleware\EnrollmentMiddleware;
use App\Middleware\PaginationMiddleware;
use App\Middleware\QueryMiddleware;
use App\Middleware\SanitizeMiddleware;
use App\Middleware\SortingMiddleware;
use App\Middleware\StorageMiddleware;
use App\Models\Courses;
use App\Models\Submits;
use App\Models\User;
use Core\Controller;

class CourseController extends Controller
{
    private Courses $courses;
    private User $user;
    private Submits $submits;

    public function __construct()
    {
        $this->courses = new Courses();
        $this->user = new User();
        $this->submits = new Submits();
    }

    public function index()
    {
        $userId = $_SESSION['user_id'];

        // * SORTING - DATA ---------------------------------------------------------------
        $sortingData = SortingMiddleware::getSortingData();
        [$sortColumn, $sortOrder] = $sortingData;

        // * QUERY - DATA ---------------------------------------------------------------
        $queryData = QueryMiddleware::getQueryData(['title', 'description'], 'BOOLEAN');
        [$query, $params, $match] = $queryData;

        // * TOTAL COURSES ---------------------------------------------------------------
        $totalCourses = $this->courses->getTotalCourses($params, $match);

        // * CURRENT PAGE ---------------------------------------------------------------
        $currentPage = isset($_GET['page']) ? (int) SanitizeMiddleware::sanitize($_GET['page'], 'url') : 1;

        // * SHOW ENROLLED ---------------------------------------------------------------
        $options = ['yes', 'no'];
        $enrolled = isset($_GET['show_enrolled']) && in_array($_GET['show_enrolled'], $options) ? SanitizeMiddleware::sanitize($_GET['show_enrolled'], 'url') : 'no';

        // * PAGINATION - DATA ---------------------------------------------------------------
        $paginationMiddleware = new PaginationMiddleware(6, $totalCourses, $currentPage);
        $paginationData = $paginationMiddleware->getPagination();
        $pagination = [$paginationData['limit'], $paginationData['offset']];

        // * VALID PAGES ---------------------------------------------------------------
        if ($currentPage < 1) {
            $currentPage = 1;
        } elseif ($currentPage > $paginationData['totalPages']) {
            $currentPage = $paginationData['totalPages'];
        }

        // * RENDER ---------------------------------------------------------------
        $allCourses = $this->courses->getCourses($params, $pagination, $match, $sortingData);

        // * GET SUBMISSIONS ---------------------------------------------------------------

        $hasSubmitted = [];
        foreach ($allCourses as $key => $course) {
            $hasSubmitted[$key] = $this->submits->getSubmits($userId, $course['id']);
        }

        $this->view('courses', [
            'courses' => $allCourses,
            'submitted' => $hasSubmitted,
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $paginationData['currentPage'],
            'sortColumn' => $sortColumn,
            'sortOrder' => $sortOrder,
            'query' => $query,
            'enrolled' => $enrolled
        ]);
    }

    public function show(int $courseId, string $courseTitle)  // string $courseTitle -> TEST
    {
        $userId = $_SESSION['user_id'];

        // * GET USERNAME ---------------------------------------------------------------
        $userData = $this->user->getUser($userId);
        $username = $userData['username'];

        // * GET COURSE ---------------------------------------------------------------
        $course = $this->courses->getCourse($courseId);
        $coursename = $course['title'];

        // * CHECK IF USER IS ENROLLED ---------------------------------------------------------------
        $enrollmentMiddleware = new EnrollmentMiddleware($userId, $courseId);
        $storageMiddleware = new StorageMiddleware($userId, $courseId);
        $submitsModel = new Submits();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // * PUT CSRF TOKEN ABOVE SO BELOW CODE DOESNT RUN - OTHERWISE USERS ACTIONS WILL STILL CARRY ON
            if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $msg = 'Invalid Enrollment Token';
                // $storageMiddleware->storeLogs($msg);
                die($msg);
                exit;
            }

            // * COURSE ENROLLMENT ---------------------------------------------------------------
            if (isset($_POST['enrol'])) {
                $enrollmentMiddleware->enroll();
            } elseif (isset($_POST['withdraw'])) {
                $enrollmentMiddleware->withdraw();
            }

            // * FILE HANDELING ---------------------------------------------------------------
            if (isset($_FILES['file'])) {
                $storeResult = $storageMiddleware->storeUploads($_FILES['file'], $courseTitle);
                if ($storeResult === 'no file') {
                    $storeResult = '⊗ You must select a file to upload.';
                    $msg = "{ERROR}:[$username] tried to submit with no file for $coursename";
                    $storageMiddleware->storeLogs($msg);
                } elseif ($storeResult === 'file too big') {
                    $storeResult = '⊗ File must not be bigger than 1MB.';
                    $msg = "{ERROR}:[$username] tried to submit a file exceeding the file size limit for $coursename";
                    $storageMiddleware->storeLogs($msg);
                } elseif ($storeResult === 'wrong type') {
                    $storeResult = '⊗ You can only upload pdf, docx, pptx, jpeg or png.';
                    $msg = "{ERROR}:[$username] has submitted a wrong file type for $coursename";
                    $storageMiddleware->storeLogs($msg);
                } else {
                    $msg = "[$username] has submitted their assigment for $coursename";
                    $storageMiddleware->storeLogs($msg);
                }
            }
        }

        // * TOKEN ROTATION ---------------------------------------------------------------
        $token = $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // * GET ENROLLED ---------------------------------------------------------------
        $enrolled = $enrollmentMiddleware->enrollmentStatus();

        // * GET SUBMISSIONS ---------------------------------------------------------------
        $hasSubmitted = $this->submits->getSubmits($userId, $courseId);

        // * RENDER ---------------------------------------------------------------

        // die(var_dump($course)); // ? DEBUG
        $this->view('course-page', [
            'course' => $course,
            'enrolled' => $enrolled,
            'error' => $storeResult ?? null,
            'hasSubmitted' => $hasSubmitted,
            'token' => $token
        ]);
    }
}
