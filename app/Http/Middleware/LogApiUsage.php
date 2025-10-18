<?php

namespace App\Http\Middleware;

use App\Models\ApiUsageLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiUsage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        // Log the API usage asynchronously (you might want to use queues for this in production)
        try {
            ApiUsageLog::create([
                'user_id' => auth()->id(),
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'response_code' => $response->getStatusCode(),
                'response_time_ms' => round($responseTime, 2),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'request_data' => $this->sanitizeData($request->except(['password', 'password_confirmation', 'token'])),
                'response_data' => null, // You can log response data if needed
                'error_message' => $response->getStatusCode() >= 400 ? $this->getErrorMessage($response) : null,
            ]);
        } catch (\Exception $e) {
            // Silently fail to not interrupt the request
            \Log::error('Failed to log API usage: ' . $e->getMessage());
        }

        return $response;
    }

    /**
     * Sanitize sensitive data from logging
     */
    private function sanitizeData(array $data): array
    {
        $sensitiveKeys = ['password', 'token', 'api_key', 'secret'];

        foreach ($sensitiveKeys as $key) {
            if (isset($data[$key])) {
                $data[$key] = '***REDACTED***';
            }
        }

        return $data;
    }

    /**
     * Extract error message from response
     */
    private function getErrorMessage(Response $response): ?string
    {
        try {
            $content = json_decode($response->getContent(), true);
            return $content['message'] ?? $content['error'] ?? 'Unknown error';
        } catch (\Exception $e) {
            return 'Failed to parse error message';
        }
    }
}
