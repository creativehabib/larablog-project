<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Default role and permission definitions keyed by role slug.
     *
     * @var array<string, array<string, mixed>>
     */
    protected array $definitions = [
        UserType::SuperAdmin->value => [
            'label' => 'সুপার অ্যাডমিন',
            'summary' => 'সম্পূর্ণ ওয়েবসাইট, সার্ভার, প্লাগইন ও থিমসহ সব টেকনিক্যাল ব্যবস্থাপনার সর্বময় দায়িত্ব পালন করেন এবং প্রয়োজন অনুযায়ী যেকোনো ব্যবহারকারী যোগ বা অপসারণ করতে পারেন।',
            'permissions' => ['*', 'manage_roles'],
        ],
        UserType::Administrator->value => [
            'label' => 'অ্যাডমিনিস্ট্রেটর',
            'summary' => 'সম্পাদকীয় নীতি বাস্তবায়ন, প্রকাশনার সময়সূচী নির্ধারণ, লেখক-এডিটরদের তদারকি এবং কন্টেন্ট ও ব্যবহারকারী ব্যবস্থাপনার দায়িত্বে থাকেন। টেকনিক্যাল সেটিংস ছাড়া সাইটের প্রায় সবকিছু নিয়ন্ত্রণ করতে পারেন।',
            'permissions' => [
                'access_admin_panel',
                'manage_content',
                'publish_posts',
                'edit_any_post',
                'create_posts',
                'edit_own_posts',
                'submit_posts',
                'schedule_posts',
            ],
        ],
        UserType::Editor->value => [
            'label' => 'এডিটর',
            'summary' => 'লেখকদের জমা দেওয়া কন্টেন্ট যাচাই-বাছাই, সম্পাদনা, ক্যাটাগরি ও ট্যাগ ঠিক করা এবং প্রকাশের অনুমোদন দেওয়ার দায়িত্বে থাকেন। যেকোনো পোস্ট এডিট, প্রকাশ বা ডিলিট করতে পারেন, তবে সাইট সেটিংস বা ইউজার ম্যানেজমেন্ট করতে পারেন না।',
            'permissions' => [
                'access_admin_panel',
                'publish_posts',
                'edit_any_post',
                'create_posts',
                'edit_own_posts',
                'review_posts',
                'manage_categories',
                'verify_content',
            ],
        ],
        UserType::Author->value => [
            'label' => 'লেখক/রিপোর্টার',
            'summary' => 'ওয়েবসাইটের মূল কন্টেন্ট নির্মাতা হিসেবে খবর সংগ্রহ, আর্টিকেল লেখা, নিজের মিডিয়া আপলোড এবং প্রয়োজন অনুযায়ী নিজের লেখা পোস্ট এডিট বা ডিলিট করেন। সাধারণত তাঁদের লেখা সরাসরি প্রকাশিত হয় না; “Pending Review” হিসেবে জমা পড়ে।',
            'permissions' => [
                'access_admin_panel',
                'create_posts',
                'edit_own_posts',
                'delete_own_posts',
                'upload_media',
                'submit_posts',
            ],
        ],
        UserType::Contributor->value => [
            'label' => 'কন্ট্রিবিউটর',
            'summary' => 'ফ্রিল্যান্সার, অতিথি লেখক বা নতুন রিপোর্টার হিসেবে পোস্ট লিখে রিভিউর জন্য জমা দেন। তাঁদের অনুমতি লেখকের চেয়েও সীমিত—তাঁরা পোস্ট প্রকাশ করতে পারেন না, বেশিরভাগ ক্ষেত্রে মিিয়া আপলোডের সুযোগও থাকে না। জমা দেওয়ার পর কোনো এডিটর অনুমোদন বা বাতিল না করা পর্যন্ত পোস্টটি আর সম্পাদনা করতে পারেন না।',
            'permissions' => [
                'access_admin_panel',
                'create_posts',
                'submit_posts',
            ],
        ],
        UserType::Subscriber->value => [
            'label' => 'সাবস্ক্রাইবার',
            'summary' => 'কন্টেন্ট পড়া ও কমেন্ট করা',
            'permissions' => [
                'read_and_comment',
            ],
        ],
    ];

    /**
     * Seed the application's roles and permissions.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->definitions as $slug => $definition) {
            $role = Role::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $definition['label'] ?? Str::headline($slug),
                    'summary' => $definition['summary'] ?? null,
                    'guard_name' => $this->guardName(),
                ]
            );

            $permissions = $definition['permissions'] ?? [];

            $permissionModels = collect($permissions)
                ->flatten()
                ->filter(fn ($permission) => is_string($permission) && $permission !== '')
                ->map(function (string $permission) {
                    $label = $permission === '*'
                        ? 'All Permissions'
                        : Str::headline(str_replace(['*', '.'], ' ', $permission));

                    return Permission::query()->updateOrCreate(
                        ['slug' => $permission],
                        [
                            'name' => $label !== '' ? $label : Str::headline($permission),
                            'guard_name' => $this->guardName(),
                        ]
                    );
                })
                ->all();

            $role->syncPermissions($permissionModels);
        }
    }

    protected function guardName(): string
    {
        return config('auth.defaults.guard', 'web');
    }
}
