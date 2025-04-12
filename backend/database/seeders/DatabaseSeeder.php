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

        // Crear 10 tareas importantes y urgentes
        Task::factory()->importantAndUrgent()->count(10)->create();

        // Crear 10 tareas importantes pero no urgentes
        Task::factory()->importantButNotUrgent()->count(10)->create();

        // Crear 10 tareas no importantes pero urgentes
        Task::factory()->notImportantButUrgent()->count(10)->create();

        // Crear 10 tareas no importantes y no urgentes
        Task::factory()->notImportantAndNotUrgent()->count(10)->create();
    }
}
