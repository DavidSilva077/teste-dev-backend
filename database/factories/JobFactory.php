<?php

namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(['clt', 'pj', 'freelancer']),
            'paused' => fake()->boolean(),
        ];
    }
}
