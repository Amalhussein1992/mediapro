<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use App\Services\VoiceTranscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

/**
 * AI Controller - Comprehensive AI services for social media management
 * Handles content generation, transcription, and AI-powered features
 */
class AIController extends Controller
{
    /**
     * @var AIService
     */
    private $aiService;

    /**
     * @var VoiceTranscriptionService
     */
    private $transcriptionService;

    /**
     * Constructor - Initialize AI services
     */
    public function __construct()
    {
        $this->aiService = new AIService();
        $this->transcriptionService = new VoiceTranscriptionService();
    }

    /**
     * Generate social media post content using AI
     *
     * POST /api/ai/generate-content
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateContent(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'prompt' => 'required|string|max:2000',
            'platform' => 'nullable|string|in:instagram,facebook,twitter,linkedin,tiktok,youtube',
            'tone' => 'nullable|string|in:professional,casual,friendly,enthusiastic,informative',
            'length' => 'nullable|string|in:short,medium,long',
            'language' => 'nullable|string|in:en,ar,es,fr,de',
            'provider' => 'nullable|string|in:openai,gemini,claude',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Set preferred provider if specified
            if ($request->has('provider')) {
                $this->aiService->setPreferredProvider($request->provider);
            }

            // Prepare options
            $options = [
                'platform' => $request->input('platform', 'instagram'),
                'tone' => $request->input('tone', 'professional'),
                'length' => $request->input('length', 'medium'),
                'language' => $request->input('language', 'en'),
            ];

            // Generate content
            $result = $this->aiService->generateContent($request->prompt, $options);

            // Log success
            Log::info('AI content generated successfully', [
                'user_id' => $request->user()->id ?? null,
                'provider' => $result['provider'],
                'platform' => $options['platform'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Content generated successfully',
                'data' => $result,
            ]);
        } catch (Exception $e) {
            Log::error('Content generation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate hashtags for social media content
     *
     * POST /api/ai/generate-hashtags
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateHashtags(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:5000',
            'platform' => 'nullable|string|in:instagram,facebook,twitter,linkedin,tiktok,youtube',
            'count' => 'nullable|integer|min:1|max:30',
            'provider' => 'nullable|string|in:openai,gemini,claude',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Set preferred provider if specified
            if ($request->has('provider')) {
                $this->aiService->setPreferredProvider($request->provider);
            }

            $platform = $request->input('platform', 'instagram');
            $count = $request->input('count', 10);

            // Generate hashtags
            $result = $this->aiService->generateHashtags($request->content, $platform, $count);

            Log::info('Hashtags generated successfully', [
                'user_id' => $request->user()->id ?? null,
                'count' => $result['count'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Hashtags generated successfully',
                'data' => $result,
            ]);
        } catch (Exception $e) {
            Log::error('Hashtag generation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate hashtags',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Transcribe audio file to text using Whisper
     *
     * POST /api/ai/transcribe-voice
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transcribeVoice(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'audio' => 'required|file|mimes:mp3,mp4,mpeg,mpga,m4a,wav,webm|max:25600', // 25MB max
            'language' => 'nullable|string|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $audioFile = $request->file('audio');
            $language = $request->input('language', null);

            // Validate audio file
            $validation = $this->transcriptionService->validateAudioFile($audioFile);

            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid audio file',
                    'errors' => $validation['errors'],
                ], 422);
            }

            // Transcribe audio
            $result = $this->transcriptionService->transcribe($audioFile, $language);

            Log::info('Voice transcription successful', [
                'user_id' => $request->user()->id ?? null,
                'file_size_mb' => $validation['file_size_mb'],
                'language' => $result['language'] ?? 'auto-detected',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Audio transcribed successfully',
                'data' => $result,
            ]);
        } catch (Exception $e) {
            Log::error('Voice transcription failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to transcribe audio',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Transcribe audio and generate social media post
     *
     * POST /api/ai/voice-to-post
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function voiceToPost(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'audio' => 'required|file|mimes:mp3,mp4,mpeg,mpga,m4a,wav,webm|max:25600', // 25MB max
            'platform' => 'required|string|in:instagram,facebook,twitter,linkedin,tiktok,youtube',
            'tone' => 'nullable|string|in:professional,casual,friendly,enthusiastic,informative',
            'length' => 'nullable|string|in:short,medium,long',
            'language' => 'nullable|string|in:en,ar,es,fr,de',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $audioFile = $request->file('audio');
            $platform = $request->input('platform');

            // Prepare options
            $options = [
                'tone' => $request->input('tone', 'professional'),
                'length' => $request->input('length', 'medium'),
                'language' => $request->input('language', null),
            ];

            // Convert voice to post
            $result = $this->transcriptionService->transcribeAndGenerate($audioFile, $platform, $options);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to convert voice to post',
                    'error' => $result['error'] ?? 'Unknown error',
                ], 500);
            }

            Log::info('Voice-to-post conversion successful', [
                'user_id' => $request->user()->id ?? null,
                'platform' => $platform,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Voice successfully converted to post',
                'data' => $result,
            ]);
        } catch (Exception $e) {
            Log::error('Voice-to-post conversion failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to convert voice to post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Enhance existing content with AI
     *
     * POST /api/ai/enhance-content
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function enhanceContent(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:10000',
            'tone' => 'nullable|string|in:professional,casual,friendly,enthusiastic,informative',
            'improvements' => 'nullable|array',
            'improvements.*' => 'string|in:grammar,engagement,clarity,seo,emojis',
            'provider' => 'nullable|string|in:openai,gemini,claude',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Set preferred provider if specified
            if ($request->has('provider')) {
                $this->aiService->setPreferredProvider($request->provider);
            }

            $tone = $request->input('tone', 'professional');
            $improvements = $request->input('improvements', ['grammar', 'engagement', 'clarity']);

            // Enhance content
            $result = $this->aiService->enhanceContent($request->content, $tone, $improvements);

            Log::info('Content enhanced successfully', [
                'user_id' => $request->user()->id ?? null,
                'improvements' => $improvements,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Content enhanced successfully',
                'data' => $result,
            ]);
        } catch (Exception $e) {
            Log::error('Content enhancement failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to enhance content',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get list of available AI providers
     *
     * GET /api/ai/providers
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProviders(Request $request)
    {
        try {
            $providers = $this->aiService->getAvailableProviders();

            return response()->json([
                'success' => true,
                'message' => 'Available AI providers retrieved',
                'data' => [
                    'providers' => $providers,
                    'configured_count' => count(array_filter($providers, fn($p) => $p['available'])),
                    'total_count' => count($providers),
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Failed to get providers: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve AI providers',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set preferred AI provider for user
     *
     * POST /api/ai/set-provider
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setProvider(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string|in:openai,gemini,claude',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $provider = $request->input('provider');

            // Check if provider is configured
            $providers = $this->aiService->getAvailableProviders();

            if (!isset($providers[$provider]) || !$providers[$provider]['available']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected provider is not configured or available',
                    'error' => 'Provider not available',
                ], 400);
            }

            // Set preferred provider
            $this->aiService->setPreferredProvider($provider);

            // In a real application, you would save this preference to the user's settings
            // For now, we just confirm the selection

            Log::info('AI provider preference set', [
                'user_id' => $request->user()->id ?? null,
                'provider' => $provider,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Preferred AI provider set successfully',
                'data' => [
                    'provider' => $provider,
                    'name' => $providers[$provider]['name'],
                    'model' => $providers[$provider]['model'],
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Failed to set provider: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to set AI provider',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate multilingual content
     *
     * POST /api/ai/generate-multilingual
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateMultilingual(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'prompt' => 'required|string|max:2000',
            'languages' => 'required|array|min:1|max:5',
            'languages.*' => 'string|in:en,ar,es,fr,de',
            'platform' => 'nullable|string|in:instagram,facebook,twitter,linkedin,tiktok,youtube',
            'tone' => 'nullable|string|in:professional,casual,friendly,enthusiastic,informative',
            'length' => 'nullable|string|in:short,medium,long',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $options = [
                'platform' => $request->input('platform', 'instagram'),
                'tone' => $request->input('tone', 'professional'),
                'length' => $request->input('length', 'medium'),
            ];

            // Generate multilingual content
            $result = $this->aiService->generateMultilingualContent(
                $request->prompt,
                $request->languages,
                $options
            );

            Log::info('Multilingual content generated', [
                'user_id' => $request->user()->id ?? null,
                'languages' => $request->languages,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Multilingual content generated successfully',
                'data' => $result,
            ]);
        } catch (Exception $e) {
            Log::error('Multilingual generation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate multilingual content',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get voice transcription service info
     *
     * GET /api/ai/transcription-info
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTranscriptionInfo(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'supported_formats' => $this->transcriptionService->getSupportedFormats(),
                    'max_file_size' => $this->transcriptionService->getMaxFileSize(),
                    'max_file_size_mb' => round($this->transcriptionService->getMaxFileSize() / 1024 / 1024, 2),
                    'model' => 'whisper-1',
                    'provider' => 'OpenAI Whisper',
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get transcription info',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
