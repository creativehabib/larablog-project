<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;

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
//        abort_unless($this->canCreatePosts(), 403);

        return view('back.pages.posts.create', [
            'pageTitle' => 'Create Post',
        ]);
    }

    public function edit(Post $post)
    {
//        abort_unless($this->canAccessPost($post), 403);

        return view('back.pages.posts.edit', [
            'pageTitle' => 'Edit Post',
            'post' => $post,
        ]);
    }

}
