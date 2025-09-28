<?php

use App\UserType;

return [
    UserType::SuperAdmin->value => [
        'label' => 'সুপার অ্যাডমিন',
        'summary' => 'টেকনিক্যাল ব্যবস্থাপনা',
        'permissions' => ['*'],
    ],
    UserType::Administrator->value => [
        'label' => 'অ্যাডমিনিস্ট্রেটর',
        'summary' => 'কন্টেন্ট ও ইউজার ব্যবস্থাপনা',
        'permissions' => [
            'access_admin_panel',
            'manage_content',
            'manage_users',
            'publish_posts',
            'edit_any_post',
            'create_posts',
            'edit_own_posts',
            'submit_posts',
        ],
    ],
    UserType::Editor->value => [
        'label' => 'এডিটর',
        'summary' => 'কন্টেন্ট সম্পাদনা ও প্রকাশ',
        'permissions' => [
            'access_admin_panel',
            'publish_posts',
            'edit_any_post',
            'create_posts',
            'edit_own_posts',
        ],
    ],
    UserType::Author->value => [
        'label' => 'লেখক/রিপোর্টার',
        'summary' => 'কন্টেন্ট তৈরি করা',
        'permissions' => [
            'access_admin_panel',
            'create_posts',
            'edit_own_posts',
        ],
    ],
    UserType::Contributor->value => [
        'label' => 'কন্ট্রিবিউটর',
        'summary' => 'কন্টেন্ট জমা দেওয়া',
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
