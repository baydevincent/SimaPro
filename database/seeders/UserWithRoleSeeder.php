<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserWithRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $adminRole = Role::where('name', 'administrator')->first();
        $mandorRole = Role::where('name', 'mandor')->first();

        // Create admin user
        $adminUser = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Administrator',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'username' => 'admin',
        ]);

        // Assign admin role to admin user
        if ($adminRole) {
            $adminUser->roles()->sync([$adminRole->id]);
        }

        // Create mandor user
        $mandorUser = User::firstOrCreate([
            'email' => 'mandor@example.com',
        ], [
            'name' => 'Mandor',
            'last_name' => 'User',
            'email' => 'mandor@example.com',
            'password' => 'password',
            'username' => 'mandor',
        ]);

        // Assign mandor role to mandor user
        if ($mandorRole) {
            $mandorUser->roles()->sync([$mandorRole->id]);
        }
    }
}
