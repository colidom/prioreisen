<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Crear Tareas
        Task::factory()->importantAndUrgent()->count(3)->create();
        Task::factory()->importantButNotUrgent()->count(3)->create();
        Task::factory()->notImportantButUrgent()->count(3)->create();
        Task::factory()->notImportantAndNotUrgent()->count(3)->create();
    }
}
