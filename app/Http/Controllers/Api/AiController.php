<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiGeneration;
use App\Models\BrandKit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    // Generate Image using AI
    public function generateImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prompt' => 'required|string',
            'brand_kit_id' => 'nullable|exists:brand_kits,id',
            'size' => 'nullable|string|in:256x256,512x512,1024x1024',
            'style' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $aiGeneration = AiGeneration::create([
            'user_id' => $request->user()->id,
            'brand_kit_id' => $request->brand_kit_id,
            'type' => AiGeneration::TYPE_IMAGE,
            'prompt' => $request->prompt,
            'settings' => [
                'size' => $request->size ?? '1024x1024',
                'style' => $request->style,
            ],
            'status' => AiGeneration::STATUS_PROCESSING,
        ]);

        try {
            // TODO: Integrate with actual AI service (OpenAI DALL-E, Midjourney, etc.)
            // For now, return a mock response
            $result = [
                'image_url' => 'https://via.placeholder.com/1024',
                'thumbnail_url' => 'https://via.placeholder.com/256',
                'generated_at' => now()->toISOString(),
            ];

            $aiGeneration->update([
                'result' => json_encode($result),
                'status' => AiGeneration::STATUS_COMPLETED,
                'tokens_used' => 1000,
            ]);

            return response()->json([
                'message' => 'Image generated successfully',
                'generation' => $aiGeneration->fresh(),
            ]);
        } catch (\Exception $e) {
            $aiGeneration->update([
                'status' => AiGeneration::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Failed to generate image'], 500);
        }
    }

    // Generate Video Script using AI
    public function generateVideoScript(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topic' => 'required|string',
            'brand_kit_id' => 'nullable|exists:brand_kits,id',
            'duration' => 'nullable|integer|min:30|max:600',
            'platform' => 'nullable|string|in:youtube,tiktok,instagram,facebook',
            'tone' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $brandKit = null;
        if ($request->brand_kit_id) {
            $brandKit = BrandKit::find($request->brand_kit_id);
        }

        $prompt = "Generate a video script for: {$request->topic}";
        if ($brandKit) {
            $prompt .= "\nBrand Voice: {$brandKit->voice}";
            $prompt .= "\nTone: {$brandKit->tone}";
        }
        if ($request->duration) {
            $prompt .= "\nDuration: {$request->duration} seconds";
        }
        if ($request->platform) {
            $prompt .= "\nPlatform: {$request->platform}";
        }

        $aiGeneration = AiGeneration::create([
            'user_id' => $request->user()->id,
            'brand_kit_id' => $request->brand_kit_id,
            'type' => AiGeneration::TYPE_VIDEO_SCRIPT,
            'prompt' => $prompt,
            'settings' => [
                'topic' => $request->topic,
                'duration' => $request->duration ?? 60,
                'platform' => $request->platform,
                'tone' => $request->tone ?? ($brandKit->tone ?? 'professional'),
            ],
            'status' => AiGeneration::STATUS_PROCESSING,
        ]);

        try {
            // TODO: Integrate with actual AI service (OpenAI GPT-4, etc.)
            $result = [
                'script' => "Hook: [Opening statement]\n\nIntroduction: [Introduce the topic]\n\nMain Content: [Key points]\n\nCall to Action: [Closing statement]",
                'estimated_duration' => $request->duration ?? 60,
                'scenes' => 4,
                'generated_at' => now()->toISOString(),
            ];

            $aiGeneration->update([
                'result' => json_encode($result),
                'status' => AiGeneration::STATUS_COMPLETED,
                'tokens_used' => 500,
            ]);

            return response()->json([
                'message' => 'Video script generated successfully',
                'generation' => $aiGeneration->fresh(),
            ]);
        } catch (\Exception $e) {
            $aiGeneration->update([
                'status' => AiGeneration::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Failed to generate video script'], 500);
        }
    }

    // Transcribe Audio to Text
    public function transcribeAudio(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'audio_file' => 'required|file|mimes:mp3,wav,m4a,ogg|max:25600',
            'language' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $audioPath = $request->file('audio_file')->store('audio-transcriptions', 'public');

        $aiGeneration = AiGeneration::create([
            'user_id' => $request->user()->id,
            'type' => AiGeneration::TYPE_AUDIO_TRANSCRIPTION,
            'prompt' => 'Audio file: ' . $audioPath,
            'settings' => [
                'file_path' => $audioPath,
                'language' => $request->language ?? 'auto',
            ],
            'status' => AiGeneration::STATUS_PROCESSING,
        ]);

        try {
            // TODO: Integrate with actual AI service (OpenAI Whisper, Google Speech-to-Text, etc.)
            $result = [
                'transcription' => 'This is a sample transcription of the audio file.',
                'language' => $request->language ?? 'en',
                'duration' => 60,
                'confidence' => 0.95,
                'generated_at' => now()->toISOString(),
            ];

            $aiGeneration->update([
                'result' => json_encode($result),
                'status' => AiGeneration::STATUS_COMPLETED,
                'tokens_used' => 300,
            ]);

            return response()->json([
                'message' => 'Audio transcribed successfully',
                'generation' => $aiGeneration->fresh(),
            ]);
        } catch (\Exception $e) {
            $aiGeneration->update([
                'status' => AiGeneration::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Failed to transcribe audio'], 500);
        }
    }

    // Generate Social Media Content
    public function generateSocialContent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topic' => 'required|string',
            'brand_kit_id' => 'nullable|exists:brand_kits,id',
            'platform' => 'required|string|in:facebook,instagram,twitter,linkedin,tiktok',
            'content_type' => 'required|string|in:post,story,reel,tweet,article',
            'include_hashtags' => 'nullable|boolean',
            'include_emojis' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $brandKit = null;
        if ($request->brand_kit_id) {
            $brandKit = BrandKit::find($request->brand_kit_id);
        }

        $prompt = "Generate {$request->content_type} content for {$request->platform} about: {$request->topic}";
        if ($brandKit) {
            $prompt .= "\nBrand Voice: {$brandKit->voice}";
            $prompt .= "\nTone: {$brandKit->tone}";
            if ($brandKit->keywords) {
                $prompt .= "\nKeywords: " . implode(', ', $brandKit->keywords);
            }
        }
        if ($user->type_of_audience) {
            $prompt .= "\nTarget Audience: {$user->type_of_audience}";
        }

        $aiGeneration = AiGeneration::create([
            'user_id' => $user->id,
            'brand_kit_id' => $request->brand_kit_id,
            'type' => AiGeneration::TYPE_SOCIAL_CONTENT,
            'prompt' => $prompt,
            'settings' => [
                'topic' => $request->topic,
                'platform' => $request->platform,
                'content_type' => $request->content_type,
                'include_hashtags' => $request->include_hashtags ?? true,
                'include_emojis' => $request->include_emojis ?? true,
            ],
            'status' => AiGeneration::STATUS_PROCESSING,
        ]);

        try {
            // TODO: Integrate with actual AI service (OpenAI GPT-4, etc.)
            $hashtags = $request->include_hashtags ? ['#topic', '#socialmedia', '#content'] : [];

            $result = [
                'content' => "This is a sample {$request->content_type} for {$request->platform}. " .
                             "It follows the brand guidelines and targets the specified audience.",
                'hashtags' => $hashtags,
                'estimated_reach' => rand(1000, 10000),
                'best_time_to_post' => now()->addHours(2)->toISOString(),
                'generated_at' => now()->toISOString(),
            ];

            $aiGeneration->update([
                'result' => json_encode($result),
                'status' => AiGeneration::STATUS_COMPLETED,
                'tokens_used' => 200,
            ]);

            return response()->json([
                'message' => 'Social media content generated successfully',
                'generation' => $aiGeneration->fresh(),
            ]);
        } catch (\Exception $e) {
            $aiGeneration->update([
                'status' => AiGeneration::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Failed to generate social media content'], 500);
        }
    }

    // Get AI Generation History
    public function getHistory(Request $request)
    {
        $type = $request->query('type');

        $query = $request->user()->aiGenerations()->latest();

        if ($type) {
            $query->byType($type);
        }

        $generations = $query->paginate(20);

        return response()->json($generations);
    }

    // Get specific AI Generation
    public function getGeneration(Request $request, $id)
    {
        $generation = $request->user()->aiGenerations()->findOrFail($id);
        return response()->json($generation);
    }
}
