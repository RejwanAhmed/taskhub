<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acmeCorp = Organization::where('slug', 'acme-corp')->first();

        $websiteRedesign = Project::where('slug', 'website-redesign')->first();
        if ($websiteRedesign) {
            $this->createWebsiteRedesignTasks($websiteRedesign, $acmeCorp);
        }

        // Mobile App Development Project Tasks
        $mobileProject = Project::where('slug', 'mobile-app-dev')->first();
        if ($mobileProject) {
            $this->createMobileAppTasks($mobileProject, $acmeCorp);
        }

        // API Refactoring Project Tasks
        $apiProject = Project::where('slug', 'api-refactoring')->first();
        if ($apiProject) {
            $this->createApiRefactoringTasks($apiProject, $acmeCorp);
        }
    }

    private function createWebsiteRedesignTasks(Project $project, Organization $org)
    {
        $alice = User::where('email', 'alice@example.com')->first();
        $bob = User::where('email', 'bob@example.com')->first();
        $john = User::where('email', 'john@example.com')->first();

        $designTag = Tag::where('organization_id', $org->id)->where('slug', 'design')->first();
        $frontendTag = Tag::where('organization_id', $org->id)->where('slug', 'frontend')->first();
        $urgentTag = Tag::where('organization_id', $org->id)->where('slug', 'urgent')->first();

        // Task 1: Design Homepage
        $homepageTask = Task::create([
            'organization_id' => $org->id,
            'project_id' => $project->id,
            'title' => 'Design homepage mockup',
            'description' => 'Create modern homepage design with hero section, features, and call-to-action',
            'priority' => 'high',
            'status' => 'in_progress',
            'assigned_to' => $alice->id,
            'created_by' => $john->id,
            'due_date' => now()->addDays(5),
            'estimated_hours' => 16,
            'actual_hours' => 8,
            'position' => 1,
        ]);

        $homepageTask->tags()->attach([$designTag->id, $urgentTag->id]);

        // Task 1.1: Hero Section (Subtask)
        $heroTask = Task::create([
            'organization_id' => $org->id,
            'project_id' => $project->id,
            'parent_task_id' => $homepageTask->id,
            'title' => 'Design hero section',
            'description' => 'Eye-catching hero section with CTA button',
            'priority' => 'high',
            'status' => 'completed',
            'assigned_to' => $alice->id,
            'created_by' => $john->id,
            'due_date' => now()->addDays(2),
            'estimated_hours' => 4,
            'actual_hours' => 3.5,
            'completed_at' => now()->subDay(),
            'position' => 1,
        ]);
        $heroTask->tags()->attach($designTag->id);

        // Task 1.2: Features Section (Subtask)
        Task::create([
            'organization_id' => $org->id,
            'project_id' => $project->id,
            'parent_task_id' => $homepageTask->id,
            'title' => 'Design features section',
            'description' => 'Grid layout showing key product features',
            'priority' => 'medium',
            'status' => 'in_progress',
            'assigned_to' => $alice->id,
            'created_by' => $john->id,
            'due_date' => now()->addDays(3),
            'estimated_hours' => 4,
            'actual_hours' => 2,
            'position' => 2,
        ]);

        // Task 2: Implement Responsive Navigation
        Task::create([
            'organization_id' => $org->id,
            'project_id' => $project->id,
            'title' => 'Implement responsive navigation',
            'description' => 'Build mobile-friendly navigation with hamburger menu',
            'priority' => 'high',
            'status' => 'to_do',
            'assigned_to' => $bob->id,
            'created_by' => $john->id,
            'due_date' => now()->addWeek(),
            'estimated_hours' => 8,
            'position' => 2,
        ]);

        // Task 3: Create Logo
        Task::create([
            'organization_id' => $org->id,
            'project_id' => $project->id,
            'title' => 'Create new company logo',
            'description' => 'Design modern, scalable logo with multiple variations',
            'priority' => 'medium',
            'status' => 'in_review',
            'assigned_to' => $alice->id,
            'created_by' => $john->id,
            'due_date' => now()->addDays(10),
            'estimated_hours' => 12,
            'actual_hours' => 11,
            'position' => 3,
        ]);

        // Task 4: Setup CSS Framework
        Task::create([
            'organization_id' => $org->id,
            'project_id' => $project->id,
            'title' => 'Setup Tailwind CSS',
            'description' => 'Configure Tailwind CSS with custom theme',
            'priority' => 'medium',
            'status' => 'completed',
            'assigned_to' => $bob->id,
            'created_by' => $john->id,
            'due_date' => now()->subDays(2),
            'estimated_hours' => 4,
            'actual_hours' => 3,
            'completed_at' => now()->subDays(3),
            'position' => 4,
        ]);

        // Task 5: Performance Optimization
        Task::create([
            'organization_id' => $org->id,
            'project_id' => $project->id,
            'title' => 'Optimize page load speed',
            'description' => 'Implement lazy loading, image optimization, and code splitting',
            'priority' => 'low',
            'status' => 'to_do',
            'assigned_to' => $bob->id,
            'created_by' => $john->id,
            'due_date' => now()->addWeeks(2),
            'estimated_hours' => 6,
            'position' => 5,
        ]);

        $this->command->info('✅ 7 tasks created for Website Redesign');
    }

    private function createMobileAppTasks(Project $project, Organization $org)
    {
        $bob = User::where('email', 'bob@example.com')->first();
        $carol = User::where('email', 'carol@example.com')->first();
        $alice = User::where('email', 'alice@example.com')->first();

         // Task 1: Setup Project Structure
        Task::create([
            'organization_id' => $org->id,
            'project_id' => $project->id,
            'title' => 'Setup React Native project',
            'description' => 'Initialize React Native project with proper folder structure',
            'priority' => 'urgent',
            'status' => 'to_do',
            'assigned_to' => $bob->id,
            'created_by' => $alice->id,
            'due_date' => now()->addDays(3),
            'estimated_hours' => 4,
            'position' => 1,
        ]);

        // Task 2: User Authentication
        Task::create([
            'organization_id' => $org->id,
            'project_id' => $project->id,
            'title' => 'Implement user authentication',
            'description' => 'Build login, registration, and password reset flows',
            'priority' => 'high',
            'status' => 'to_do',
            'assigned_to' => $bob->id,
            'created_by' => $alice->id,
            'due_date' => now()->addWeeks(2),
            'estimated_hours' => 20,
            'position' => 2,
        ]);

        // Task 3: Design App Screens
        Task::create([
            'organization_id' => $org->id,
            'project_id' => $project->id,
            'title' => 'Design all app screens',
            'description' => 'Create high-fidelity designs for all app screens',
            'priority' => 'high',
            'status' => 'to_do',
            'assigned_to' => $alice->id,
            'created_by' => $alice->id,
            'due_date' => now()->addWeeks(3),
            'estimated_hours' => 40,
            'position' => 3,
        ]);

        $this->command->info('✅ 3 tasks created for Mobile App Development');
    }

    private function createApiRefactoringTasks(Project $project, Organization $org)
    {
        $bob = User::where('email', 'bob@example.com')->first();
        $john = User::where('email', 'john@example.com')->first();

        $backendTag = Tag::where('organization_id', $org->id)->where('slug', 'backend')->first();

        // Create 5 tasks
        Task::create([
            'organization_id' => $org->id,
            'project_id' => $project->id,
            'title' => 'Audit existing API endpoints',
            'description' => 'Document all existing API endpoints and their usage',
            'priority' => 'high',
            'status' => 'completed',
            'assigned_to' => $john->id,
            'created_by' => $bob->id,
            'due_date' => now()->subWeek(),
            'estimated_hours' => 8,
            'actual_hours' => 10,
            'completed_at' => now()->subDays(5),
            'position' => 1,
        ])->tags()->attach($backendTag->id);

        Task::create([
            'organization_id' => $org->id,
            'project_id' => $project->id,
            'title' => 'Design microservices architecture',
            'description' => 'Plan the new microservices structure',
            'priority' => 'high',
            'status' => 'in_progress',
            'assigned_to' => $bob->id,
            'created_by' => $bob->id,
            'due_date' => now()->addDays(7),
            'estimated_hours' => 16,
            'actual_hours' => 8,
            'position' => 2,
        ]);

        $this->command->info('✅ 2 tasks created for API Refactoring');
    }
}
