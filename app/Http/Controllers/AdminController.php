<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SawaStacks\Utils\Kropify;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $data = [
            'pageTitle' => 'Dashboard',
        ];
        return view('back.pages.dashboard', $data);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')->with('success', 'You have been logged out.');
    }

    public function profile()
    {
        $data = [
            'pageTitle' => 'Profile',
        ];
        return view('back.pages.auth.profile', $data);
    }

    public function updateProfile(Request $request){
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = User::findOrFail(Auth::id());
        $path = 'images/users';
        $file = $request->file('avatar');
        $old_picture = $user->getAttributes()['avatar'];
        $extension = $file->getClientOriginalExtension();

        if (empty($extension)) {
            $extension = $file->extension();
        }

        if (empty($extension)) {
            $extension = 'jpg';
        }

        $filename = 'IMG_'.uniqid().'.'.strtolower($extension);

        $upload = Kropify::getFile($file, $filename)
            ->setDisk('public')
            ->setPath($path . '/')
            ->save();
        if($upload){
            if($old_picture != null && Storage::disk('public')->exists($old_picture)){
                Storage::disk('public')->delete($old_picture);
            }

            $user->update(['avatar' => $path . '/' . $filename]);
            $user->refresh();
            Auth::setUser($user);

            return response()->json([
                'status'=>1,
                'message' => 'Profile updated successfully.',
                'avatar_url' => $user->avatar,
            ]);
        }else{
            return response()->json(['status'=>0,'message' => 'Something went wrong.']);
        }
    }
}
