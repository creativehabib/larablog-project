<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\Poll;
use App\Models\Post;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use SawaStacks\Utils\Kropify;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $totalPosts = Post::count();
        $featuredPosts = Post::where('is_featured', true)->count();
        $indexablePosts = Post::where('is_indexable', true)->count();

        $categoriesCount = Category::count();
        $subCategoriesCount = SubCategory::count();
        $totalUsers = User::count();

        $roles = Role::withCount('users')->get();
        $roleCounts = $roles->mapWithKeys(fn (Role $role) => [$role->name => $role->users_count])->toArray();

        $roleChartLabels = $roles->pluck('name')->values()->all();
        $roleChartSeries = $roles->pluck('users_count')->map(fn ($count) => (int) $count)->values()->all();

        $monthlyPostCounts = Post::query()
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->get()
            ->groupBy(fn (Post $post) => $post->created_at->format('M Y'))
            ->map->count();

        $monthlyPostLabels = [];
        $monthlyPostSeries = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $label = $month->format('M Y');
            $monthlyPostLabels[] = $label;
            $monthlyPostSeries[] = (int) ($monthlyPostCounts[$label] ?? 0);
        }

        $recentPosts = Post::with(['category', 'author'])
            ->latest('updated_at')
            ->limit(5)
            ->get();

        $recentUsers = User::with('roles')
            ->latest()
            ->limit(5)
            ->get();

        $activePoll = Poll::query()
            ->where('is_active', true)
            ->latest('poll_date')
            ->latest()
            ->first();

        $pollChartLabels = $activePoll
            ? ['হ্যাঁ', 'না', 'মতামত নেই']
            : [];

        $pollChartSeries = $activePoll
            ? [
                (int) $activePoll->yes_votes,
                (int) $activePoll->no_votes,
                (int) $activePoll->no_opinion_votes,
            ]
            : [];

        $pollChartColors = ['#28a745', '#dc3545', '#6c757d'];

        $settings = GeneralSetting::first();

        return view('back.pages.dashboard', [
            'pageTitle' => 'Dashboard',
            'totalPosts' => $totalPosts,
            'featuredPosts' => $featuredPosts,
            'indexablePosts' => $indexablePosts,
            'categoriesCount' => $categoriesCount,
            'subCategoriesCount' => $subCategoriesCount,
            'totalUsers' => $totalUsers,
            'monthlyPostLabels' => $monthlyPostLabels,
            'monthlyPostSeries' => $monthlyPostSeries,
            'roleChartLabels' => $roleChartLabels,
            'roleChartSeries' => $roleChartSeries,
            'roleCounts' => $roleCounts,
            'recentPosts' => $recentPosts,
            'recentUsers' => $recentUsers,
            'dashboardWidgetVisibility' => $settings?->dashboard_widget_visibility ?? [],
            'activePoll' => $activePoll,
            'pollChartLabels' => $pollChartLabels,
            'pollChartSeries' => $pollChartSeries,
            'pollChartColors' => $pollChartColors,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')->with('success', 'You have successfully logged out.');
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
                'message' => 'Profile Picture updated successfully.',
                'avatar_url' => $user->avatar,
            ]);
        }else{
            return response()->json(['status'=>0,'message' => 'Something went wrong.']);
        }
    }

    //Setting
    public function generalSettings(Request $request)
    {
        $data = [
            'pageTitle' => 'General Settings',
        ];
        return view('back.pages.settings.general_settings', $data);
    }

    public function sitemapSettings()
    {
        return redirect()->route('admin.settings', ['tab' => 'sitemap_setting']);
    }
}
