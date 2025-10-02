<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function index()
    {
        ! Gate::allows('post.view') ? abort(403, 'Unauthorized action.') : null;
        return view('back.pages.posts.index', [
            'pageTitle' => 'Posts',
        ]);
    }

    public function create()
    {
        ! Gate::allows('post.create') ? abort(403, 'Unauthorized action.') : null;
        return view('back.pages.posts.create', [
            'pageTitle' => 'Create Post',
        ]);
    }

    public function edit(Post $post)
    {
        ! Gate::allows('post.edit') ? abort(403, 'Unauthorized action.') : null;
        return view('back.pages.posts.edit', [
            'pageTitle' => 'Edit Post',
            'post' => $post,
        ]);
    }

}
