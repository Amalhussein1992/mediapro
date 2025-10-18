<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'endpoint',
        'method',
        'response_code',
        'response_time_ms',
        'ip_address',
        'user_agent',
        'request_data',
        'response_data',
        'error_message',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'response_time_ms' => 'integer',
        'response_code' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that made the API call
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by endpoint
     */
    public function scopeByEndpoint($query, string $endpoint)
    {
        return $query->where('endpoint', 'like', "%{$endpoint}%");
    }

    /**
     * Scope to filter by response code
     */
    public function scopeByResponseCode($query, int $code)
    {
        return $query->where('response_code', $code);
    }

    /**
     * Scope to filter errors
     */
    public function scopeErrors($query)
    {
        return $query->where('response_code', '>=', 400);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Static method to log API usage
     */
    public static function logUsage(
        string $endpoint,
        string $method,
        int $responseCode,
        int $responseTimeMs,
        ?array $requestData = null,
        ?array $responseData = null,
        ?string $errorMessage = null
    ): void {
        self::create([
            'user_id' => auth()->id(),
            'endpoint' => $endpoint,
            'method' => $method,
            'response_code' => $responseCode,
            'response_time_ms' => $responseTimeMs,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'request_data' => $requestData,
            'response_data' => $responseData,
            'error_message' => $errorMessage,
        ]);
    }
}
