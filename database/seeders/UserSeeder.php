<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@taskhub.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'timezone' => 'Asia/Dhaka',
            'bio' => 'System Administrator',
        ]);

        $this->command->info('Admin User Created');

        $john = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'timezone' => 'Asia/Dhaka',
            'bio' => 'Project Manager with 5 years of experience',
        ]);

        $alice = User::create([
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'timezone' => 'Asia/Dhaka',
            'bio' => 'Senior Designer specializing in UI/UX',
        ]);

        $bob = User::create([
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'timezone' => 'Asia/Dhaka',
            'bio' => 'Full-stack developer',
        ]);

        $carol = User::create([
            'name' => 'Carol Williams',
        'email' => 'carol@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'timezone' => 'Asia/Dhaka',
            'bio' => 'QA Engineer',
        ]);

        $this->command->info('✅ Test users created (john, alice, bob, carol)');
    }
}
