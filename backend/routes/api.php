<?php

use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\StudentController;

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {
    // Courses
    Route::get('courses/dashboard', [CourseController::class, 'dashboard'])->name('courses.dashboard');

    Route::get('courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('courses/{id}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('courses/{id}/students', [CourseController::class, 'students'])->name('courses.students');
    Route::put('courses/{id}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('courses/{id}', [CourseController::class, 'destroy'])->name('courses.destroy');
    Route::post('courses', [CourseController::class, 'store'])->name('courses.store');

    // Students
    Route::get('students', [StudentController::class, 'index'])->name('students.index');
    Route::get('students/{id}', [StudentController::class, 'show'])->name('students.show');
    Route::post('students', [StudentController::class, 'store'])->name('students.store');

    // Enrollments
    Route::post('enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::put('enrollments/{id}/progress', [EnrollmentController::class, 'updateProgress'])->name('enrollments.updateProgress');
});
