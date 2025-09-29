<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\UserType;

class PostController extends Controller
{
    public function index()
    {
        return view('back.pages.posts.index', [
            'pageTitle' => 'Posts',
        ]);
    }

    public function create()
    {
        return view('back.pages.posts.create', [
            'pageTitle' => 'Create Post',
        ]);
    }

    public function edit(Post $post)
    {
        abort_unless($this->canAccessPost($post), 403);

        return view('back.pages.posts.edit', [
            'pageTitle' => 'Edit Post',
            'post' => $post,
        ]);
    }

    protected function canAccessPost(Post $post): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->hasAnyRole(UserType::SuperAdmin->value, UserType::Administrator->value)) {
            return true;
        }

        if ($user->hasAnyPermission('manage_content', 'edit_any_post')) {
            return true;
        }

        return (int) $post->user_id === (int) $user->id;
    }
}
