<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use Illuminate\Http\Request;

class AdminTranslationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Translation::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('value_en', 'like', "%{$search}%")
                  ->orWhere('value_ar', 'like', "%{$search}%");
            });
        }

        // Group filtering
        if ($request->has('group') && $request->group) {
            $query->where('group', $request->group);
        }

        $translations = $query->latest()->paginate(20);
        $groups = Translation::getAllGroups();
        $currentGroup = $request->group ?? 'all';

        return view('admin.translations.index', compact('translations', 'groups', 'currentGroup'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = Translation::getAllGroups();
        return view('admin.translations.create', compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:translations,key|max:255',
            'value_en' => 'required|string',
            'value_ar' => 'required|string',
            'group' => 'required|string|max:255',
        ]);

        Translation::create($validated);

        return redirect()->route('admin.translations.index')
            ->with('success', 'Translation created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Translation $translation)
    {
        $groups = Translation::getAllGroups();
        return view('admin.translations.edit', compact('translation', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Translation $translation)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:translations,key,' . $translation->id,
            'value_en' => 'required|string',
            'value_ar' => 'required|string',
            'group' => 'required|string|max:255',
        ]);

        $translation->update($validated);

        return redirect()->route('admin.translations.index')
            ->with('success', 'Translation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Translation $translation)
    {
        $translation->delete();

        return redirect()->route('admin.translations.index')
            ->with('success', 'Translation deleted successfully.');
    }

    /**
     * Export translations to JSON
     */
    public function export(Request $request)
    {
        $locale = $request->get('locale', 'en');
        $translations = Translation::all();

        $data = [];
        foreach ($translations as $translation) {
            $data[$translation->group][$translation->key] = $locale === 'ar' ? $translation->value_ar : $translation->value_en;
        }

        return response()->json($data);
    }

    /**
     * Import translations from JSON
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json',
        ]);

        $content = file_get_contents($request->file('file')->getRealPath());
        $data = json_decode($content, true);

        if (!$data) {
            return redirect()->route('admin.translations.index')
                ->with('error', 'Invalid JSON file.');
        }

        foreach ($data as $group => $translations) {
            foreach ($translations as $key => $values) {
                Translation::updateOrCreate(
                    ['key' => $key],
                    [
                        'value_en' => $values['en'] ?? '',
                        'value_ar' => $values['ar'] ?? '',
                        'group' => $group,
                    ]
                );
            }
        }

        return redirect()->route('admin.translations.index')
            ->with('success', 'Translations imported successfully.');
    }
}
