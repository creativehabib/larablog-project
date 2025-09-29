<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // প্রথমে পারমিশন ক্যাশ রিসেট করে নিন
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 📝 পারমিশন তালিকা (slug সহ)
        Permission::create(['name' => 'create articles', 'slug' => Str::slug('create articles'), 'guard_name' => 'web']);
        Permission::create(['name' => 'edit articles', 'slug' => Str::slug('edit articles'), 'guard_name' => 'web']);
        Permission::create(['name' => 'delete articles', 'slug' => Str::slug('delete articles'), 'guard_name' => 'web']);
        Permission::create(['name' => 'publish articles', 'slug' => Str::slug('publish articles'), 'guard_name' => 'web']);
        Permission::create(['name' => 'unpublish articles', 'slug' => Str::slug('unpublish articles'), 'guard_name' => 'web']);
        Permission::create(['name' => 'manage users', 'slug' => Str::slug('manage users'), 'guard_name' => 'web']);

        // 🧑‍💼 ভূমিকা বা রোল তালিকা (slug সহ) - ✅ এখানে পরিবর্তন করা হয়েছে
        $adminRole = Role::create(['name' => 'Admin', 'slug' => Str::slug('Admin'), 'guard_name' => 'web']);
        $editorRole = Role::create(['name' => 'Editor', 'slug' => Str::slug('Editor'), 'guard_name' => 'web']);
        $writerRole = Role::create(['name' => 'Writer', 'slug' => Str::slug('Writer'), 'guard_name' => 'web']);

        // 🔐 ভূমিকা অনুযায়ী পারমিশন বণ্টন

        // Admin কে সব পারমিশন দেওয়া হলো
        $adminRole->givePermissionTo(Permission::all());

        // Editor কে নির্দিষ্ট পারমিশন দেওয়া হলো
        $editorRole->givePermissionTo([
            'create articles',
            'edit articles',
            'publish articles',
            'unpublish articles'
        ]);

        // Writer কে নির্দিষ্ট পারমিশন দেওয়া হলো
        $writerRole->givePermissionTo([
            'create articles',
            'edit articles'
        ]);
    }
}
