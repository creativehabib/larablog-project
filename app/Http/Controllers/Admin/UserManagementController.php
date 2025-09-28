<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class UserManagementController extends Controller
{
    public function index()
    {
        return view('back.pages.users.index', [
            'pageTitle' => 'User Management',
        ]);
    }
}
