<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@taskhub.com')->first();
        $john = User::where('email', 'john@example.com')->first();
        $alice = User::where('email', 'alice@example.com')->first();
        $bob = User::where('email', 'bob@example.com')->first();
        $carol = User::where('email', 'carol@example.com')->first();

        // Organization 1: Acme Corp (Main Organization)
        $acmeCorp = Organization::create([
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
            'description' => 'Leading software development company specializing in enterprise solutions',
            'settings' => [
                'working_hours' => '09:00-17:00',
                'timezone' => 'Asia/Dhaka',
                'allow_public_projects' => false,
            ],
        ]);

        // Add members to Acme Corp
        $acmeCorp->members()->attach($admin->id, [
            'role' => 'owner',
            'joined_at' => now()->subMonths(6),
        ]);

        $acmeCorp->members()->attach($john->id, [
            'role' => 'manager',
            'joined_at' => now()->subMonths(5),
        ]);

        $acmeCorp->members()->attach($alice->id, [
            'role' => 'member',
            'joined_at' => now()->subMonths(4),
        ]);

        $acmeCorp->members()->attach($bob->id, [
            'role' => 'member',
            'joined_at' => now()->subMonths(3),
        ]);

        $acmeCorp->members()->attach($carol->id, [
            'role' => 'member',
            'joined_at' => now()->subMonths(2),
        ]);

        $this->command->info('✅ Acme Corp created with 5 members');

        // Organization 2: Tech Startup
        $techStartup = Organization::create([
            'name' => 'Tech Startup',
            'slug' => 'tech-startup',
            'description' => 'Innovative tech solutions for modern businesses',
            'settings' => [
                'working_hours' => '10:00-18:00',
                'timezone' => 'Asia/Dhaka',
                'allow_public_projects' => true,
            ],
        ]);

        // Add members to Tech Startup
        $techStartup->members()->attach($alice->id, [
            'role' => 'owner',
            'joined_at' => now()->subMonths(8),
        ]);

        $techStartup->members()->attach($bob->id, [
            'role' => 'manager',
            'joined_at' => now()->subMonths(6),
        ]);

        $techStartup->members()->attach($carol->id, [
            'role' => 'member',
            'joined_at' => now()->subMonth(),
        ]);

        $this->command->info('✅ Tech Startup created with 3 members');

        // Organization 3: Design Agency
        $designAgency = Organization::create([
            'name' => 'Design Agency',
            'slug' => 'design-agency',
            'description' => 'Creative design solutions that bring your vision to life',
            'settings' => [
                'working_hours' => '09:00-17:00',
                'timezone' => 'Asia/Dhaka',
                'allow_public_projects' => false,
            ],
        ]);

        $designAgency->members()->attach($john->id, [
            'role' => 'owner',
            'joined_at' => now()->subYear(),
        ]);

        $designAgency->members()->attach($alice->id, [
            'role' => 'manager',
            'joined_at' => now()->subMonths(9),
        ]);

        $this->command->info('✅ Design Agency created with 2 members');
    }
}
