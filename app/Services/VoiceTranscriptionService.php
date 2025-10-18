<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

/**
 * Voice Transcription Service using OpenAI Whisper API
 * Handles audio file transcription and voice-to-post conversion
 */
class VoiceTranscriptionService
{
    // OpenAI Whisper API endpoint
    private $whisperEndpoint = 'https://api.openai.com/v1/audio/transcriptions';

    // Supported audio formats
    const SUPPORTED_FORMATS = ['mp3', 'mp4', 'mpeg', 'mpga', 'm4a', 'wav', 'webm'];

    // Max file size (25MB)
    const MAX_FILE_SIZE = 25 * 1024 * 1024;

    /**
     * @var AIService
     */
    private $aiService;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->aiService = new AIService();
    }

    /**
     * Transcribe audio file to text using OpenAI Whisper
     *
     * @param mixed $audioFile File path or UploadedFile instance
     * @param string|null $language Language code (optional, Whisper can auto-detect)
     * @param string $responseFormat Response format (text, json, srt, vtt, verbose_json)
     * @return array Transcription result with text and metadata
     */
    public function transcribe($audioFile, $language = null, $responseFormat = 'verbose_json')
    {
        // Validate API key
        $apiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY');
        if (!$apiKey) {
            throw new Exception('OpenAI API key not configured. Please add OPENAI_API_KEY to your .env file.');
        }

        // Validate and prepare the audio file
        $filePath = $this->prepareAudioFile($audioFile);

        try {
            // Check file size
            $fileSize = filesize($filePath);
            if ($fileSize > self::MAX_FILE_SIZE) {
                throw new Exception('Audio file size exceeds maximum limit of 25MB');
            }

            // Validate file format
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            if (!in_array($extension, self::SUPPORTED_FORMATS)) {
                throw new Exception('Unsupported audio format. Supported formats: ' . implode(', ', self::SUPPORTED_FORMATS));
            }

            Log::info("Transcribing audio file: {$filePath} (Size: " . round($fileSize / 1024 / 1024, 2) . "MB)");

            // Prepare the request
            $requestData = [
                'model' => 'whisper-1',
                'response_format' => $responseFormat,
            ];

            // Add language if specified
            if ($language) {
                $requestData['language'] = $language;
            }

            // Make the API request with file upload
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])
            ->timeout(120) // Longer timeout for large files
            ->attach('file', file_get_contents($filePath), basename($filePath))
            ->post($this->whisperEndpoint, $requestData);

            // Check response
            if (!$response->successful()) {
                $errorBody = $response->json();
                $errorMessage = $errorBody['error']['message'] ?? $response->body();
                throw new Exception('Whisper API request failed: ' . $errorMessage);
            }

            $result = $response->json();

            // Parse response based on format
            if ($responseFormat === 'verbose_json') {
                return [
                    'success' => true,
                    'text' => $result['text'] ?? '',
                    'language' => $result['language'] ?? $language,
                    'duration' => $result['duration'] ?? null,
                    'segments' => $result['segments'] ?? [],
                    'word_count' => str_word_count($result['text'] ?? ''),
                    'char_count' => mb_strlen($result['text'] ?? ''),
                ];
            } elseif ($responseFormat === 'json') {
                return [
                    'success' => true,
                    'text' => $result['text'] ?? '',
                    'word_count' => str_word_count($result['text'] ?? ''),
                    'char_count' => mb_strlen($result['text'] ?? ''),
                ];
            } else {
                // For text, srt, vtt formats
                return [
                    'success' => true,
                    'text' => $result,
                    'word_count' => str_word_count($result),
                    'char_count' => mb_strlen($result),
                ];
            }
        } catch (Exception $e) {
            Log::error('Transcription failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Transcribe audio and generate social media post
     *
     * @param mixed $audioFile File path or UploadedFile instance
     * @param string $platform Target social media platform
     * @param array $options Additional options (tone, language, etc.)
     * @return array Generated post with transcript
     */
    public function transcribeAndGenerate($audioFile, $platform = 'instagram', $options = [])
    {
        try {
            // Step 1: Transcribe the audio
            Log::info("Starting voice-to-post conversion for platform: {$platform}");

            $language = $options['language'] ?? null;
            $transcriptionResult = $this->transcribe($audioFile, $language);

            if (!$transcriptionResult['success']) {
                throw new Exception('Transcription failed');
            }

            $transcript = $transcriptionResult['text'];
            $detectedLanguage = $transcriptionResult['language'] ?? 'en';

            Log::info("Transcription successful. Detected language: {$detectedLanguage}");

            // Step 2: Generate social media post from transcript
            $tone = $options['tone'] ?? 'professional';
            $length = $options['length'] ?? 'medium';

            $postPrompt = "Convert this transcript into an engaging social media post for {$platform}: {$transcript}";

            $contentResult = $this->aiService->generateContent($postPrompt, [
                'platform' => $platform,
                'tone' => $tone,
                'length' => $length,
                'language' => $detectedLanguage,
            ]);

            // Step 3: Generate hashtags
            $hashtagResult = $this->aiService->generateHashtags($contentResult['content'], $platform, 10);

            Log::info("Voice-to-post conversion completed successfully");

            return [
                'success' => true,
                'transcript' => [
                    'text' => $transcript,
                    'language' => $detectedLanguage,
                    'duration' => $transcriptionResult['duration'] ?? null,
                    'word_count' => $transcriptionResult['word_count'],
                ],
                'post' => [
                    'content' => $contentResult['content'],
                    'hashtags' => $hashtagResult['hashtags'],
                    'platform' => $platform,
                    'tone' => $tone,
                ],
                'metadata' => [
                    'ai_provider' => $contentResult['provider'],
                    'platform' => $platform,
                    'language' => $detectedLanguage,
                ],
            ];
        } catch (Exception $e) {
            Log::error('Voice-to-post conversion failed: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Failed to convert voice to post. Please try again.',
            ];
        }
    }

    /**
     * Transcribe audio with timestamps for video subtitles
     *
     * @param mixed $audioFile File path or UploadedFile instance
     * @param string $format Output format (srt, vtt)
     * @return array Transcription with timestamps
     */
    public function transcribeWithTimestamps($audioFile, $format = 'srt')
    {
        $validFormats = ['srt', 'vtt', 'verbose_json'];

        if (!in_array($format, $validFormats)) {
            throw new Exception("Invalid format. Supported formats: " . implode(', ', $validFormats));
        }

        $result = $this->transcribe($audioFile, null, $format);

        return [
            'success' => true,
            'format' => $format,
            'content' => $result['text'],
            'segments' => $result['segments'] ?? [],
        ];
    }

    /**
     * Batch transcribe multiple audio files
     *
     * @param array $audioFiles Array of audio files
     * @param string|null $language Language code
     * @return array Results for each file
     */
    public function batchTranscribe($audioFiles, $language = null)
    {
        $results = [];

        foreach ($audioFiles as $index => $audioFile) {
            try {
                $result = $this->transcribe($audioFile, $language);
                $results[] = [
                    'index' => $index,
                    'success' => true,
                    'data' => $result,
                ];
            } catch (Exception $e) {
                Log::error("Batch transcription failed for file {$index}: " . $e->getMessage());
                $results[] = [
                    'index' => $index,
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'success' => true,
            'total' => count($audioFiles),
            'successful' => count(array_filter($results, fn($r) => $r['success'])),
            'failed' => count(array_filter($results, fn($r) => !$r['success'])),
            'results' => $results,
        ];
    }

    /**
     * Prepare audio file for transcription
     *
     * @param mixed $audioFile File path or UploadedFile instance
     * @return string File path
     */
    private function prepareAudioFile($audioFile)
    {
        // If it's already a file path string
        if (is_string($audioFile)) {
            if (file_exists($audioFile)) {
                return $audioFile;
            }

            // Check if it's a storage path
            if (Storage::exists($audioFile)) {
                return Storage::path($audioFile);
            }

            throw new Exception('Audio file not found: ' . $audioFile);
        }

        // If it's an UploadedFile instance (from request)
        if (method_exists($audioFile, 'getRealPath')) {
            return $audioFile->getRealPath();
        }

        throw new Exception('Invalid audio file provided');
    }

    /**
     * Get supported audio formats
     *
     * @return array Supported formats
     */
    public function getSupportedFormats()
    {
        return self::SUPPORTED_FORMATS;
    }

    /**
     * Get maximum file size
     *
     * @return int Max file size in bytes
     */
    public function getMaxFileSize()
    {
        return self::MAX_FILE_SIZE;
    }

    /**
     * Validate audio file before upload
     *
     * @param mixed $audioFile File to validate
     * @return array Validation result
     */
    public function validateAudioFile($audioFile)
    {
        $errors = [];

        try {
            $filePath = $this->prepareAudioFile($audioFile);

            // Check if file exists
            if (!file_exists($filePath)) {
                $errors[] = 'File does not exist';
                return ['valid' => false, 'errors' => $errors];
            }

            // Check file size
            $fileSize = filesize($filePath);
            if ($fileSize > self::MAX_FILE_SIZE) {
                $errors[] = 'File size exceeds maximum limit of 25MB (current: ' . round($fileSize / 1024 / 1024, 2) . 'MB)';
            }

            // Check file format
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            if (!in_array($extension, self::SUPPORTED_FORMATS)) {
                $errors[] = 'Unsupported audio format. Supported formats: ' . implode(', ', self::SUPPORTED_FORMATS);
            }

            // Check if file is readable
            if (!is_readable($filePath)) {
                $errors[] = 'File is not readable';
            }

            return [
                'valid' => empty($errors),
                'errors' => $errors,
                'file_size' => $fileSize,
                'file_size_mb' => round($fileSize / 1024 / 1024, 2),
                'format' => $extension,
            ];
        } catch (Exception $e) {
            return [
                'valid' => false,
                'errors' => [$e->getMessage()],
            ];
        }
    }
}
