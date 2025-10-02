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
        return view('back.pages.posts.create', [
            'pageTitle' => 'Create Post',
        ]);
    }

    public function edit(Post $post)
    {
        return view('back.pages.posts.edit', [
            'pageTitle' => 'Edit Post',
            'post' => $post,
        ]);
    }

}
