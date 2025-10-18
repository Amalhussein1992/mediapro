<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BrandKit;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandKitController extends Controller
{
    protected $openAI;

    public function __construct(OpenAIService $openAI)
    {
        $this->openAI = $openAI;
    }
    /**
     * Display a listing of brand kits
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $brandKits = $user->brandKits()->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $brandKits
        ]);
    }

    /**
     * Get the default brand kit
     */
    public function getDefault(Request $request)
    {
        $user = $request->user();
        $defaultKit = $user->brandKits()->where('is_default', true)->first();

        if (!$defaultKit) {
            $defaultKit = $user->brandKits()->first();
        }

        return response()->json([
            'success' => true,
            'data' => $defaultKit
        ]);
    }

    /**
     * Store a newly created brand kit
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'colors' => 'nullable|array',
            'fonts' => 'nullable|array',
            'templates' => 'nullable|array',
            'languages' => 'nullable|array',
            'toneOfVoice' => 'nullable|array',
            'guidelines' => 'nullable|array',
            'hashtags' => 'nullable|array',
            'arabicSettings' => 'nullable|array',
            'logo' => 'nullable|image|max:2048',
        ]);

        $user = $request->user();

        // If this is the first brand kit or is_default is set, make it default
        $isFirstKit = $user->brandKits()->count() === 0;

        $data = [
            'user_id' => $user->id,
            'name' => $request->name,
            'colors' => $request->colors,
            'fonts' => $request->fonts,
            'templates' => $request->templates,
            'languages' => $request->languages,
            'tone_of_voice' => $request->toneOfVoice,
            'guidelines' => $request->guidelines,
            'hashtags' => $request->hashtags,
            'arabic_settings' => $request->arabicSettings,
            'is_default' => $isFirstKit || $request->boolean('isDefault'),
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('brand-kits/logos', 'public');
            $data['logo_url'] = $logoPath;
        }

        // If setting as default, unset other defaults
        if ($data['is_default']) {
            $user->brandKits()->update(['is_default' => false]);
        }

        $brandKit = BrandKit::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Brand kit created successfully',
            'data' => $brandKit
        ], 201);
    }

    /**
     * Display the specified brand kit
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $brandKit = $user->brandKits()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $brandKit
        ]);
    }

    /**
     * Update the specified brand kit
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'colors' => 'nullable|array',
            'fonts' => 'nullable|array',
            'templates' => 'nullable|array',
            'languages' => 'nullable|array',
            'toneOfVoice' => 'nullable|array',
            'guidelines' => 'nullable|array',
            'hashtags' => 'nullable|array',
            'arabicSettings' => 'nullable|array',
            'logo' => 'nullable|image|max:2048',
        ]);

        $user = $request->user();
        $brandKit = $user->brandKits()->findOrFail($id);

        $data = array_filter([
            'name' => $request->name,
            'colors' => $request->colors,
            'fonts' => $request->fonts,
            'templates' => $request->templates,
            'languages' => $request->languages,
            'tone_of_voice' => $request->toneOfVoice,
            'guidelines' => $request->guidelines,
            'hashtags' => $request->hashtags,
            'arabic_settings' => $request->arabicSettings,
        ], function ($value) {
            return $value !== null;
        });

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($brandKit->logo_url) {
                Storage::disk('public')->delete($brandKit->logo_url);
            }

            $logoPath = $request->file('logo')->store('brand-kits/logos', 'public');
            $data['logo_url'] = $logoPath;
        }

        $brandKit->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Brand kit updated successfully',
            'data' => $brandKit
        ]);
    }

    /**
     * Set a brand kit as default
     */
    public function setDefault(Request $request, $id)
    {
        $user = $request->user();
        $brandKit = $user->brandKits()->findOrFail($id);

        // Unset all other defaults
        $user->brandKits()->update(['is_default' => false]);

        // Set this one as default
        $brandKit->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Brand kit set as default successfully',
            'data' => $brandKit
        ]);
    }

    /**
     * Remove the specified brand kit
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $brandKit = $user->brandKits()->findOrFail($id);

        // Delete logo file
        if ($brandKit->logo) {
            Storage::disk('public')->delete($brandKit->logo);
        }

        $brandKit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Brand kit deleted successfully'
        ]);
    }

    /**
     * Generate brand kit using AI
     */
    public function generateWithAI(Request $request)
    {
        $request->validate([
            'brand_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'industry' => 'nullable|string|max:200',
            'target_audience' => 'nullable|string|max:500',
        ]);

        try {
            $brandName = $request->brand_name;
            $description = $request->description ?? '';
            $industry = $request->industry ?? '';
            $targetAudience = $request->target_audience ?? '';

            // Build comprehensive prompt for AI
            $prompt = "Generate a comprehensive brand kit for '{$brandName}'.";
            if ($description) $prompt .= " Description: {$description}.";
            if ($industry) $prompt .= " Industry: {$industry}.";
            if ($targetAudience) $prompt .= " Target Audience: {$targetAudience}.";

            $prompt .= "\n\nPlease provide the following in JSON format:
            {
                \"colors\": {
                    \"primary\": \"#hexcolor\",
                    \"secondary\": \"#hexcolor\",
                    \"accent\": \"#hexcolor\",
                    \"background\": \"#hexcolor\",
                    \"text\": \"#hexcolor\"
                },
                \"brand_voice\": \"tone description (professional/friendly/casual/luxury)\",
                \"target_audience_description\": \"detailed audience description\",
                \"hashtags\": [\"#hashtag1\", \"#hashtag2\", ...],
                \"fonts\": {
                    \"primary\": \"font name\",
                    \"secondary\": \"font name\"
                },
                \"tone_of_voice\": [\"characteristic1\", \"characteristic2\", \"characteristic3\"]
            }";

            // Generate using AI service
            $result = $this->openAI->generateCaption($prompt, 'professional', 'general', 'long');
            $aiResponse = $result['caption'];

            // Parse JSON from AI response
            $brandData = $this->parseAIBrandKitResponse($aiResponse);

            return response()->json([
                'success' => true,
                'message' => 'Brand kit generated successfully with AI',
                'data' => $brandData,
                'ai_provider' => $result['source'] ?? 'openai',
            ]);

        } catch (\Exception $e) {
            \Log::error('AI Brand Kit Generation Error: ' . $e->getMessage());

            // Return fallback brand kit
            return response()->json([
                'success' => true,
                'message' => 'Brand kit generated with default values',
                'data' => $this->getFallbackBrandKit($request->brand_name, $request->industry),
                'ai_provider' => 'fallback',
            ]);
        }
    }

    /**
     * Parse AI response to extract brand kit data
     */
    private function parseAIBrandKitResponse($response)
    {
        // Try to extract JSON from response
        if (preg_match('/\{[\s\S]*\}/', $response, $matches)) {
            try {
                $json = json_decode($matches[0], true);
                if ($json && isset($json['colors'])) {
                    return $json;
                }
            } catch (\Exception $e) {
                // JSON parsing failed, use fallback
            }
        }

        // Extract colors with regex
        preg_match_all('/#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})/', $response, $colorMatches);
        $colors = $colorMatches[0] ?? [];

        // Extract hashtags
        preg_match_all('/#\w+/', $response, $hashtagMatches);
        $hashtags = array_slice($hashtagMatches[0] ?? [], 0, 10);

        return [
            'colors' => [
                'primary' => $colors[0] ?? '#6366F1',
                'secondary' => $colors[1] ?? '#8B5CF6',
                'accent' => $colors[2] ?? '#06B6D4',
                'background' => $colors[3] ?? '#FFFFFF',
                'text' => $colors[4] ?? '#1F2937',
            ],
            'brand_voice' => 'professional',
            'target_audience_description' => 'General audience',
            'hashtags' => $hashtags,
            'fonts' => [
                'primary' => 'NotoKufiArabic-Regular',
                'secondary' => 'Montserrat-Regular',
            ],
            'tone_of_voice' => ['professional', 'engaging', 'authentic'],
        ];
    }

    /**
     * Get fallback brand kit when AI fails
     */
    private function getFallbackBrandKit($brandName, $industry)
    {
        // Industry-based color palettes
        $industryPalettes = [
            'technology' => ['#3B82F6', '#60A5FA', '#DBEAFE', '#FFFFFF', '#1F2937'],
            'health' => ['#10B981', '#34D399', '#D1FAE5', '#FFFFFF', '#1F2937'],
            'finance' => ['#6366F1', '#818CF8', '#E0E7FF', '#FFFFFF', '#1F2937'],
            'food' => ['#F59E0B', '#FBBF24', '#FEF3C7', '#FFFFFF', '#1F2937'],
            'fashion' => ['#EC4899', '#F472B6', '#FCE7F3', '#FFFFFF', '#1F2937'],
            'education' => ['#8B5CF6', '#A78BFA', '#EDE9FE', '#FFFFFF', '#1F2937'],
        ];

        $industryKey = strtolower($industry ?? 'technology');
        $palette = $industryPalettes[$industryKey] ?? $industryPalettes['technology'];

        return [
            'colors' => [
                'primary' => $palette[0],
                'secondary' => $palette[1],
                'accent' => $palette[2],
                'background' => $palette[3],
                'text' => $palette[4],
            ],
            'brand_voice' => 'professional',
            'target_audience_description' => ucfirst($industry ?? 'Technology') . ' enthusiasts and professionals',
            'hashtags' => [
                '#' . str_replace(' ', '', $brandName),
                '#' . ucfirst($industry ?? 'Business'),
                '#Innovation',
                '#Quality',
                '#Excellence',
            ],
            'fonts' => [
                'primary' => 'NotoKufiArabic-Bold',
                'secondary' => 'Montserrat-Regular',
            ],
            'tone_of_voice' => ['professional', 'innovative', 'trustworthy'],
        ];
    }
}
