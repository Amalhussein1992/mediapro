<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BrandKit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandKitController extends Controller
{
    /**
     * Display a listing of brand kits.
     */
    public function index()
    {
        $brandKits = BrandKit::orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('admin.brand-kits.index', compact('brandKits'));
    }

    /**
     * Show the form for creating a new brand kit.
     */
    public function create()
    {
        return view('admin.brand-kits.create');
    }

    /**
     * Store a newly created brand kit.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'colors' => 'required|array|min:1',
            'colors.*' => 'required|string',
            'fonts' => 'required|array|min:1',
            'fonts.*' => 'required|string',
            'tone_of_voice' => 'nullable|in:professional,casual,friendly,formal,playful,inspirational',
            'guidelines' => 'nullable|string',
            'hashtags' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        // Handle logo upload
        $logoUrl = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('brand-kits/logos', 'public');
            $logoUrl = Storage::url($logoPath);
        }

        // If setting as default, unset other defaults
        if ($request->boolean('is_default')) {
            BrandKit::where('is_default', true)->update(['is_default' => false]);
        }

        // Parse hashtags
        $hashtags = null;
        if ($request->filled('hashtags')) {
            $hashtagsArray = array_filter(array_map('trim', explode(',', $request->hashtags)));
            $hashtags = $hashtagsArray;
        }

        // Parse tone of voice
        $toneOfVoice = $request->filled('tone_of_voice') ? [$request->tone_of_voice] : null;

        // Parse guidelines
        $guidelines = $request->filled('guidelines') ? ['text' => $request->guidelines] : null;

        BrandKit::create([
            'user_id' => 1, // Admin user
            'name' => $validated['name'],
            'logo_url' => $logoUrl,
            'colors' => $validated['colors'],
            'fonts' => $validated['fonts'],
            'tone_of_voice' => $toneOfVoice,
            'guidelines' => $guidelines,
            'hashtags' => $hashtags,
            'is_default' => $request->boolean('is_default', false),
        ]);

        return redirect()->route('admin.brand-kits.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم إنشاء Brand Kit بنجاح' : 'Brand Kit created successfully');
    }

    /**
     * Display the specified brand kit.
     */
    public function show($id)
    {
        $brandKit = BrandKit::findOrFail($id);
        return view('admin.brand-kits.show', compact('brandKit'));
    }

    /**
     * Show the form for editing the specified brand kit.
     */
    public function edit($id)
    {
        $brandKit = BrandKit::findOrFail($id);
        return view('admin.brand-kits.edit', compact('brandKit'));
    }

    /**
     * Update the specified brand kit.
     */
    public function update(Request $request, $id)
    {
        $brandKit = BrandKit::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'colors' => 'required|array|min:1',
            'colors.*' => 'required|string',
            'fonts' => 'required|array|min:1',
            'fonts.*' => 'required|string',
            'tone_of_voice' => 'nullable|in:professional,casual,friendly,formal,playful,inspirational',
            'guidelines' => 'nullable|string',
            'hashtags' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($brandKit->logo_url) {
                $oldPath = str_replace('/storage/', '', $brandKit->logo_url);
                Storage::disk('public')->delete($oldPath);
            }

            $logoPath = $request->file('logo')->store('brand-kits/logos', 'public');
            $brandKit->logo_url = Storage::url($logoPath);
        }

        // If setting as default, unset other defaults
        if ($request->boolean('is_default') && !$brandKit->is_default) {
            BrandKit::where('is_default', true)->update(['is_default' => false]);
        }

        // Parse hashtags
        $hashtags = null;
        if ($request->filled('hashtags')) {
            $hashtagsArray = array_filter(array_map('trim', explode(',', $request->hashtags)));
            $hashtags = $hashtagsArray;
        }

        // Parse tone of voice
        $toneOfVoice = $request->filled('tone_of_voice') ? [$request->tone_of_voice] : null;

        // Parse guidelines
        $guidelines = $request->filled('guidelines') ? ['text' => $request->guidelines] : null;

        $brandKit->update([
            'name' => $validated['name'],
            'colors' => $validated['colors'],
            'fonts' => $validated['fonts'],
            'tone_of_voice' => $toneOfVoice,
            'guidelines' => $guidelines,
            'hashtags' => $hashtags,
            'is_default' => $request->boolean('is_default', false),
        ]);

        return redirect()->route('admin.brand-kits.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث Brand Kit بنجاح' : 'Brand Kit updated successfully');
    }

    /**
     * Remove the specified brand kit.
     */
    public function destroy($id)
    {
        $brandKit = BrandKit::findOrFail($id);

        // Delete logo if exists
        if ($brandKit->logo_url) {
            $logoPath = str_replace('/storage/', '', $brandKit->logo_url);
            Storage::disk('public')->delete($logoPath);
        }

        $brandKit->delete();

        return redirect()->route('admin.brand-kits.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم حذف Brand Kit بنجاح' : 'Brand Kit deleted successfully');
    }

    /**
     * Set a brand kit as default.
     */
    public function setDefault($id)
    {
        // Unset all defaults
        BrandKit::where('is_default', true)->update(['is_default' => false]);

        // Set this one as default
        $brandKit = BrandKit::findOrFail($id);
        $brandKit->update(['is_default' => true]);

        return redirect()->route('admin.brand-kits.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم تعيين Brand Kit كافتراضي' : 'Brand Kit set as default');
    }
}
