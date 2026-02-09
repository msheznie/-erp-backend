<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Failed Audit Log Model
 * 
 * Represents audit logs that failed to send to VictoriaLogs
 * Part of Victoria Logs Migration
 * 
 * @property int $id
 * @property array $log_data
 * @property string|null $error_message
 * @property int $retry_count
 * @property \Carbon\Carbon|null $last_retry_at
 * @property \Carbon\Carbon $created_at
 */
class FailedAuditLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'failed_audit_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'log_data',
        'error_message',
        'retry_count',
        'last_retry_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'log_data' => 'array',
        'retry_count' => 'integer',
        'last_retry_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Indicates if the model should use timestamps.
     * Only created_at is used, updated_at is not needed.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'last_retry_at',
        'created_at',
    ];

    /**
     * Scope a query to only include retryable logs.
     * 
     * Retryable logs have:
     * - retry_count < 5 (max retries)
     * - created_at within last 7 days (retention period)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRetryable($query)
    {
        return $query->where('retry_count', '<', 5)
            ->where('created_at', '>', now()->subDays(7))
            ->orderBy('created_at', 'asc');
    }

    /**
     * Scope a query to only include permanently failed logs.
     * 
     * Permanently failed logs have either:
     * - retry_count >= 5 (max retries exceeded)
     * - OR created_at > 7 days ago (expired retention)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePermanentlyFailed($query)
    {
        return $query->where(function ($q) {
            $q->where('retry_count', '>=', 5)
              ->orWhere('created_at', '<=', now()->subDays(7));
        });
    }

    /**
     * Scope a query to filter by channel.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $channel
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByChannel($query, $channel)
    {
        return $query->whereRaw("JSON_EXTRACT(log_data, '$.channel') = ?", [$channel]);
    }

    /**
     * Increment retry count and update last retry timestamp.
     *
     * @return bool
     */
    public function incrementRetry()
    {
        $this->retry_count++;
        $this->last_retry_at = now();
        return $this->save();
    }

    /**
     * Check if this log has exceeded retry limit.
     *
     * @return bool
     */
    public function hasExceededRetryLimit()
    {
        return $this->retry_count >= 5;
    }

    /**
     * Check if this log has expired (older than 7 days).
     *
     * @return bool
     */
    public function hasExpired()
    {
        return $this->created_at->lt(now()->subDays(7));
    }

    /**
     * Get the channel from log_data.
     *
     * @return string
     */
    public function getChannelAttribute()
    {
        return $this->log_data['channel'] ?? 'unknown';
    }

    /**
     * Get the tenant UUID from log_data.
     *
     * @return string|null
     */
    public function getTenantUuidAttribute()
    {
        return $this->log_data['tenant_uuid'] ?? null;
    }

    /**
     * Get the transaction ID from log_data.
     *
     * @return string|null
     */
    public function getTransactionIdAttribute()
    {
        return $this->log_data['transaction_id'] ?? null;
    }
}
