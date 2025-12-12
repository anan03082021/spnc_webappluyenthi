<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class ClassroomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
public function definition()
{
    return [
        'name' => 'Lớp ' . $this->faker->randomElement(['10', '11', '12']) . $this->faker->randomElement(['A', 'B', 'C']) . $this->faker->numberBetween(1, 5),
        'code' => strtoupper($this->faker->bothify('CLASS-####')),
        'academic_year' => '2024 - 2025',
        'teacher_id' => 1,
    ];
}
}
