<?php
use Core\Router;

Router::post('register', [new App\Controllers\RegisterController, 'store']);
Router::post('login', [new App\Controllers\LoginController, 'store']);
Router::post('courses/{courseId}/{courseTitle}', [new App\Controllers\CourseController, 'show']);
