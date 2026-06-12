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
            font-size: 500;
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

        /* ── BATTLE MODAL ── */
        .battle-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.85);
            backdrop-filter: blur(6px);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .battle-modal {
            position: relative;
            width: 100%;
            max-width: 480px;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 32px 80px rgba(0,0,0,0.8);
        }

        .battle-bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #080f1f 0%, #0a0a0a 50%, #1a0808 100%);
        }

        .battle-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg,
                rgba(0,100,255,0.08) 0%,
                transparent 50%,
                rgba(255,40,40,0.08) 100%
            );
        }

        .battle-bg::after {
            content: '';
            position: absolute;
            top: 0; bottom: 0;
            left: 50%; width: 2px;
            background: linear-gradient(to bottom,
                transparent, rgba(255,255,255,0.15), transparent
            );
            transform: skewX(-8deg);
        }

        .battle-inner {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 0;
            padding: 32px 24px 28px;
        }

        .battle-side {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .battle-side.right { flex-direction: column; }

        .battle-avatar-wrap {
            position: relative;
        }

        .battle-avatar {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255,255,255,0.15);
            display: block;
        }

        .battle-side.left .battle-avatar {
            border-color: rgba(60,140,255,0.5);
            box-shadow: 0 0 24px rgba(60,140,255,0.3), 0 0 0 6px rgba(60,140,255,0.08);
        }

        .battle-side.right .battle-avatar {
            border-color: rgba(255,60,60,0.5);
            box-shadow: 0 0 24px rgba(255,60,60,0.3), 0 0 0 6px rgba(255,60,60,0.08);
        }

        .battle-flag {
            width: 40px;
            height: 28px;
            object-fit: cover;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.5);
        }

        .battle-person {
            font-family: 'Anton', sans-serif;
            font-size: 15px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--text);
            text-align: center;
        }

        .battle-team {
            font-size: 11px;
            color: var(--muted);
            text-align: center;
            line-height: 1.3;
        }

        .battle-side.left .battle-team { color: rgba(120,180,255,0.8); }
        .battle-side.right .battle-team { color: rgba(255,120,120,0.8); }

        .battle-vs {
            font-family: 'Anton', sans-serif;
            font-size: 42px;
            letter-spacing: 0.04em;
            text-align: center;
            padding: 0 12px;
            background: linear-gradient(180deg, #fff 0%, rgba(255,255,255,0.4) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            filter: drop-shadow(0 0 20px rgba(255,255,255,0.3));
        }

        .battle-footer {
            position: relative;
            z-index: 1;
            border-top: 1px solid rgba(255,255,255,0.07);
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .battle-match-info {
            font-family: 'DM Mono', monospace;
            font-size: 10px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .battle-kickoff {
            font-family: 'Anton', sans-serif;
            font-size: 18px;
            color: var(--green);
            letter-spacing: 0.04em;
        }

        .battle-close {
            position: absolute;
            top: 12px;
            right: 14px;
            z-index: 2;
            background: rgba(255,255,255,0.08);
            border: none;
            color: var(--muted);
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, color 0.2s;
        }

        .battle-close:hover { background: rgba(255,255,255,0.15); color: var(--text); }

        /* animations */
        @keyframes battleBackdropIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes battleSlideLeft {
            from { opacity: 0; transform: translateX(-40px) scale(0.85); }
            to   { opacity: 1; transform: translateX(0) scale(1); }
        }
        @keyframes battleSlideRight {
            from { opacity: 0; transform: translateX(40px) scale(0.85); }
            to   { opacity: 1; transform: translateX(0) scale(1); }
        }
        @keyframes battleVsPop {
            0%   { opacity: 0; transform: scale(0.3) rotate(-15deg); }
            65%  { transform: scale(1.15) rotate(4deg); }
            100% { opacity: 1; transform: scale(1) rotate(0deg); }
        }
        @keyframes battleModalIn {
            from { opacity: 0; transform: scale(0.88) translateY(16px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        .battle-backdrop { animation: battleBackdropIn 0.2s ease forwards; }
        .battle-modal    { animation: battleModalIn 0.3s cubic-bezier(0.34,1.56,0.64,1) forwards; }
        .battle-side.left  { animation: battleSlideLeft  0.35s cubic-bezier(0.34,1.56,0.64,1) 0.1s both; }
        .battle-side.right { animation: battleSlideRight 0.35s cubic-bezier(0.34,1.56,0.64,1) 0.1s both; }
        .battle-vs         { animation: battleVsPop      0.4s cubic-bezier(0.34,1.56,0.64,1) 0.2s both; }

        .match-card.upcoming-card { cursor: pointer; }

        /* ── LEADERBOARD ── */
        .lb-row {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 20px;
            display: grid;
            grid-template-columns: 32px 52px 1fr auto 56px;
            align-items: center;
            gap: 12px;
        }

        .lb-row.lb-first {
            border-color: rgba(245,197,24,0.3);
            background: linear-gradient(135deg, rgba(245,197,24,0.06), var(--surface));
        }

        .lb-chips {
            display: flex;
            gap: 4px;
            align-items: center;
        }

        .lb-chip {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            padding: 5px 6px;
            border-radius: 6px;
            background: var(--surface2);
            border: 1px solid var(--border);
            line-height: 1;
            gap: 2px;
        }

        .lb-chip-val {
            font-size: 14px;
            font-weight: 600;
            font-family: 'DM Mono', monospace;
        }

        .lb-chip-label {
            font-size: 8px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted2);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 640px) {
            .hero { padding: 40px 0 32px; }
            .hero-stats { gap: 20px; }
            .people-grid { grid-template-columns: 1fr; }
            .match-grid { grid-template-columns: 1fr; }
            .tabs { overflow-x: auto; }

            .lb-row {
                grid-template-columns: 28px 40px 1fr 56px;
                padding: 12px 14px;
                gap: 10px;
            }

            .lb-chips { display: none; }
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
        <p style="font-size:12px; color:var(--muted2); margin-top:8px; font-family:'DM Mono',monospace; letter-spacing:0.08em;">Made by Euan</p>
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
                <span class="hero-stat-val">{{ $totalMatches }}</span>
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

    {{-- ── UPCOMING + RESULTS TABS ── --}}
    @if($upcomingMatches->count() || $recentMatches->count())
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">Matches</h2>
            <div class="tabs" style="margin-bottom:0;">
                @if($upcomingMatches->count())
                <button
                    class="tab-btn"
                    :class="{ active: matchTab === 'upcoming' }"
                    @click="matchTab = 'upcoming'"
                >
                    Upcoming <span style="opacity:0.5; font-size:11px; margin-left:4px;">{{ $upcomingMatches->count() }}</span>
                </button>
                @endif
                @if($recentMatches->count())
                <button
                    class="tab-btn"
                    :class="{ active: matchTab === 'results' }"
                    @click="matchTab = 'results'"
                >
                    Results <span style="opacity:0.5; font-size:11px; margin-left:4px;">{{ $recentMatches->count() }}</span>
                </button>
                @endif
            </div>
        </div>

        @if($upcomingMatches->count())
        <div x-show="matchTab === 'upcoming'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="match-grid">
                @foreach($upcomingMatches as $match)
                @include('partials.match-card', ['match' => $match, 'sweepstakeTeams' => $sweepstakeTeams, 'isToday' => false])
                @endforeach
            </div>
        </div>
        @endif

        @if($recentMatches->count())
        <div x-show="matchTab === 'results'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="match-grid">
                @foreach($recentMatches as $match)
                @include('partials.match-card', ['match' => $match, 'sweepstakeTeams' => $sweepstakeTeams])
                @endforeach
            </div>
        </div>
        @endif
    </section>
    @endif

    {{-- ── LEADERBOARD ── --}}
    @if($leaderboard->where('played', '>', 0)->count())
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">Leaderboard</h2>
            <span class="section-badge">Points · GD · Goals</span>
        </div>

        <div style="display:flex; flex-direction:column; gap:8px;">
            @foreach($leaderboard as $i => $row)
            @php $person = $row['person']; @endphp
            <div class="lb-row {{ $i === 0 ? 'lb-first' : '' }}">
                {{-- Rank --}}
                <div style="font-family:'Anton',sans-serif; font-size:18px; color:{{ $i === 0 ? 'var(--gold)' : 'var(--muted2)' }}; text-align:center;">
                    {{ $i + 1 }}
                </div>

                {{-- Avatar --}}
                <img src="{{ $person->avatar_url }}" alt="{{ $person->name }}"
                    style="width:44px; height:44px; border-radius:50%; object-fit:cover; border:2px solid {{ $i === 0 ? 'var(--gold)' : 'var(--border2)' }};">

                {{-- Name + teams --}}
                <div style="min-width:0;">
                    <div style="font-family:'Anton',sans-serif; font-size:16px; letter-spacing:0.02em; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $person->name }}</div>
                    <div style="font-size:11px; color:var(--muted); margin-top:1px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $person->teams->pluck('name')->join(', ') }}
                    </div>
                </div>

                {{-- Stats chips --}}
                <div class="lb-chips">
                    <div class="lb-chip">
                        <span class="lb-chip-val">{{ $row['played'] }}</span>
                        <span class="lb-chip-label">P</span>
                    </div>
                    <div class="lb-chip">
                        <span class="lb-chip-val" style="color:var(--green);">{{ $row['wins'] }}</span>
                        <span class="lb-chip-label">W</span>
                    </div>
                    <div class="lb-chip">
                        <span class="lb-chip-val" style="color:var(--muted);">{{ $row['draws'] }}</span>
                        <span class="lb-chip-label">D</span>
                    </div>
                    <div class="lb-chip">
                        <span class="lb-chip-val" style="color:var(--red);">{{ $row['losses'] }}</span>
                        <span class="lb-chip-label">L</span>
                    </div>
                    <div class="lb-chip">
                        <span class="lb-chip-val" style="color:{{ $row['gd'] > 0 ? 'var(--green)' : ($row['gd'] < 0 ? 'var(--red)' : 'var(--muted)') }};">{{ $row['gd'] > 0 ? '+' : '' }}{{ $row['gd'] }}</span>
                        <span class="lb-chip-label">GD</span>
                    </div>
                </div>

                {{-- Points --}}
                <div style="text-align:center; background:{{ $i === 0 ? 'rgba(245,197,24,0.12)' : 'var(--surface2)' }}; border-radius:8px; padding:6px 4px; border:1px solid {{ $i === 0 ? 'rgba(245,197,24,0.25)' : 'var(--border)' }};">
                    <div style="font-family:'Anton',sans-serif; font-size:20px; color:{{ $i === 0 ? 'var(--gold)' : 'var(--text)' }}; line-height:1;">{{ $row['points'] }}</div>
                    <div style="font-size:9px; letter-spacing:0.1em; text-transform:uppercase; color:var(--muted);">Pts</div>
                </div>
            </div>
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
        battle: null,
        result: null,
        _confettiFrame: null,
        matchTab: '{{ $recentMatches->count() && !$upcomingMatches->count() ? "results" : "upcoming" }}',

        init() {
            this.animateCards();
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') { this.battle = null; this.closeResult(); }
            });
        },

        openBattle(data) {
            this.battle = data;
        },

        openResult(data) {
            console.log('openResult called', data);
            this.result = data;
            console.log('result set to', this.result);
            this.$nextTick(() => this.launchConfetti());
        },

        closeResult() {
            this.result = null;
            cancelAnimationFrame(this._confettiFrame);
            const c = document.getElementById('confetti-canvas');
            if (c) { const ctx = c.getContext('2d'); ctx.clearRect(0, 0, c.width, c.height); }
        },

        launchConfetti() {
            const canvas = document.getElementById('confetti-canvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            canvas.width  = window.innerWidth;
            canvas.height = window.innerHeight;

            const colours = ['#00e87a','#f5c518','#ffffff','#3c8cff','#ff4040','#c0ffb3'];
            const pieces  = Array.from({ length: 140 }, () => ({
                x:   Math.random() * canvas.width,
                y:   Math.random() * canvas.height - canvas.height,
                w:   6 + Math.random() * 8,
                h:   10 + Math.random() * 6,
                col: colours[Math.floor(Math.random() * colours.length)],
                rot: Math.random() * Math.PI * 2,
                vx:  (Math.random() - 0.5) * 2,
                vy:  2 + Math.random() * 4,
                vr:  (Math.random() - 0.5) * 0.18,
                alpha: 0.85 + Math.random() * 0.15,
            }));

            const draw = () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                let alive = false;
                pieces.forEach(p => {
                    p.x  += p.vx;
                    p.y  += p.vy;
                    p.rot += p.vr;
                    if (p.y < canvas.height + 20) alive = true;
                    ctx.save();
                    ctx.globalAlpha = p.alpha;
                    ctx.translate(p.x, p.y);
                    ctx.rotate(p.rot);
                    ctx.fillStyle = p.col;
                    ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                    ctx.restore();
                });
                if (alive) this._confettiFrame = requestAnimationFrame(draw);
            };
            this._confettiFrame = requestAnimationFrame(draw);
        },

        allEvents(stats) {
            if (!stats?.events?.length) return null;
            const relevant = stats.events
                .filter(e => ['goal','ownGoal','redCard','yellowCard'].includes(e.type))
                .sort((a, b) => (parseInt(a.minute) || 0) - (parseInt(b.minute) || 0))
                .map(e => ({
                    ...e,
                    icon: e.type === 'redCard' ? '🟥' : e.type === 'yellowCard' ? '🟨' : '⚽',
                    note: e.type === 'ownGoal' ? 'og' : e.penalty ? 'pen' : null,
                    minuteStr: e.minute ? String(e.minute).replace(/'$/, '') + "'" : '',
                }));
            return relevant.length ? relevant : null;
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

{{-- ── BATTLE MODAL ── --}}
<template x-teleport="body">
    <div x-show="battle" class="battle-backdrop" @click.self="battle = null" style="display:none;">
        <div class="battle-modal" x-show="battle" @click.stop>
            <button class="battle-close" @click="battle = null">✕</button>

            <div class="battle-bg"></div>

            <div class="battle-inner" x-show="battle">
                {{-- Left / Home --}}
                <div class="battle-side left">
                    <img class="battle-avatar" :src="battle?.homeAvatar" :alt="battle?.homePerson">
                    <img class="battle-flag" :src="battle?.homeFlag" :alt="battle?.homeTeam" onerror="this.style.display='none'">
                    <div class="battle-person" x-text="battle?.homePerson ?? '?'"></div>
                    <div class="battle-team" x-text="battle?.homeTeam"></div>
                </div>

                <div class="battle-vs">VS</div>

                {{-- Right / Away --}}
                <div class="battle-side right">
                    <img class="battle-avatar" :src="battle?.awayAvatar" :alt="battle?.awayPerson">
                    <img class="battle-flag" :src="battle?.awayFlag" :alt="battle?.awayTeam" onerror="this.style.display='none'">
                    <div class="battle-person" x-text="battle?.awayPerson ?? '?'"></div>
                    <div class="battle-team" x-text="battle?.awayTeam"></div>
                </div>
            </div>

            <div class="battle-footer">
                <span class="battle-match-info" x-text="battle?.stage ?? 'Group Stage'"></span>
                <span class="battle-kickoff" x-text="battle?.kickoff"></span>
                <span class="battle-match-info" x-text="battle?.venue"></span>
            </div>
        </div>
    </div>
</template>

{{-- ── RESULT MODAL ── --}}
<template x-teleport="body">
<div>
    <canvas id="confetti-canvas" x-show="result" style="display:none; position:fixed; inset:0; pointer-events:none; z-index:1003;"></canvas>

    <div x-show="result" class="battle-backdrop" @click.self="closeResult()" style="display:none; z-index:1001;">
        <div class="battle-modal" x-show="result" @click.stop style="max-width:400px; max-height:90vh; overflow-y:auto;">
            <button class="battle-close" @click="closeResult()">✕</button>

            {{-- Background tint based on draw/win --}}
            <div class="battle-bg" :style="result?.isDraw ? '' : 'background: linear-gradient(135deg, #060f08 0%, #0a0a0a 50%, #060f08 100%);'"></div>
            <div style="position:absolute; inset:0; background: radial-gradient(ellipse at 50% 0%, rgba(0,232,122,0.15) 0%, transparent 65%); z-index:0;" x-show="result && !result.isDraw"></div>

            <div style="position:relative; z-index:1; padding: 36px 28px 24px; text-align:center;">

                {{-- Draw state --}}
                <template x-if="result?.isDraw">
                    <div>
                        <div style="font-family:'DM Mono',monospace; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; color:var(--muted); margin-bottom:20px;">Full Time</div>
                        <div style="display:flex; align-items:center; justify-content:center; gap:20px; margin-bottom:20px;">
                            <img :src="result.homeAvatar" style="width:72px; height:72px; border-radius:50%; object-fit:cover; border:3px solid rgba(255,255,255,0.1);" onerror="this.style.display='none'">
                            <div style="font-family:'Anton',sans-serif; font-size:40px; color:var(--muted);">—</div>
                            <img :src="result.awayAvatar" style="width:72px; height:72px; border-radius:50%; object-fit:cover; border:3px solid rgba(255,255,255,0.1);" onerror="this.style.display='none'">
                        </div>
                        <div style="background:var(--surface2); border:1px solid var(--border); border-radius:10px; padding:14px 20px; margin-bottom:0;">
                            <div style="font-family:'Anton',sans-serif; font-size:38px; letter-spacing:0.04em; color:var(--text); margin-bottom:4px;" x-text="result.homeScore + ' – ' + result.awayScore"></div>
                            <div style="font-size:13px; color:var(--muted); margin-bottom:4px;" x-text="result.homeTeam + ' vs ' + result.awayTeam"></div>
                            <div style="font-family:'DM Mono',monospace; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:var(--muted2);">Draw</div>

                            <template x-if="allEvents(result.stats)">
                                <div style="margin-top:12px; padding-top:10px; border-top:1px solid rgba(255,255,255,0.06); display:flex; flex-direction:column; gap:2px;">
                                    <template x-for="e in allEvents(result.stats)" :key="(e.player||'') + (e.minute||'') + e.type">
                                        <div :style="e.side === 'home' ? 'display:flex; justify-content:flex-start;' : 'display:flex; justify-content:flex-end;'">
                                            <div style="display:inline-flex; align-items:center; gap:6px; padding:2px 0;">
                                                <span style="font-size:12px;" x-text="e.icon"></span>
                                                <span style="font-size:12px; color:var(--text); font-weight:500;" x-text="e.player ?? ''"></span>
                                                <span x-show="e.note" style="font-size:9px; color:var(--muted); font-family:'DM Mono',monospace;" x-text="e.note ? '(' + e.note + ')' : ''"></span>
                                                <span x-show="e.minuteStr" style="font-family:'DM Mono',monospace; font-size:9px; color:var(--muted2); background:rgba(255,255,255,0.07); border-radius:3px; padding:1px 5px;" x-text="e.minuteStr"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <template x-if="result.stats">
                            <div style="margin-top:16px; background:var(--surface2); border:1px solid var(--border); border-radius:10px; padding:14px 16px; text-align:left;">
                                <div style="font-family:'DM Mono',monospace; font-size:9px; letter-spacing:0.14em; text-transform:uppercase; color:var(--muted2); text-align:center; margin-bottom:14px;">Match Stats</div>

                                {{-- Possession --}}
                                <template x-if="result.stats.home.possession && result.stats.away.possession">
                                    <div style="margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid rgba(255,255,255,0.06);">
                                        <div style="display:grid; grid-template-columns:1fr auto 1fr; align-items:center; gap:8px; margin-bottom:6px;">
                                            <span style="font-family:'Anton',sans-serif; font-size:16px; color:rgba(120,180,255,0.9); text-align:right;" x-text="result.stats.home.possession + '%'"></span>
                                            <span style="font-size:9px; letter-spacing:0.1em; text-transform:uppercase; color:var(--muted); min-width:72px; text-align:center;">Possession</span>
                                            <span style="font-family:'Anton',sans-serif; font-size:16px; color:rgba(255,120,120,0.9);" x-text="result.stats.away.possession + '%'"></span>
                                        </div>
                                        <div style="height:5px; border-radius:3px; overflow:hidden; background:rgba(255,255,255,0.06); display:flex;">
                                            <div :style="'width:' + result.stats.home.possession + '%; background:rgba(60,140,255,0.65); border-radius:3px 0 0 3px; transition:width 0.6s ease;'"></div>
                                            <div style="flex:1; background:rgba(255,60,60,0.65); border-radius:0 3px 3px 0;"></div>
                                        </div>
                                    </div>
                                </template>

                                {{-- Other stat rows with proportion bar --}}
                                <template x-for="row in [
                                    { label: 'Shots', home: result.stats.home.shots, away: result.stats.away.shots },
                                    { label: 'On Target', home: result.stats.home.shotsOnTarget, away: result.stats.away.shotsOnTarget },
                                    { label: 'Corners', home: result.stats.home.corners, away: result.stats.away.corners },
                                    { label: 'Fouls', home: result.stats.home.fouls, away: result.stats.away.fouls },
                                    { label: 'Yellows', home: result.stats.home.yellowCards, away: result.stats.away.yellowCards },
                                    { label: 'Saves', home: result.stats.home.saves, away: result.stats.away.saves },
                                ].filter(r => r.home != null || r.away != null)" :key="row.label">
                                    <div style="padding:7px 0;">
                                        <div style="display:grid; grid-template-columns:1fr auto 1fr; align-items:center; gap:8px; margin-bottom:5px;">
                                            <span style="font-family:'Anton',sans-serif; font-size:15px; color:rgba(120,180,255,0.9); text-align:right;" x-text="row.home ?? '–'"></span>
                                            <span style="font-size:9px; letter-spacing:0.1em; text-transform:uppercase; color:var(--muted); min-width:72px; text-align:center;" x-text="row.label"></span>
                                            <span style="font-family:'Anton',sans-serif; font-size:15px; color:rgba(255,120,120,0.9);" x-text="row.away ?? '–'"></span>
                                        </div>
                                        <div style="height:3px; border-radius:2px; overflow:hidden; background:rgba(255,255,255,0.06); display:flex;" x-show="(row.home ?? 0) + (row.away ?? 0) > 0">
                                            <div :style="'width:' + ((row.home ?? 0) / ((row.home ?? 0) + (row.away ?? 0)) * 100) + '%; background:rgba(60,140,255,0.55); border-radius:2px 0 0 2px;'"></div>
                                            <div style="flex:1; background:rgba(255,60,60,0.55); border-radius:0 2px 2px 0;"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Winner state --}}
                <template x-if="result && !result.isDraw">
                    <div>
                        <div style="font-family:'DM Mono',monospace; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; color:var(--green); margin-bottom:20px;">Full Time</div>

                        <div style="position:relative; display:inline-block; margin-bottom:16px;">
                            <img :src="result.winnerAvatar"
                                style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:4px solid var(--green); box-shadow: 0 0 32px rgba(0,232,122,0.4), 0 0 0 8px rgba(0,232,122,0.08); display:block;"
                                onerror="this.style.display='none'">
                            <img x-show="result.winnerFlag" :src="result.winnerFlag"
                                style="position:absolute; bottom:-4px; right:-4px; width:32px; height:22px; object-fit:cover; border-radius:4px; border:2px solid var(--bg); box-shadow:0 2px 8px rgba(0,0,0,0.5);"
                                onerror="this.style.display='none'">
                        </div>

                        <div style="font-family:'DM Mono',monospace; font-size:10px; letter-spacing:0.14em; text-transform:uppercase; color:var(--green); opacity:0.7; margin-bottom:4px;">Winner</div>
                        <div style="font-family:'Anton',sans-serif; font-size:26px; letter-spacing:0.04em; text-transform:uppercase; color:var(--green); margin-bottom:2px;" x-text="result.winnerPerson"></div>
                        <div style="font-size:13px; color:var(--muted); margin-bottom:20px;" x-text="result.winnerTeam"></div>

                        <div style="background:var(--surface2); border:1px solid var(--border); border-radius:10px; padding:14px 20px;">
                            <div style="font-family:'Anton',sans-serif; font-size:34px; letter-spacing:0.06em; color:var(--text);" x-text="result.homeScore + ' – ' + result.awayScore"></div>
                            <div style="font-size:12px; color:var(--muted); margin-top:4px;" x-text="result.homeTeam + ' vs ' + result.awayTeam"></div>

                            <template x-if="allEvents(result.stats)">
                                <div style="margin-top:12px; padding-top:10px; border-top:1px solid rgba(255,255,255,0.06); display:flex; flex-direction:column; gap:2px;">
                                    <template x-for="e in allEvents(result.stats)" :key="(e.player||'') + (e.minute||'') + e.type">
                                        <div :style="e.side === 'home' ? 'display:flex; justify-content:flex-start;' : 'display:flex; justify-content:flex-end;'">
                                            <div style="display:inline-flex; align-items:center; gap:6px; padding:2px 0;">
                                                <span style="font-size:12px;" x-text="e.icon"></span>
                                                <span style="font-size:12px; color:var(--text); font-weight:500;" x-text="e.player ?? ''"></span>
                                                <span x-show="e.note" style="font-size:9px; color:var(--muted); font-family:'DM Mono',monospace;" x-text="e.note ? '(' + e.note + ')' : ''"></span>
                                                <span x-show="e.minuteStr" style="font-family:'DM Mono',monospace; font-size:9px; color:var(--muted2); background:rgba(255,255,255,0.07); border-radius:3px; padding:1px 5px;" x-text="e.minuteStr"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <template x-if="result.stats">
                            <div style="margin-top:16px; background:var(--surface2); border:1px solid var(--border); border-radius:10px; padding:14px 16px; text-align:left;">
                                <div style="font-family:'DM Mono',monospace; font-size:9px; letter-spacing:0.14em; text-transform:uppercase; color:var(--muted2); text-align:center; margin-bottom:12px;">Match Stats</div>

                                {{-- Possession bar --}}
                                <template x-if="result.stats.home.possession && result.stats.away.possession">
                                    <div style="margin-bottom:12px;">
                                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">
                                            <span style="font-family:'Anton',sans-serif; font-size:14px;" x-text="result.stats.home.possession + '%'"></span>
                                            <span style="font-size:9px; letter-spacing:0.1em; text-transform:uppercase; color:var(--muted);">Possession</span>
                                            <span style="font-family:'Anton',sans-serif; font-size:14px;" x-text="result.stats.away.possession + '%'"></span>
                                        </div>
                                        <div style="height:6px; border-radius:3px; overflow:hidden; background:rgba(255,255,255,0.08); display:flex;">
                                            <div :style="'width:' + result.stats.home.possession + '%; background:rgba(60,140,255,0.7); border-radius:3px 0 0 3px;'"></div>
                                            <div :style="'flex:1; background:rgba(255,60,60,0.7); border-radius:0 3px 3px 0;'"></div>
                                        </div>
                                    </div>
                                </template>

                                {{-- Stat rows --}}
                                <template x-for="row in [
                                    { label: 'Shots', home: result.stats.home.shots, away: result.stats.away.shots },
                                    { label: 'On Target', home: result.stats.home.shotsOnTarget, away: result.stats.away.shotsOnTarget },
                                    { label: 'Corners', home: result.stats.home.corners, away: result.stats.away.corners },
                                    { label: 'Fouls', home: result.stats.home.fouls, away: result.stats.away.fouls },
                                    { label: 'Yellows', home: result.stats.home.yellowCards, away: result.stats.away.yellowCards },
                                    { label: 'Saves', home: result.stats.home.saves, away: result.stats.away.saves },
                                ].filter(r => r.home != null || r.away != null)" :key="row.label">
                                    <div style="display:grid; grid-template-columns:1fr auto 1fr; gap:6px; align-items:center; padding:4px 0; border-top:1px solid rgba(255,255,255,0.04);">
                                        <span style="font-family:'Anton',sans-serif; font-size:14px; color:var(--text);" x-text="row.home ?? '–'"></span>
                                        <span style="font-size:9px; letter-spacing:0.1em; text-transform:uppercase; color:var(--muted); text-align:center; min-width:64px;" x-text="row.label"></span>
                                        <span style="font-family:'Anton',sans-serif; font-size:14px; color:var(--text); text-align:right;" x-text="row.away ?? '–'"></span>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <div style="margin-top:12px; font-size:11px; font-family:'DM Mono',monospace; letter-spacing:0.08em; text-transform:uppercase; color:var(--muted2);" x-text="result.stage"></div>
                    </div>
                </template>

            </div>
        </div>
    </div>
</div>
</template>

</body>
</html>
