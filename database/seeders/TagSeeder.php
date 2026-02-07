<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acmeCorp = Organization::where('slug', 'acme-corp')->first();
        $techStartup = Organization::where('slug', 'tech-startup')->first();
        $designAgency = Organization::where('slug', 'design-agency')->first();

        // Tags for Acme Corp
        $this->createAcmeCorpTags($acmeCorp);
        
        // Tags for Tech Startup
        $this->createTechStartupTags($techStartup);
        
        // Tags for Design Agency
        $this->createDesignAgencyTags($designAgency);
    }

    private function createAcmeCorpTags(Organization $org): void
    {
        $tags = [
            ['name' => 'Bug', 'slug' => 'bug', 'color' => '#EF4444', 'description' => 'Issues and bugs that need fixing'],
            ['name' => 'Feature', 'slug' => 'feature', 'color' => '#10B981', 'description' => 'New features and functionality'],
            ['name' => 'Design', 'slug' => 'design', 'color' => '#8B5CF6', 'description' => 'Design-related tasks'],
            ['name' => 'Frontend', 'slug' => 'frontend', 'color' => '#3B82F6', 'description' => 'Frontend development tasks'],
            ['name' => 'Backend', 'slug' => 'backend', 'color' => '#F59E0B', 'description' => 'Backend development tasks'],
            ['name' => 'Urgent', 'slug' => 'urgent', 'color' => '#DC2626', 'description' => 'High priority urgent items'],
            ['name' => 'Testing', 'slug' => 'testing', 'color' => '#EC4899', 'description' => 'QA and testing tasks'],
            ['name' => 'Documentation', 'slug' => 'documentation', 'color' => '#6B7280', 'description' => 'Documentation tasks'],
        ];

        foreach ($tags as $tagData) {
            Tag::create(array_merge($tagData, ['organization_id' => $org->id]));
        }

        $this->command->info('✅ 8 tags created for Acme Corp');
    }

    private function createTechStartupTags(Organization $org): void
    {
        $tags = [
            ['name' => 'MVP', 'slug' => 'mvp', 'color' => '#10B981', 'description' => 'Minimum viable product features'],
            ['name' => 'Research', 'slug' => 'research', 'color' => '#3B82F6', 'description' => 'Research and investigation'],
            ['name' => 'Enhancement', 'slug' => 'enhancement', 'color' => '#8B5CF6', 'description' => 'Improvements and enhancements'],
            ['name' => 'Critical', 'slug' => 'critical', 'color' => '#EF4444', 'description' => 'Critical priority items'],
            ['name' => 'Nice to Have', 'slug' => 'nice-to-have', 'color' => '#6B7280', 'description' => 'Low priority nice-to-have features'],
        ];

        foreach ($tags as $tagData) {
            Tag::create(array_merge($tagData, ['organization_id' => $org->id]));
        }

        $this->command->info('✅ 5 tags created for Tech Startup');
    }

    private function createDesignAgencyTags(Organization $org): void
    {
        $tags = [
            ['name' => 'Branding', 'slug' => 'branding', 'color' => '#EC4899', 'description' => 'Brand identity work'],
            ['name' => 'UI Design', 'slug' => 'ui-design', 'color' => '#8B5CF6', 'description' => 'User interface design'],
            ['name' => 'UX Design', 'slug' => 'ux-design', 'color' => '#3B82F6', 'description' => 'User experience design'],
            ['name' => 'Prototype', 'slug' => 'prototype', 'color' => '#10B981', 'description' => 'Prototyping tasks'],
            ['name' => 'Client Review', 'slug' => 'client-review', 'color' => '#F59E0B', 'description' => 'Pending client review'],
        ];

        foreach ($tags as $tagData) {
            Tag::create(array_merge($tagData, ['organization_id' => $org->id]));
        }

        $this->command->info('✅ 5 tags created for Design Agency');
    }
}
