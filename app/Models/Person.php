<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    protected $fillable = ['name', 'avatar', 'is_office'];

    protected $casts = [
        'is_office' => 'boolean',
    ];

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=00e87a&color=0a0f1a&bold=true&size=128';
    }
}
