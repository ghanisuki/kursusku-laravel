<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('???-###')),
            'name' => 'Kursus ' . fake()->words(2, true),
            'description' => 'Data latihan KursusKu.',
            'fee' => 400000,
            'quota' => 20,
            'start_date' => now()->addDays(14)->toDateString(),
            'status' => 'open',
        ];
    }
}
