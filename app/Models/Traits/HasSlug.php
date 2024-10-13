<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * The model's slug attribute.
     */
    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn() => Str::slug($this->name),
        );
    }
}
