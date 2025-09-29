<?php

use App\UserType;

return [
    'guard' => env('PERMISSION_GUARD', config('auth.defaults.guard', 'web')),

    'default' => UserType::Subscriber->value,

    'prune_missing' => false,

    'roles' => [
        UserType::SuperAdmin->value => [
            'label' => 'সুপার অ্যাডমিন',
            'summary' => null,
            'permissions' => ['*', 'manage_roles'],
        ],
        UserType::Administrator->value => [
            'label' => 'অ্যাডমিনিস্ট্রেটর',
            'summary' => null,
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
            'summary' => null,
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
            'summary' => null,
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
            'summary' => null,
            'permissions' => [
                'access_admin_panel',
                'create_posts',
                'submit_posts',
            ],
        ],
        UserType::Subscriber->value => [
            'label' => 'সাবস্ক্রাইবার',
            'summary' => null,
            'permissions' => ['read_and_comment'],
        ],
    ],
];
