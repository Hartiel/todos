<?php

namespace Database\Factories;


use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'color' => $this->faker->safeColorName,
            'favorite' => $this->faker->boolean,
            'user_id' => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}
