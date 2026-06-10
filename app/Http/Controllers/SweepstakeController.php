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

        // Build a lookup: lowercase team name → [person, flag_url]
        $sweepstakeTeams = $people->flatMap(function ($person) {
            return $person->teams->map(fn ($team) => [
                'key'      => strtolower($team->name),
                'person'   => $person->name,
                'flag_url' => $team->flag_url,
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
            ->limit(8)
            ->get();

        $upcomingMatches = FootballMatch::upcoming()
            ->whereDate('match_date', '>', today())
            ->orderBy('match_date')
            ->limit(48)
            ->get();

        return view('sweepstake', compact('people', 'liveMatches', 'todayMatches', 'recentMatches', 'upcomingMatches', 'sweepstakeTeams'));
    }
}
