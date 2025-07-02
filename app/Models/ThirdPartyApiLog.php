<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ThirdPartyApiLog extends Model
{
    public $table = 'third_party_api_logs';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'external_reference',
        'third_party_integration_key_id',
        'tenant_uuid',
        'endpoint',
        'method',
        'execution_time_ms'
    ];

    protected $casts = [
        'id' => 'integer',
        'third_party_integration_key_id' => 'integer',
        'execution_time_ms' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public static $rules = [
        'external_reference' => 'required|string|max:255',
        'endpoint' => 'required|string|max:255',
        'method' => 'required|string|max:10'
    ];

    /**
     * Relationship with ThirdPartyIntegrationKeys
     */
    public function thirdPartyIntegrationKey()
    {
        return $this->belongsTo(ThirdPartyIntegrationKeys::class, 'third_party_integration_key_id');
    }


    /**
     * Generate external reference from request payload
     */
    public static function generateExternalReference()
    {
        return 'auto_' . Str::random(16);
    }


    /**
     * Scope to filter by external reference
     */
    public function scopeByExternalReference($query, $externalReference)
    {
        return $query->where('external_reference', $externalReference);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
} 