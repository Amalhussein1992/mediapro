<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\SocialAccount;
use App\Models\Analytics;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics.
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_posts' => Post::count(),
            'total_social_accounts' => SocialAccount::count(),
            'published_posts' => Post::where('status', 'published')->count(),
            'scheduled_posts' => Post::where('status', 'scheduled')->count(),
            'draft_posts' => Post::where('status', 'draft')->count(),
        ];

        // Get recent posts
        $recent_posts = Post::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Get recent users
        $recent_users = User::latest()
            ->take(5)
            ->get();

        // Get active social accounts
        $active_accounts = SocialAccount::where('is_active', true)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recent_posts', 'recent_users', 'active_accounts'));
    }
}
