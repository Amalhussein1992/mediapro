<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LandingPageSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminLandingPageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = LandingPageSection::ordered()->get()->groupBy('section_type');
        return view('admin.landing-page.index', compact('sections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sectionTypes = [
            'hero' => __('Hero Section'),
            'features' => __('Features'),
            'testimonials' => __('Testimonials'),
            'pricing' => __('Pricing'),
            'cta' => __('Call to Action'),
            'faq' => __('FAQ'),
            'stats' => __('Statistics'),
        ];

        return view('admin.landing-page.create', compact('sectionTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_type' => 'required|string|in:hero,features,testimonials,pricing,cta,faq,stats',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'content' => 'nullable|array',
            'image' => 'nullable|image|max:2048',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('landing-page', 'public');
            $validated['image_url'] = $imagePath;
        }

        // Set default order if not provided
        if (!isset($validated['order'])) {
            $maxOrder = LandingPageSection::where('section_type', $validated['section_type'])->max('order');
            $validated['order'] = ($maxOrder ?? 0) + 1;
        }

        $validated['is_active'] = $request->has('is_active');

        LandingPageSection::create($validated);

        return redirect()->route('admin.landing-page.index')
            ->with('success', __('Section created successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(LandingPageSection $landingPage)
    {
        return view('admin.landing-page.show', compact('landingPage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LandingPageSection $landingPage)
    {
        $sectionTypes = [
            'hero' => __('Hero Section'),
            'features' => __('Features'),
            'testimonials' => __('Testimonials'),
            'pricing' => __('Pricing'),
            'cta' => __('Call to Action'),
            'faq' => __('FAQ'),
            'stats' => __('Statistics'),
        ];

        return view('admin.landing-page.edit', compact('landingPage', 'sectionTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LandingPageSection $landingPage)
    {
        $validated = $request->validate([
            'section_type' => 'required|string|in:hero,features,testimonials,pricing,cta,faq,stats',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'content' => 'nullable|array',
            'image' => 'nullable|image|max:2048',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($landingPage->image_url) {
                Storage::disk('public')->delete($landingPage->image_url);
            }

            $imagePath = $request->file('image')->store('landing-page', 'public');
            $validated['image_url'] = $imagePath;
        }

        $validated['is_active'] = $request->has('is_active');

        $landingPage->update($validated);

        return redirect()->route('admin.landing-page.index')
            ->with('success', __('Section updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LandingPageSection $landingPage)
    {
        // Delete image file
        if ($landingPage->image_url) {
            Storage::disk('public')->delete($landingPage->image_url);
        }

        $landingPage->delete();

        return redirect()->route('admin.landing-page.index')
            ->with('success', __('Section deleted successfully'));
    }

    /**
     * Reorder sections
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:landing_page_sections,id',
            'sections.*.order' => 'required|integer',
        ]);

        foreach ($request->sections as $section) {
            LandingPageSection::where('id', $section['id'])
                ->update(['order' => $section['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Sections reordered successfully')
        ]);
    }

    /**
     * Toggle section active status
     */
    public function toggleActive(LandingPageSection $landingPage)
    {
        $landingPage->update(['is_active' => !$landingPage->is_active]);

        return redirect()->route('admin.landing-page.index')
            ->with('success', __('Section status updated'));
    }
}
