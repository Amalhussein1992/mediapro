<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pages = DB::table('pages')
            ->orderBy('section', 'asc')
            ->orderBy('order', 'asc')
            ->get();

        // Group pages by section
        $pagesBySection = $pages->groupBy('section');

        return view('admin.pages.index', compact('pages', 'pagesBySection'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'nullable|string',
            'content_ar' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_description_ar' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'status' => 'required|in:published,draft',
            'order' => 'nullable|integer',
            'icon' => 'nullable|string|max:255',
            'section' => 'required|string|max:255',
            'show_in_header' => 'nullable|boolean',
            'show_in_footer' => 'nullable|boolean',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Set default values
        $validated['order'] = $validated['order'] ?? 0;
        $validated['show_in_header'] = $request->has('show_in_header') ? 1 : 0;
        $validated['show_in_footer'] = $request->has('show_in_footer') ? 1 : 0;

        DB::table('pages')->insert(array_merge($validated, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return redirect()->route('admin.pages.index')
            ->with('success', 'تم إنشاء الصفحة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = DB::table('pages')->where('id', $id)->first();

        if (!$page) {
            abort(404, 'Page not found');
        }

        return view('admin.pages.show', compact('page'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = DB::table('pages')->where('id', $id)->first();

        if (!$page) {
            abort(404, 'Page not found');
        }

        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $page = DB::table('pages')->where('id', $id)->first();

        if (!$page) {
            abort(404, 'Page not found');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $id,
            'content' => 'nullable|string',
            'content_ar' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_description_ar' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'status' => 'required|in:published,draft',
            'order' => 'nullable|integer',
            'icon' => 'nullable|string|max:255',
            'section' => 'required|string|max:255',
            'show_in_header' => 'nullable|boolean',
            'show_in_footer' => 'nullable|boolean',
        ]);

        // Set default values
        $validated['order'] = $validated['order'] ?? 0;
        $validated['show_in_header'] = $request->has('show_in_header') ? 1 : 0;
        $validated['show_in_footer'] = $request->has('show_in_footer') ? 1 : 0;

        DB::table('pages')
            ->where('id', $id)
            ->update(array_merge($validated, [
                'updated_at' => now(),
            ]));

        return redirect()->route('admin.pages.index')
            ->with('success', 'تم تحديث الصفحة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $page = DB::table('pages')->where('id', $id)->first();

        if (!$page) {
            return redirect()->route('admin.pages.index')
                ->with('error', 'الصفحة غير موجودة');
        }

        DB::table('pages')->where('id', $id)->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'تم حذف الصفحة بنجاح');
    }
}
