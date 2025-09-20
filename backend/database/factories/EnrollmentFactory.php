<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start          = fake()->dateTimeBetween('-6 months', 'now');
        $maybeCompleted = fake()->boolean(40);
        $completion     = $maybeCompleted ? fake()->dateTimeBetween($start, 'now') : null;

        return [
            'student_id' => Student::inRandomOrder()->value('id') ?? Student::factory(),
            'course_id'  => Course::inRandomOrder()->value('id') ?? Course::factory(),
            'progress_percentage' => $completion ? 100 : fake()->numberBetween(0, 99),
            'enrollment_date' => $start->format('Y-m-d'),
            'completion_date' => $completion ? $completion->format('Y-m-d') : null,
        ];
    }
}
