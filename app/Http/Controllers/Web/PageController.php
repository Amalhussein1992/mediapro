<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;

class PageController extends Controller
{
    /**
     * Display a specific page by slug
     */
    public function show($slug)
    {
        // Try to get page from database first
        try {
            $page = Page::where('slug', $slug)
                        ->where('is_active', 1)
                        ->first();

            if ($page) {
                return view('pages.show', compact('page'));
            }
        } catch (\Exception $e) {
            // Database error - fall back to blade files
        }

        // Fallback: Check if blade file exists
        $viewPath = 'pages.' . $slug;
        if (view()->exists($viewPath)) {
            // Create a simple page object for the view
            $page = (object) [
                'title' => ucfirst($slug),
                'slug' => $slug,
                'content' => '',
            ];
            return view($viewPath, compact('page'));
        }

        abort(404, 'Page not found');
    }
}
