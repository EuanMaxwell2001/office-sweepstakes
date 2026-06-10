<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Team extends Model
{
    protected $fillable = ['person_id', 'name', 'country_code', 'is_eliminated'];

    protected $casts = [
        'is_eliminated' => 'boolean',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function getFlagUrlAttribute(): string
    {
        $code = strtolower($this->country_code ?: 'un');
        return "https://flagcdn.com/{$code}.svg";
    }
}
