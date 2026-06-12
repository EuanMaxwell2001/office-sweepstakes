@php
    $homeEntry = $sweepstakeTeams->get(strtolower($match->home_team));
    $awayEntry = $sweepstakeTeams->get(strtolower($match->away_team));

    $homeFlag = $match->home_flag ?: ($homeEntry['flag_url'] ?? null);
    $awayFlag = $match->away_flag ?: ($awayEntry['flag_url'] ?? null);

    $isToday    = $isToday ?? $match->match_date?->isToday();
    $isUpcoming = ! $match->isFinished() && ! $match->isLive();

    $battleData = $isUpcoming ? json_encode([
        'homeTeam'   => $match->home_team,
        'awayTeam'   => $match->away_team,
        'homeFlag'   => $homeFlag,
        'awayFlag'   => $awayFlag,
        'homePerson' => $homeEntry['person'] ?? null,
        'awayPerson' => $awayEntry['person'] ?? null,
        'homeAvatar' => $homeEntry['avatar_url'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($match->home_team) . '&background=0d1421&color=ffffff&size=128',
        'awayAvatar' => $awayEntry['avatar_url'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($match->away_team) . '&background=1a0808&color=ffffff&size=128',
        'stage'      => $match->stage,
        'venue'      => $match->venue,
        'kickoff'    => $match->match_date?->format('H:i'),
    ]) : 'null';

    $isDraw      = $match->isFinished() && $match->home_score === $match->away_score;
    $homeWon     = $match->isFinished() && $match->home_score > $match->away_score;
    $winnerEntry = $homeWon ? $homeEntry : $awayEntry;
    $winnerTeam  = $homeWon ? $match->home_team : $match->away_team;

    $resultData = $match->isFinished() ? json_encode([
        'isDraw'       => $isDraw,
        'homeScore'    => $match->home_score,
        'awayScore'    => $match->away_score,
        'homeTeam'     => $match->home_team,
        'awayTeam'     => $match->away_team,
        'homeAvatar'   => $homeEntry['avatar_url'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($match->home_team) . '&background=0d1421&color=ffffff&size=128',
        'awayAvatar'   => $awayEntry['avatar_url'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($match->away_team) . '&background=1a0808&color=ffffff&size=128',
        'winnerPerson' => $winnerEntry['person'] ?? null,
        'winnerTeam'   => $winnerTeam,
        'winnerAvatar' => $winnerEntry['avatar_url'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($winnerTeam) . '&background=061a0e&color=00e87a&size=128',
        'winnerFlag'   => $homeWon ? $homeFlag : $awayFlag,
        'stage'        => $match->stage,
        'stats'        => $match->stats,
    ]) : 'null';
@endphp

<div
    class="match-card {{ $match->isLive() ? 'live' : ($isToday ? 'today' : '') }} {{ $isUpcoming ? 'upcoming-card' : ($match->isFinished() ? 'upcoming-card' : '') }}"
    @if($isUpcoming)
        @click="openBattle(JSON.parse($el.dataset.battle))"
        data-battle='{{ $battleData }}'
    @elseif($match->isFinished())
        @click="console.log('card clicked', $el.dataset.result); openResult(JSON.parse($el.dataset.result))"
        data-result='{{ $resultData }}'
    @endif
>
    <div class="match-meta">
        <span class="match-stage">{{ $match->stage ?? 'Group Stage' }}</span>
        <span class="match-date">
            @if($match->isLive())
                <span style="color:var(--red); font-family:'DM Mono',monospace; font-size:10px;">{{ $match->status_detail ?? 'LIVE' }}</span>
            @elseif($match->isFinished())
                <span style="font-family:'DM Mono',monospace; font-size:10px; color:var(--muted); background:var(--surface2); border:1px solid var(--border); border-radius:4px; padding:2px 6px; letter-spacing:0.1em;">FT</span>
                <span style="margin-left:4px;">{{ $match->match_date?->format('d M') }}</span>
            @elseif($isToday)
                <span class="today-badge">⚽ {{ $match->match_date?->format('H:i') }}</span>
            @else
                {{ $match->match_date?->format('D d M · H:i') }}
            @endif
        </span>
    </div>

    <div class="match-teams">
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
