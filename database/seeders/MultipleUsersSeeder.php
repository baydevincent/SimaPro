<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class MultipleUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $adminRole = Role::where('name', 'administrator')->first();
        $mandorRole = Role::where('name', 'mandor')->first();

        // Create multiple mandor users
        $mandorUsers = [
            [
                'name' => 'Mandor 1',
                'last_name' => 'User',
                'email' => 'mandor1@example.com',
                'username' => 'mandor1',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Mandor 2',
                'last_name' => 'User',
                'email' => 'mandor2@example.com',
                'username' => 'mandor2',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Mandor 3',
                'last_name' => 'User',
                'email' => 'mandor3@example.com',
                'username' => 'mandor3',
                'password' => bcrypt('password'),
            ]
        ];

        foreach ($mandorUsers as $userData) {
            $user = User::firstOrCreate([
                'email' => $userData['email'],
            ], $userData);

            // Assign mandor role to user
            if ($mandorRole && !$user->roles->contains($mandorRole->id)) {
                $user->roles()->attach($mandorRole->id);
            }
        }

        // Create multiple admin users
        $adminUsers = [
            [
                'name' => 'Admin 1',
                'last_name' => 'User',
                'email' => 'admin1@example.com',
                'username' => 'admin1',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Admin 2',
                'last_name' => 'User',
                'email' => 'admin2@example.com',
                'username' => 'admin2',
                'password' => bcrypt('password'),
            ]
        ];

        foreach ($adminUsers as $userData) {
            $user = User::firstOrCreate([
                'email' => $userData['email'],
            ], $userData);

            // Assign admin role to user
            if ($adminRole && !$user->roles->contains($adminRole->id)) {
                $user->roles()->attach($adminRole->id);
            }
        }
    }
}