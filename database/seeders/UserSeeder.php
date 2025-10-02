<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role1 = Role::create(['name' => 'admin']);
        $role2 = Role::create(['name' => 'author']);
        $role3 = Role::create(['name' => 'writer']);

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
        $superAdmin->assignRole($role1);
        // Creating Editor
        $admin = User::create([
            'name' => 'Syed Ahsan Kamal',
            'username' => 'kamal',
            'email' => 'editor@example.com',
            'password' => $password,
            'type' => 'editor',
            'status' => 'active',
        ]);
        $admin->assignRole($role2);

        // Creating Writer
        $user = User::create([
            'name' => 'Naghman Ali',
            'username' => 'writer',
            'email' => 'nagham@example.com',
            'password' => $password,
            'type' => 'writer',
            'status' => 'active',
        ]);
        $user->assignRole($role3);

    }
}
