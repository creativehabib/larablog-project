<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // Creating Admin User
        $superAdmin = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => $password,
            'type' => 'admin',
            'status' => 'active',
        ]);
        $superAdmin->assignRole('Admin');

        // Creating Editor
        $admin = User::create([
            'name' => 'Syed Ahsan Kamal',
            'username' => 'editor',
            'email' => 'editor@example.com',
            'password' => $password,
            'type' => 'editor',
            'status' => 'active',
        ]);
        $admin->assignRole('Editor');

        // Creating Writer
        $user = User::create([
            'name' => 'Naghman Ali',
            'username' => 'writer',
            'email' => 'writer@example.com',
            'password' => $password,
            'type' => 'writer',
            'status' => 'active',
        ]);
        $user->assignRole('Writer');

    }
}
