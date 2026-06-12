<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FootballMatch extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'espn_id',
        'home_team',
        'away_team',
        'home_team_abbr',
        'away_team_abbr',
        'home_flag',
        'away_flag',
        'home_score',
        'away_score',
        'status',
        'status_detail',
        'match_date',
        'stage',
        'group_name',
        'venue',
        'stats',
    ];

    protected $casts = [
        'match_date' => 'datetime',
        'home_score' => 'integer',
        'away_score' => 'integer',
        'stats'      => 'array',
    ];

public function scopeFinished($query)
    {
        return $query->where('status', 'finished');
    }

    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')->where('match_date', '>=', now());
    }

    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    public function isFinished(): bool
    {
        return $this->status === 'finished';
    }
}
