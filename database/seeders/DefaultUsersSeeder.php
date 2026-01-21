<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class DefaultUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $adminRole = Role::where('name', 'administrator')->first();
        $mandorRole = Role::where('name', 'mandor')->first();

        // Create admin user if doesn't exist
        $adminUser = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Administrator',
            'last_name' => 'User',
            'username' => 'admin',
            'password' => bcrypt('password'),
        ]);

        // Assign admin role to admin user
        if ($adminRole && !$adminUser->roles->contains($adminRole->id)) {
            $adminUser->roles()->attach($adminRole->id);
        }

        // Create mandor user if doesn't exist
        $mandorUser = User::firstOrCreate([
            'email' => 'mandor@example.com',
        ], [
            'name' => 'Mandor',
            'last_name' => 'User',
            'username' => 'mandor',
            'password' => bcrypt('password'),
        ]);

        // Assign mandor role to mandor user
        if ($mandorRole && !$mandorUser->roles->contains($mandorRole->id)) {
            $mandorUser->roles()->attach($mandorRole->id);
        }
    }
}