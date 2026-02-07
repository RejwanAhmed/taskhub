<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some specific tasks
        $homepageTask = Task::where('title', 'Design homepage mockup')->first();
        $apiTask = Task::where('title', 'Audit existing API endpoints')->first();

        if ($homepageTask) {
            $this->createHomepageTaskComments($homepageTask);
        }

        if ($apiTask) {
            $this->createApiTaskComments($apiTask);
        }
    }

    private function createHomepageTaskComments(Task $task): void
    {
        $alice = User::where('email', 'alice@example.com')->first();
        $john = User::where('email', 'john@example.com')->first();
        $bob = User::where('email', 'bob@example.com')->first();

        Comment::create([
            'commentable_type' => Task::class,
            'commentable_id' => $task->id,
            'user_id' => $alice->id,
            'content' => 'Started working on the homepage design. Planning to use a modern gradient background with glassmorphism elements.',
            'created_at' => now()->subDays(2),
        ]);

        Comment::create([
            'commentable_type' => Task::class,
            'commentable_id' => $task->id,
            'user_id' => $john->id,
            'content' => '@alice Looks great! Can you make sure the CTA button is prominent enough? We want to increase conversions.',
            'created_at' => now()->subDays(1),
        ]);

        Comment::create([
            'commentable_type' => Task::class,
            'commentable_id' => $task->id,
            'user_id' => $alice->id,
            'content' => '@john Absolutely! I\'ll make the CTA button larger and use a contrasting color. Should have the updated mockup by tomorrow.',
            'created_at' => now()->subHours(12),
        ]);

        Comment::create([
            'commentable_type' => Task::class,
            'commentable_id' => $task->id,
            'user_id' => $bob->id,
            'content' => 'Just a heads up - I\'ll need the final design by end of week to start implementing. Let me know if that\'s doable!',
            'created_at' => now()->subHours(6),
        ]);

        $this->command->info('✅ 4 comments created for Homepage task');
    }

    private function createApiTaskComments(Task $task): void
    {
        $bob = User::where('email', 'bob@example.com')->first();
        $john = User::where('email', 'john@example.com')->first();

        Comment::create([
            'commentable_type' => Task::class,
            'commentable_id' => $task->id,
            'user_id' => $john->id,
            'content' => 'Completed the audit. Found 47 endpoints in total. About 12 of them are deprecated and should be removed.',
            'created_at' => now()->subDays(5),
        ]);

        Comment::create([
            'commentable_type' => Task::class,
            'commentable_id' => $task->id,
            'user_id' => $bob->id,
            'content' => 'Great work! Can you create a detailed document with the findings? We\'ll use it as a reference for the refactoring.',
            'created_at' => now()->subDays(4),
        ]);

        $this->command->info('✅ 2 comments created for API task');
    }

}
