<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acmeCorp = Organization::where('slug', 'acme-corp')->first();
        $techStartup = Organization::where('slug', 'tech-startup')->first();
        $designAgency = Organization::where('slug', 'design-agency')->first();

        // Acme Corp Projects
        $this->createAcmeCorpProjects($acmeCorp);
        
        // Tech Startup Projects
        $this->createTechStartupProjects($techStartup);
        
        // Design Agency Projects
        $this->createDesignAgencyProjects($designAgency);
        
    }

    private function createAcmeCorpProjects(Organization $org)
    {
        $john = User::where('email', 'john@example.com')->first();
        $alice = User::where('email', 'alice@example.com')->first();
        $bob = User::where('email', 'bob@example.com')->first();
        $carol = User::where('email', 'carol@example.com')->first();

        // Project 1: Website Redesign (Active)
        $websiteRedesign = Project::create([
            'organization_id' => $org->id,
            'name' => 'Website Redesign',
            'slug' => 'website-redesign',
            'description' => 'Complete redesign of company website with modern UI/UX',
            'status' => 'Active',
            'color' => '#3B82F6',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonths(2),
            'created_by' => $john->id,
        ]);

        // Add members
        $websiteRedesign->members()->attach($john->id, [
            'role' => 'manager',
        ]);

         $websiteRedesign->members()->attach($alice->id, [
            'role' => 'member',
            'added_by' => $john->id,
        ]);
        $websiteRedesign->members()->attach($bob->id, [
            'role' => 'member',
            'added_by' => $john->id,
        ]);

        $this->command->info('✅ Website Redesign project created');

        // Project 2: Mobile App Development (Planning)
        $mobileApp = Project::create([
            'organization_id' => $org->id,
            'name' => 'Mobile App Development',
            'slug' => 'mobile-app-dev',
            'description' => 'Native mobile application for iOS and Android',
            'status' => 'planning',
            'color' => '#10B981',
            'start_date' => now()->addWeeks(2),
            'end_date' => now()->addMonths(6),
            'created_by' => $alice->id,
        ]);

        $mobileApp->members()->attach($alice->id, [
            'role' => 'manager',
        ]);

        $mobileApp->members()->attach($bob->id, [
            'role' => 'member',
            'added_by' => $alice->id,
        ]);
        $mobileApp->members()->attach($carol->id, [
            'role' => 'member',
            'added_by' => $alice->id,
        ]);

        $this->command->info('✅ Mobile App Development project created');

         // Project 3: API Refactoring (Active)
        $apiRefactor = Project::create([
            'organization_id' => $org->id,
            'name' => 'API Refactoring',
            'slug' => 'api-refactoring',
            'description' => 'Refactor legacy API to modern microservices architecture',
            'status' => 'active',
            'color' => '#F59E0B',
            'start_date' => now()->subWeeks(3),
            'end_date' => now()->addMonths(4),
            'created_by' => $bob->id,
        ]);

        $apiRefactor->members()->attach($bob->id, [
            'role' => 'manager',
        ]);
        $apiRefactor->members()->attach($john->id, [
            'role' => 'member',
            'added_by' => $bob->id,
        ]);

        $this->command->info('✅ API Refactoring project created');

        // Project 4: Documentation Update (Completed)
        $docsUpdate = Project::create([
            'organization_id' => $org->id,
            'name' => 'Documentation Update',
            'slug' => 'docs-update',
            'description' => 'Update all technical documentation',
            'status' => 'completed',
            'color' => '#8B5CF6',
            'start_date' => now()->subMonths(3),
            'end_date' => now()->subMonth(),
            'created_by' => $carol->id,
        ]);

         $docsUpdate->members()->attach($carol->id, [
            'role' => 'manager',
        ]);

        $this->command->info('✅ Documentation Update project created');
    }

    private function createTechStartupProjects(Organization $org)
    {
        $alice = User::where('email', 'alice@example.com')->first();
        $bob = User::where('email', 'bob@example.com')->first();

        // Project: MVP Development
        $mvp = Project::create([
            'organization_id' => $org->id,
            'name' => 'MVP Development',
            'slug' => 'mvp-development',
            'description' => 'Build minimum viable product for market validation',
            'status' => 'active',
            'color' => '#EC4899',
            'start_date' => now()->subWeeks(4),
            'end_date' => now()->addMonths(3),
            'created_by' => $alice->id,
        ]);

        $mvp->members()->attach($alice->id, [
            'role' => 'manager',
        ]);

         $mvp->members()->attach($bob->id, [
            'role' => 'member', 
            'added_by' => $alice->id
        ]);

        $this->command->info('✅ MVP Development project created');
    }

    private function createDesignAgencyProjects(Organization $org): void
    {
        $john = User::where('email', 'john@example.com')->first();
        $alice = User::where('email', 'alice@example.com')->first();

        // Project: Brand Identity
        $branding = Project::create([
            'organization_id' => $org->id,
            'name' => 'Brand Identity Design',
            'slug' => 'brand-identity',
            'description' => 'Complete brand identity package for new client',
            'status' => 'active',
            'color' => '#EF4444',
            'start_date' => now()->subWeeks(2),
            'end_date' => now()->addMonth(),
            'created_by' => $alice->id,
        ]);

        $branding->members()->attach($alice->id, [
            'role' => 'manager', 
        ]);
        $branding->members()->attach($john->id, [
            'role' => 'member', 
            'added_by' => $alice->id
        ]);

        $this->command->info('✅ Brand Identity Design project created');
    }
}
