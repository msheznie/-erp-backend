<?php

namespace App\Casts;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Stores datetime as literal 'Y-m-d H:i:s' without UTC conversion.
 * Keeps exact date/time for tender calendar fields (avoids Laravel 12 datetime shift).
 *
 * - set(): stores the value as-is (string) or formats Carbon to Y-m-d H:i:s
 * - get(): parses the stored string in app timezone and returns Carbon
 */
class DateTimeLiteralCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?CarbonInterface
    {
        if ($value === null || $value === '') {
            return null;
        }
        return Carbon::parse($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }
        if (is_string($value)) {
            return $value;
        }
        if ($value instanceof CarbonInterface) {
            return $value->format('Y-m-d H:i:s');
        }
        return null;
    }
}
