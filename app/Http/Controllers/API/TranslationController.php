<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TranslationController extends Controller
{
    /**
     * Get all translations for a specific locale
     *
     * @param string $locale
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByLocale($locale = 'en')
    {
        try {
            $translations = Translation::all();
            $result = [];

            foreach ($translations as $translation) {
                $keys = explode('.', $translation->key);
                $current = &$result;

                foreach ($keys as $index => $key) {
                    if ($index === count($keys) - 1) {
                        $current[$key] = $locale === 'ar' ? $translation->value_ar : $translation->value_en;
                    } else {
                        if (!isset($current[$key])) {
                            $current[$key] = [];
                        }
                        $current = &$current[$key];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'locale' => $locale,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch translations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all translations (both locales)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
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

            $translations = $query->latest()->get();
            $groups = Translation::getAllGroups();

            return response()->json([
                'success' => true,
                'data' => $translations,
                'groups' => $groups,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch translations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new translation
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'key' => 'required|string|unique:translations,key|max:255',
                'value_en' => 'required|string',
                'value_ar' => 'required|string',
                'group' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $translation = Translation::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Translation created successfully',
                'data' => $translation,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create translation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing translation
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $translation = Translation::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'key' => 'required|string|max:255|unique:translations,key,' . $translation->id,
                'value_en' => 'required|string',
                'value_ar' => 'required|string',
                'group' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $translation->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Translation updated successfully',
                'data' => $translation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update translation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a translation
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $translation = Translation::findOrFail($id);
            $translation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Translation deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete translation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk sync translations from mobile app
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'translations' => 'required|array',
                'locale' => 'required|in:en,ar',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $locale = $request->locale;
            $translations = $request->translations;
            $created = 0;
            $updated = 0;

            foreach ($translations as $key => $value) {
                $this->syncTranslationRecursive($key, $value, $locale, $created, $updated);
            }

            return response()->json([
                'success' => true,
                'message' => 'Translations synced successfully',
                'stats' => [
                    'created' => $created,
                    'updated' => $updated,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync translations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Recursively sync nested translation structure
     */
    private function syncTranslationRecursive($prefix, $data, $locale, &$created, &$updated, $group = null)
    {
        foreach ($data as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;
            $currentGroup = $group ?? explode('.', $fullKey)[0];

            if (is_array($value)) {
                $this->syncTranslationRecursive($fullKey, $value, $locale, $created, $updated, $currentGroup);
            } else {
                $translation = Translation::where('key', $fullKey)->first();

                if ($translation) {
                    if ($locale === 'ar') {
                        $translation->value_ar = $value;
                    } else {
                        $translation->value_en = $value;
                    }
                    $translation->save();
                    $updated++;
                } else {
                    Translation::create([
                        'key' => $fullKey,
                        'value_en' => $locale === 'en' ? $value : '',
                        'value_ar' => $locale === 'ar' ? $value : '',
                        'group' => $currentGroup,
                    ]);
                    $created++;
                }
            }
        }
    }

    /**
     * Export translations in JSON format
     *
     * @param string $locale
     * @return \Illuminate\Http\JsonResponse
     */
    public function export($locale = 'en')
    {
        try {
            $translations = Translation::all();
            $result = [];

            foreach ($translations as $translation) {
                $keys = explode('.', $translation->key);
                $current = &$result;

                foreach ($keys as $index => $key) {
                    if ($index === count($keys) - 1) {
                        $current[$key] = $locale === 'ar' ? $translation->value_ar : $translation->value_en;
                    } else {
                        if (!isset($current[$key])) {
                            $current[$key] = [];
                        }
                        $current = &$current[$key];
                    }
                }
            }

            $filename = "{$locale}_translations.json";
            $headers = [
                'Content-Type' => 'application/json',
                'Content-Disposition' => "attachment; filename={$filename}",
            ];

            return response()->json($result, 200, $headers);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export translations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get translation statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        try {
            $total = Translation::count();
            $groups = Translation::getAllGroups();
            $missingArabic = Translation::where('value_ar', '')->orWhereNull('value_ar')->count();
            $missingEnglish = Translation::where('value_en', '')->orWhereNull('value_en')->count();

            $groupStats = [];
            foreach ($groups as $group) {
                $groupStats[$group] = Translation::where('group', $group)->count();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'groups_count' => count($groups),
                    'missing_arabic' => $missingArabic,
                    'missing_english' => $missingEnglish,
                    'groups' => $groupStats,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
