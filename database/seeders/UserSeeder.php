<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\UserStatus;
use App\UserType;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        $users = [
            UserType::SuperAdmin->value => [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'username' => 'superadmin',
            ],
            UserType::Administrator->value => [
                'name' => 'Administrator',
                'email' => 'administrator@example.com',
                'username' => 'administrator',
            ],
            UserType::Editor->value => [
                'name' => 'Editor',
                'email' => 'editor@example.com',
                'username' => 'editor',
            ],
            UserType::Author->value => [
                'name' => 'Author Reporter',
                'email' => 'author@example.com',
                'username' => 'author',
            ],
            UserType::Contributor->value => [
                'name' => 'Contributor',
                'email' => 'contributor@example.com',
                'username' => 'contributor',
            ],
            UserType::Subscriber->value => [
                'name' => 'Subscriber',
                'email' => 'subscriber@example.com',
                'username' => 'subscriber',
            ],
        ];

        foreach ($users as $role => $attributes) {
            User::updateOrCreate(
                ['email' => $attributes['email']],
                array_merge($attributes, [
                    'password' => $password,
                    'type' => UserType::from($role),
                    'status' => UserStatus::Active,
                ])
            );
        }
    }
}
