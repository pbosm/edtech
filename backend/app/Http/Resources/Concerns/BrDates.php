<?php

namespace App\Http\Resources\Concerns;

use Illuminate\Support\Carbon;

trait BrDates
{
    protected function brDate($value): ?string
    {
        if (!$value)
            return null;

        $c = $value instanceof \DateTimeInterface ? Carbon::instance($value) : Carbon::parse($value);

        return $c->format('d/m/Y');
    }
}
