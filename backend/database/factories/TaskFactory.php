<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'important' => $this->faker->boolean(),
            'urgent' => $this->faker->boolean(),
        ];
    }

    public function importantAndUrgent()
    {
        return $this->state([
            'important' => true,
            'urgent' => true,
        ]);
    }

    public function importantButNotUrgent()
    {
        return $this->state([
            'important' => true,
            'urgent' => false,
        ]);
    }

    public function notImportantButUrgent()
    {
        return $this->state([
            'important' => false,
            'urgent' => true,
        ]);
    }

    public function notImportantAndNotUrgent()
    {
        return $this->state([
            'important' => false,
            'urgent' => false,
        ]);
    }
}
