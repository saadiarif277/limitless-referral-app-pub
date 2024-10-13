<?php

namespace App\Models\Traits;

use Carbon\Carbon;

trait HasTimezone
{
    /**
     * Convert a model attribute from application timezone to user's timezone.
     */
    protected function toUserTimezone(string $value): Carbon
    {
        if (!auth()->check()) {
            return Carbon::parse($value);
        }

        $appTimezone = config('app.timezone');
        $userTimezone = auth()->user()->timezone;

        return Carbon::createFromFormat('Y-m-d H:i:s', $value, $appTimezone)->timezone($userTimezone);
    }

    /**
     * Convert a model attribute from user's timezone to application timezone.
     */
    protected function toAppTimezone(string $value): Carbon
    {
        if (!auth()->check()) {
            return Carbon::parse($value);
        }

        $userTimezone = auth()->user()->timezone;
        $appTimezone = config('app.timezone');

        return Carbon::createFromFormat('Y-m-d H:i:s', $value, $userTimezone)->timezone($appTimezone);
    }
}
