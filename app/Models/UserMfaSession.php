<?php

namespace App\Models;

use Eloquent as Model;

class UserMfaSession extends Model
{
    public $table = 'user_mfa_sessions';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'user_id',
        'mfa_token',
        'method',
        'otp_secret',
        'attempts',
        'resend_count',
        'last_resend_at',
        'expires_at',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'last_resend_at' => 'datetime',
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function scopeExpired($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeVerified($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereNotNull('verified_at');
    }

    public function scopePurgeable($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where(function ($q) {
            $q->where('expires_at', '<', now())
                ->orWhereNotNull('verified_at');
        });
    }
}
