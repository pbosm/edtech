<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->unsignedInteger('progress_percentage')->default(0);
            $table->date('enrollment_date');
            $table->date('completion_date')->nullable();

            $table->unique(['student_id', 'course_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
