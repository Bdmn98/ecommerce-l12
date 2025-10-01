<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait CommonQueryScopes
{
    public function scopeFilterByPrice(Builder $q, $min = null, $max = null): Builder
    {
        return $q
            ->when(isset($min), fn ($qq) => $qq->where('price', '>=', $min))
            ->when(isset($max), fn ($qq) => $qq->where('price', '<=', $max));
    }

    public function scopeSearchByName($query, $name)
    {
        return $query->when($name, fn($q) => $q->where('name', 'like', "%$name%"));
    }
}

