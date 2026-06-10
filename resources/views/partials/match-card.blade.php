@php
    $homeEntry = $sweepstakeTeams->get(strtolower($match->home_team));
    $awayEntry = $sweepstakeTeams->get(strtolower($match->away_team));

    // Flag: prefer ESPN's CDN url, fall back to our flagcdn lookup
    $homeFlag = $match->home_flag ?: ($homeEntry['flag_url'] ?? null);
    $awayFlag = $match->away_flag ?: ($awayEntry['flag_url'] ?? null);

    $isToday = $isToday ?? $match->match_date?->isToday();
@endphp
<div class="match-card {{ $match->isLive() ? 'live' : ($isToday ? 'today' : '') }}">
    <div class="match-meta">
        <span class="match-stage">{{ $match->stage ?? 'Group Stage' }}</span>
        <span class="match-date">
            @if($match->isLive())
                <span style="color:var(--red); font-family:'DM Mono',monospace; font-size:10px;">{{ $match->status_detail ?? 'LIVE' }}</span>
            @elseif($match->isFinished())
                {{ $match->match_date?->format('d M') }}
            @elseif($isToday)
                <span class="today-badge">⚽ {{ $match->match_date?->format('H:i') }}</span>
            @else
                {{ $match->match_date?->format('D d M · H:i') }}
            @endif
        </span>
    </div>

    <div class="match-teams">
        {{-- Home team --}}
        <div class="match-team">
            @if($homeFlag)
            <img class="match-team-flag" src="{{ $homeFlag }}" alt="{{ $match->home_team }}" loading="lazy" onerror="this.style.display='none'">
            @endif
            <span class="match-team-name {{ $homeEntry ? 'in-sweepstake' : '' }}">{{ $match->home_team }}</span>
            @if($homeEntry)
            <span class="match-team-owner">{{ $homeEntry['person'] }}</span>
            @endif
        </div>

        @if($match->isFinished() || $match->isLive())
        <div class="match-score {{ $match->isLive() ? 'live' : '' }}">
            {{ $match->home_score }} – {{ $match->away_score }}
        </div>
        @else
        <div class="match-vs">vs</div>
        @endif

        {{-- Away team --}}
        <div class="match-team away">
            @if($awayFlag)
            <img class="match-team-flag" src="{{ $awayFlag }}" alt="{{ $match->away_team }}" loading="lazy" onerror="this.style.display='none'">
            @endif
            <span class="match-team-name {{ $awayEntry ? 'in-sweepstake' : '' }}">{{ $match->away_team }}</span>
            @if($awayEntry)
            <span class="match-team-owner">{{ $awayEntry['person'] }}</span>
            @endif
        </div>
    </div>
</div>
