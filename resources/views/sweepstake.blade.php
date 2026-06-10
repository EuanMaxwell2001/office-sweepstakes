<!DOCTYPE html>
<html lang="en" x-data="sweepstake()" x-init="init()">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>World Cup 2026 — Office Sweepstake</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,700;1,9..40,300&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --bg:       #07080d;
            --surface:  #0e1018;
            --surface2: #151720;
            --border:   rgba(255,255,255,0.07);
            --border2:  rgba(255,255,255,0.13);
            --green:    #00e87a;
            --green-dim: rgba(0,232,122,0.12);
            --green-glow: rgba(0,232,122,0.25);
            --gold:     #f5c518;
            --red:      #ff4040;
            --text:     #e8edf5;
            --muted:    rgba(232,237,245,0.45);
            --muted2:   rgba(232,237,245,0.2);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            line-height: 1.5;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Background pitch pattern ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                repeating-linear-gradient(
                    0deg,
                    transparent,
                    transparent 60px,
                    rgba(255,255,255,0.012) 60px,
                    rgba(255,255,255,0.012) 61px
                ),
                repeating-linear-gradient(
                    90deg,
                    transparent,
                    transparent 60px,
                    rgba(255,255,255,0.008) 60px,
                    rgba(255,255,255,0.008) 61px
                );
            pointer-events: none;
            z-index: 0;
        }

        /* ── Radial glow top ── */
        body::after {
            content: '';
            position: fixed;
            top: -200px;
            left: 50%;
            transform: translateX(-50%);
            width: 900px;
            height: 600px;
            background: radial-gradient(ellipse at center, rgba(0,232,122,0.06) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .wrap { max-width: 1300px; margin: 0 auto; padding: 0 24px; position: relative; z-index: 1; }

        /* ── HERO ── */
        .hero {
            padding: 64px 0 48px;
            border-bottom: 1px solid var(--border);
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--green);
            background: var(--green-dim);
            border: 1px solid rgba(0,232,122,0.25);
            border-radius: 100px;
            padding: 4px 14px;
            margin-bottom: 20px;
            opacity: 0;
            transform: translateY(8px);
            animation: fadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) 0.1s forwards;
        }

        .hero-eyebrow .dot {
            width: 6px; height: 6px;
            background: var(--green);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        .hero-title {
            font-family: 'Anton', sans-serif;
            font-size: clamp(52px, 8vw, 96px);
            line-height: 0.95;
            letter-spacing: -0.02em;
            text-transform: uppercase;
            opacity: 0;
            transform: translateY(16px);
            animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.2s forwards;
        }

        .hero-title .accent { color: var(--green); }

        .hero-sub {
            margin-top: 16px;
            color: var(--muted);
            font-size: 16px;
            font-weight: 300;
            max-width: 480px;
            opacity: 0;
            transform: translateY(12px);
            animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.35s forwards;
        }

        .hero-stats {
            display: flex;
            gap: 32px;
            margin-top: 40px;
            opacity: 0;
            animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.5s forwards;
        }

        .hero-stat { display: flex; flex-direction: column; gap: 2px; }
        .hero-stat-val {
            font-family: 'Anton', sans-serif;
            font-size: 36px;
            color: var(--green);
            line-height: 1;
        }
        .hero-stat-label {
            font-size: 11px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
        }

        /* ── SECTION HEADERS ── */
        .section { padding: 56px 0; }
        .section + .section { border-top: 1px solid var(--border); }

        .section-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .section-title {
            font-family: 'Anton', sans-serif;
            font-size: 28px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .section-badge {
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
        }

        /* ── LIVE TICKER ── */
        .live-bar {
            background: linear-gradient(135deg, rgba(255,64,64,0.12), rgba(255,64,64,0.06));
            border: 1px solid rgba(255,64,64,0.25);
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
        }

        .live-badge {
            background: var(--red);
            color: #fff;
            font-family: 'DM Mono', monospace;
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 4px;
            flex-shrink: 0;
            animation: pulse 1.5s ease-in-out infinite;
        }

        /* ── MATCH GRID ── */
        .match-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 12px;
        }

        .match-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 20px;
            transition: border-color 0.2s, transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s;
            cursor: default;
        }

        .match-card:hover {
            border-color: var(--border2);
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        }

        .match-card.live {
            border-color: rgba(255,64,64,0.35);
            background: linear-gradient(135deg, rgba(255,64,64,0.06), var(--surface));
        }

        .match-card.today {
            border-color: rgba(245,197,24,0.3);
            background: linear-gradient(135deg, rgba(245,197,24,0.05), var(--surface));
        }

        .today-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: 'DM Mono', monospace;
            font-size: 10px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--gold);
            background: rgba(245,197,24,0.1);
            border: 1px solid rgba(245,197,24,0.25);
            border-radius: 4px;
            padding: 2px 8px;
        }

        .match-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .match-stage {
            font-size: 10px;
            font-family: 'DM Mono', monospace;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .match-date {
            font-size: 11px;
            color: var(--muted);
        }

        .match-teams {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 12px;
        }

        .match-team {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .match-team.away { text-align: right; }

        .match-team-flag {
            width: 32px;
            height: 22px;
            object-fit: cover;
            border-radius: 3px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.4);
        }

        .match-team.away .match-team-flag { margin-left: auto; }

        .match-team-name {
            font-size: 13px;
            font-weight: 500;
            line-height: 1.2;
        }

        .match-team-name.in-sweepstake {
            color: var(--green);
        }

        .match-score {
            text-align: center;
            font-family: 'Anton', sans-serif;
            font-size: 26px;
            letter-spacing: 0.05em;
            line-height: 1;
            color: var(--text);
        }

        .match-score.live { color: var(--red); }

        .match-vs {
            text-align: center;
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            color: var(--muted2);
            letter-spacing: 0.1em;
        }

        .match-team-owner {
            font-size: 10px;
            font-family: 'DM Mono', monospace;
            letter-spacing: 0.06em;
            color: var(--green);
            opacity: 0.8;
            margin-top: 1px;
        }

        .match-team.away .match-team-owner { text-align: right; }

        /* ── PEOPLE GRID ── */
        .people-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 16px;
        }

        .person-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            transition: border-color 0.25s, transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.35s;
            opacity: 0;
            transform: translateY(20px);
        }

        .person-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .person-card:hover {
            border-color: var(--border2);
            transform: translateY(-4px) scale(1.005);
            box-shadow: 0 16px 48px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.05);
        }

        .person-card.office {
            border-color: rgba(245,197,24,0.2);
            background: linear-gradient(135deg, rgba(245,197,24,0.04), var(--surface));
        }

        .person-card.office:hover {
            border-color: rgba(245,197,24,0.4);
        }

        .person-header {
            padding: 20px 20px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 1px solid var(--border);
        }

        .person-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--border2);
            flex-shrink: 0;
            transition: border-color 0.2s, transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .person-card:hover .person-avatar {
            border-color: var(--green);
            transform: scale(1.08) rotate(-2deg);
        }

        .person-card.office:hover .person-avatar {
            border-color: var(--gold);
        }

        .person-info { flex: 1; min-width: 0; }

        .person-name {
            font-family: 'Anton', sans-serif;
            font-size: 20px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            line-height: 1;
        }

        .person-card.office .person-name { color: var(--gold); }

        .person-team-count {
            font-size: 11px;
            color: var(--muted);
            letter-spacing: 0.05em;
            margin-top: 3px;
        }

        .person-score {
            text-align: right;
        }

        .person-score-val {
            font-family: 'Anton', sans-serif;
            font-size: 28px;
            line-height: 1;
            color: var(--green);
        }

        .person-score-label {
            font-size: 9px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .teams-list {
            padding: 16px 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .team-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid transparent;
            transition: background 0.2s, border-color 0.2s, transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .team-row:hover {
            background: var(--surface2);
            border-color: var(--border);
            transform: translateX(3px);
        }

        .team-row.eliminated {
            opacity: 0.4;
        }

        .team-row.eliminated .team-name {
            text-decoration: line-through;
        }

        .team-flag {
            width: 36px;
            height: 25px;
            object-fit: cover;
            border-radius: 3px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.4);
            flex-shrink: 0;
        }

        .team-name {
            font-size: 14px;
            font-weight: 500;
            flex: 1;
        }

        .team-eliminated-badge {
            font-family: 'DM Mono', monospace;
            font-size: 9px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--red);
            background: rgba(255,64,64,0.1);
            border: 1px solid rgba(255,64,64,0.2);
            padding: 2px 6px;
            border-radius: 4px;
        }

        /* ── ANIMATIONS ── */
        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ── TABS ── */
        .tabs {
            display: flex;
            gap: 4px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 4px;
            width: fit-content;
            margin-bottom: 24px;
        }

        .tab-btn {
            padding: 8px 18px;
            border-radius: 7px;
            border: none;
            background: transparent;
            color: var(--muted);
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .tab-btn.active {
            background: var(--surface2);
            color: var(--text);
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        .tab-btn:hover:not(.active) {
            color: var(--text);
            background: rgba(255,255,255,0.04);
        }

        /* ── EMPTY STATE ── */
        .empty {
            text-align: center;
            padding: 48px 24px;
            color: var(--muted);
            font-size: 14px;
        }

        .empty-icon {
            font-size: 32px;
            margin-bottom: 12px;
            display: block;
        }

        /* ── FOOTER ── */
        .footer {
            border-top: 1px solid var(--border);
            padding: 32px 0;
            text-align: center;
            color: var(--muted2);
            font-size: 12px;
            letter-spacing: 0.05em;
        }

        /* ── NAV ── */
        .nav {
            border-bottom: 1px solid var(--border);
            padding: 18px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-logo {
            height: 22px;
            width: auto;
            opacity: 0.9;
            transition: opacity 0.2s;
        }

        .nav-logo:hover { opacity: 1; }

        .nav-pill {
            font-family: 'DM Mono', monospace;
            font-size: 10px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--muted);
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 5px 14px;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 640px) {
            .hero { padding: 40px 0 32px; }
            .hero-stats { gap: 20px; }
            .people-grid { grid-template-columns: 1fr; }
            .match-grid { grid-template-columns: 1fr; }
            .tabs { overflow-x: auto; }
        }
    </style>
</head>
<body>

<div class="wrap">

    {{-- ── NAV ── --}}
    <nav class="nav">
        <img src="https://www.weareeveryone.com/theme/images/site/everyone-white.svg" alt="Everyone" class="nav-logo">
        <span class="nav-pill">World Cup 2026</span>
    </nav>

    {{-- ── HERO ── --}}
    <header class="hero">
        <div class="hero-eyebrow">
            <span class="dot"></span>
            FIFA World Cup 2026
        </div>
        <h1 class="hero-title">
            Office<br><span class="accent">Sweepstake</span>
        </h1>
        <p class="hero-sub">Who's taking home the glory? Track your teams, watch the results unfold.</p>
        <div class="hero-stats">
            <div class="hero-stat">
                <span class="hero-stat-val">{{ $people->where('is_office', false)->count() }}</span>
                <span class="hero-stat-label">Players</span>
            </div>
            <div class="hero-stat">
                <span class="hero-stat-val">{{ $people->sum(fn($p) => $p->teams->count()) }}</span>
                <span class="hero-stat-label">Teams</span>
            </div>
            @if($liveMatches->count())
            <div class="hero-stat">
                <span class="hero-stat-val" style="color:var(--red)">{{ $liveMatches->count() }}</span>
                <span class="hero-stat-label">Live Now</span>
            </div>
            @else
            <div class="hero-stat">
                <span class="hero-stat-val">{{ $recentMatches->count() + $upcomingMatches->count() }}</span>
                <span class="hero-stat-label">Matches</span>
            </div>
            @endif
        </div>
    </header>

    {{-- ── LIVE MATCHES ── --}}
    @if($liveMatches->count())
    <section class="section">
        <div class="live-bar">
            <span class="live-badge">Live</span>
            <span style="font-size:13px; color:var(--muted)">{{ $liveMatches->count() }} {{ Str::plural('match', $liveMatches->count()) }} in progress right now</span>
        </div>
        <div class="match-grid">
            @foreach($liveMatches as $match)
            @include('partials.match-card', ['match' => $match, 'sweepstakeTeams' => $sweepstakeTeams])
            @endforeach
        </div>
    </section>
    @endif

    {{-- ── TODAY ── --}}
    @if($todayMatches->count())
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">Today</h2>
            <span class="section-badge">{{ now()->format('d M Y') }}</span>
        </div>
        <div class="match-grid">
            @foreach($todayMatches as $match)
            @include('partials.match-card', ['match' => $match, 'sweepstakeTeams' => $sweepstakeTeams, 'isToday' => true])
            @endforeach
        </div>
    </section>
    @endif

    {{-- ── UPCOMING ── --}}
    @if($upcomingMatches->count())
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">Upcoming</h2>
            <span class="section-badge">{{ $upcomingMatches->count() }} matches</span>
        </div>
        <div class="match-grid">
            @foreach($upcomingMatches as $match)
            @include('partials.match-card', ['match' => $match, 'sweepstakeTeams' => $sweepstakeTeams, 'isToday' => false])
            @endforeach
        </div>
    </section>
    @endif

    {{-- ── RESULTS ── --}}
    @if($recentMatches->count())
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">Results</h2>
            <span class="section-badge">{{ $recentMatches->count() }} matches</span>
        </div>
        <div class="match-grid">
            @foreach($recentMatches as $match)
            @include('partials.match-card', ['match' => $match, 'sweepstakeTeams' => $sweepstakeTeams])
            @endforeach
        </div>
    </section>
    @endif

    {{-- ── THE SWEEPSTAKE ── --}}
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">The Players</h2>
            <span class="section-badge">{{ $people->count() }} entries</span>
        </div>

        <div class="people-grid" id="people-grid">
            @foreach($people as $i => $person)
            <div class="person-card {{ $person->is_office ? 'office' : '' }}" data-index="{{ $i }}">
                <div class="person-header">
                    <img
                        class="person-avatar"
                        src="{{ $person->avatar_url }}"
                        alt="{{ $person->name }}"
                        loading="lazy"
                    >
                    <div class="person-info">
                        <div class="person-name">{{ $person->name }}</div>
                        <div class="person-team-count">{{ $person->teams->count() }} {{ Str::plural('team', $person->teams->count()) }}</div>
                    </div>
                    @php
                        $active = $person->teams->where('is_eliminated', false)->count();
                    @endphp
                    <div class="person-score">
                        <div class="person-score-val" style="{{ $person->is_office ? 'color:var(--gold)' : '' }}">
                            {{ $active }}
                        </div>
                        <div class="person-score-label">Still In</div>
                    </div>
                </div>

                <div class="teams-list">
                    @foreach($person->teams->sortBy('is_eliminated') as $team)
                    <div class="team-row {{ $team->is_eliminated ? 'eliminated' : '' }}">
                        @if($team->country_code)
                        <img
                            class="team-flag"
                            src="https://flagcdn.com/{{ strtolower($team->country_code) }}.svg"
                            alt="{{ $team->name }}"
                            loading="lazy"
                            onerror="this.style.display='none'"
                        >
                        @else
                        <div class="team-flag" style="background:var(--surface2); border-radius:3px;"></div>
                        @endif
                        <span class="team-name">{{ $team->name }}</span>
                        @if($team->is_eliminated)
                        <span class="team-eliminated-badge">Out</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </section>

</div>

<footer class="footer">
    <div class="wrap" style="display:flex; align-items:center; justify-content:center; gap:20px; flex-wrap:wrap;">
        <img src="https://www.weareeveryone.com/theme/images/site/everyone-white.svg" alt="Everyone" style="height:16px; opacity:0.4;">
        <span>World Cup 2026 Sweepstake</span>
        <span style="color:var(--muted2)">·</span>
        <span>Match data via ESPN</span>
        <span style="color:var(--muted2)">·</span>
        <a href="/admin" style="color:var(--muted); text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='var(--green)'" onmouseout="this.style.color='var(--muted)'">Admin</a>
    </div>
</footer>

<script>
function sweepstake() {
    return {
        init() {
            this.animateCards();
        },
        animateCards() {
            const cards = document.querySelectorAll('.person-card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, i) => {
                    if (entry.isIntersecting) {
                        const idx = parseInt(entry.target.dataset.index ?? 0);
                        setTimeout(() => {
                            entry.target.classList.add('visible');
                        }, idx * 60);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

            cards.forEach(card => observer.observe(card));
        }
    };
}
</script>

</body>
</html>
