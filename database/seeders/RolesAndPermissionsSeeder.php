<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Administrator role
        Role::firstOrCreate([
            'name' => 'administrator',
            'display_name' => 'Administrator',
            'description' => 'User with full administrative privileges'
        ]);

        // Create Mandor role
        Role::firstOrCreate([
            'name' => 'mandor',
            'display_name' => 'Mandor',
            'description' => 'Supervisor role with limited access'
        ]);
    }
}
