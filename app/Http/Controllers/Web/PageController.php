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
        // Get page from database
        $page = Page::where('slug', $slug)
                    ->where('is_active', 1)
                    ->first();

        if (!$page) {
            abort(404, 'Page not found');
        }

        // Always use the generic view to display content from database
        // This ensures all edits made in admin panel are reflected on the frontend
        return view('pages.show', compact('page'));
    }
}
