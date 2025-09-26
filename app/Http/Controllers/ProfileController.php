<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $data = [
            'pageTitle' => 'Profile',
        ];
        return view('back.pages.auth.profile', $data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . Auth::id(),
            'email'    => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'bio'    => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();

        // Handle profile photo upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('profiles', 'public');
            $user->avatar = $path;
        }

        $user->name     = $request->name;
        $user->username = $request->username;
        $user->email    = $request->email;
        $user->bio    = $request->bio;
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = auth()->user();

        // delete old avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // store new one
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Avatar updated successfully!',
            'avatar_url' => asset('storage/' . $path)
        ]);
    }
}
