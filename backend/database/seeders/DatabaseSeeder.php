<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // base
        $courses  = Course::factory()->count(10)->create();
        $students = Student::factory()->count(50)->create();

        // matrículas (garantindo par único student-curso)
        $pairs = [];
        $toCreate = 120;

        while (count($pairs) < $toCreate) {
            $s = $students->random()->id;
            $c = $courses->random()->id;
            $key = "$s:$c";

            if (isset($pairs[$key]))
                continue;

            $pairs[$key] = true;

            Enrollment::factory()->create([
                'student_id' => $s,
                'course_id'  => $c,
            ]);
        }
    }
}
