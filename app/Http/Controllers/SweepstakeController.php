<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use App\Models\Person;

class SweepstakeController extends Controller
{
    public function index()
    {
        $people = Person::with('teams')
            ->orderBy('is_office')
            ->orderBy('name')
            ->get();

        // Known ESPN name variants → our DB name (all lowercase)
        $aliases = [
            'bosnia-herzegovina'     => 'bosnia & herzegovina',
            'bosnia and herzegovina' => 'bosnia & herzegovina',
            'ivory coast'            => "côte d'ivoire",
            "cote d'ivoire"          => "côte d'ivoire",
            'cape verde'             => 'cabo verde',
            'turkiye'                => 'turkey',
            'türkiye'                => 'turkey',
            'dr congo'               => 'congo',
            'congo dr'               => 'congo',
            'korea republic'         => 'south korea',
            'usa'                    => 'usa',
            'united states'          => 'usa',
            'the netherlands'        => 'netherlands',
            'curacao'                => 'curaçao',
            'colombia'               => 'colombia',
        ];

        // Build a lookup: lowercase team name → [person, flag_url, avatar_url]
        $sweepstakeTeams = $people->flatMap(function ($person) {
            return $person->teams->map(fn ($team) => [
                'key'        => strtolower($team->name),
                'person'     => $person->name,
                'flag_url'   => $team->flag_url,
                'avatar_url' => $person->avatar_url,
            ]);
        })->keyBy('key');

        // Add alias entries pointing to the same data
        foreach ($aliases as $alias => $canonical) {
            if ($sweepstakeTeams->has($canonical) && ! $sweepstakeTeams->has($alias)) {
                $sweepstakeTeams->put($alias, $sweepstakeTeams->get($canonical));
            }
        }

        $liveMatches = FootballMatch::live()->orderBy('match_date')->get();

        $todayMatches = FootballMatch::query()
            ->whereDate('match_date', today())
            ->where('status', '!=', 'live')
            ->orderBy('match_date')
            ->get();

        $recentMatches = FootballMatch::finished()
            ->whereDate('match_date', '<', today())
            ->orderBy('match_date', 'desc')
            ->get();

        $knownTeams = $sweepstakeTeams->keys()->all();

        $upcomingMatches = FootballMatch::upcoming()
            ->whereDate('match_date', '>', today())
            ->whereIn(\DB::raw('LOWER(home_team)'), $knownTeams)
            ->whereIn(\DB::raw('LOWER(away_team)'), $knownTeams)
            ->orderBy('match_date')
            ->get();

        $totalMatches = $liveMatches->count() + $todayMatches->count() + $recentMatches->count() + $upcomingMatches->count();

        // Build leaderboard from all finished matches
        $allFinished = FootballMatch::finished()->get();

        // Reverse alias map: canonical → [aliases...]
        $canonicalLookup = collect($aliases); // alias → canonical

        $leaderboard = $people->map(function ($person) use ($allFinished, $canonicalLookup) {
            $teamNames = $person->teams->map(fn($t) => strtolower($t->name))->all();

            // Expand with aliases so ESPN name variants match
            $allNames = collect($teamNames)->flatMap(function ($name) use ($canonicalLookup) {
                $extras = $canonicalLookup->filter(fn($canonical) => $canonical === $name)->keys();
                return collect([$name])->merge($extras);
            })->unique()->all();

            $wins = 0; $draws = 0; $losses = 0; $goalsFor = 0; $goalsAgainst = 0;

            foreach ($allFinished as $match) {
                $home = strtolower($match->home_team);
                $away = strtolower($match->away_team);
                $isHome = in_array($home, $allNames);
                $isAway = in_array($away, $allNames);

                if (! $isHome && ! $isAway) continue;

                if ($isHome) {
                    $goalsFor     += $match->home_score;
                    $goalsAgainst += $match->away_score;
                    if ($match->home_score > $match->away_score)      $wins++;
                    elseif ($match->home_score === $match->away_score) $draws++;
                    else                                               $losses++;
                }
                if ($isAway) {
                    $goalsFor     += $match->away_score;
                    $goalsAgainst += $match->home_score;
                    if ($match->away_score > $match->home_score)      $wins++;
                    elseif ($match->away_score === $match->home_score) $draws++;
                    else                                               $losses++;
                }
            }

            $played = $wins + $draws + $losses;

            return [
                'person'       => $person,
                'wins'         => $wins,
                'draws'        => $draws,
                'losses'       => $losses,
                'played'       => $played,
                'goalsFor'     => $goalsFor,
                'goalsAgainst' => $goalsAgainst,
                'gd'           => $goalsFor - $goalsAgainst,
                'points'       => ($wins * 3) + $draws,
            ];
        })
        ->filter(fn($row) => ! $row['person']->is_office)
        ->sortBy([
            fn($a, $b) => $b['points'] <=> $a['points'],
            fn($a, $b) => $b['gd'] <=> $a['gd'],
            fn($a, $b) => $b['goalsFor'] <=> $a['goalsFor'],
        ])
        ->values();

        return view('sweepstake', compact('people', 'liveMatches', 'todayMatches', 'recentMatches', 'upcomingMatches', 'sweepstakeTeams', 'totalMatches', 'leaderboard'));
    }
}
