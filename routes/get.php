<?php
use App\Controllers;
use Core\Router;

// * ROUTE SETUP
Router::get('', [new App\Controllers\HomeController, 'index']);
Router::get('home', [new App\Controllers\HomeController, 'index']);
Router::get('login', [new App\Controllers\LoginController, 'index']);
Router::get('register', [new App\Controllers\RegisterController, 'index']);
Router::get('courses', [new App\Controllers\CourseController, 'index']);
Router::get('courses/{courseId}/{courseTitle}', [new App\Controllers\CourseController, 'show']);
Router::get('logout', [new App\Controllers\LogOutController, 'index']);
Router::get('404', [new App\Controllers\Error404Controller, 'index']);
