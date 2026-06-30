<?php

namespace App\Console\Commands;

use App\Models\FootballMatch;
use App\Models\Team;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

#[Signature('sweepstake:sync-matches')]
#[Description('Sync World Cup 2026 match data from ESPN API')]
class SyncMatchesCommand extends Command
{
    private const WC_START = '2026-06-11';
    private const WC_END   = '2026-07-19';

    // ESPN display name → ISO 3166-1 alpha-2 (or subdivision) code
    private const COUNTRY_CODES = [
        'argentina'                => 'ar',
        'australia'                => 'au',
        'austria'                  => 'at',
        'belgium'                  => 'be',
        'bosnia and herzegovina'   => 'ba',
        'bosnia-herzegovina'       => 'ba',
        'bosnia & herzegovina'     => 'ba',
        'brazil'                   => 'br',
        'cabo verde'               => 'cv',
        'cape verde'               => 'cv',
        'canada'                   => 'ca',
        'colombia'                 => 'co',
        'congo'                    => 'cg',
        'congo dr'                 => 'cd',
        'dr congo'                 => 'cd',
        'croatia'                  => 'hr',
        'curaçao'                  => 'cw',
        'curacao'                  => 'cw',
        'czechia'                  => 'cz',
        'czech republic'           => 'cz',
        'ecuador'                  => 'ec',
        'egypt'                    => 'eg',
        'england'                  => 'gb-eng',
        'france'                   => 'fr',
        'germany'                  => 'de',
        'ghana'                    => 'gh',
        'haiti'                    => 'ht',
        'iran'                     => 'ir',
        'iraq'                     => 'iq',
        'ivory coast'              => 'ci',
        "côte d'ivoire"            => 'ci',
        "cote d'ivoire"            => 'ci',
        'japan'                    => 'jp',
        'jordan'                   => 'jo',
        'mexico'                   => 'mx',
        'morocco'                  => 'ma',
        'netherlands'              => 'nl',
        'the netherlands'          => 'nl',
        'new zealand'              => 'nz',
        'norway'                   => 'no',
        'panama'                   => 'pa',
        'paraguay'                 => 'py',
        'portugal'                 => 'pt',
        'qatar'                    => 'qa',
        'saudi arabia'             => 'sa',
        'scotland'                 => 'gb-sct',
        'senegal'                  => 'sn',
        'south africa'             => 'za',
        'south korea'              => 'kr',
        'korea republic'           => 'kr',
        'spain'                    => 'es',
        'sweden'                   => 'se',
        'switzerland'              => 'ch',
        'tunisia'                  => 'tn',
        'turkey'                   => 'tr',
        'türkiye'                  => 'tr',
        'turkiye'                  => 'tr',
        'uruguay'                  => 'uy',
        'usa'                      => 'us',
        'united states'            => 'us',
        'uzbekistan'               => 'uz',
        'algeria'                  => 'dz',
        'venezuela'                => 've',
        'jamaica'                  => 'jm',
        'cuba'                     => 'cu',
        'costa rica'               => 'cr',
        'honduras'                 => 'hn',
        'el salvador'              => 'sv',
        'nigeria'                  => 'ng',
        'cameroon'                 => 'cm',
        'mali'                     => 'ml',
        'tanzania'                 => 'tz',
        'kenya'                    => 'ke',
        'comoros'                  => 'km',
        'indonesia'                => 'id',
        'thailand'                 => 'th',
        'china'                    => 'cn',
        'india'                    => 'in',
        'bahrain'                  => 'bh',
        'oman'                     => 'om',
        'ukraine'                  => 'ua',
        'serbia'                   => 'rs',
        'slovakia'                 => 'sk',
        'hungary'                  => 'hu',
        'romania'                  => 'ro',
        'albania'                  => 'al',
        'wales'                    => 'gb-wls',
    ];

    public function handle(): int
    {
        $this->info('Syncing World Cup 2026 matches from ESPN...');

        $events = [];

        $dateRange = Carbon::parse(self::WC_START)->format('Ymd')
            . '-'
            . Carbon::parse(self::WC_END)->format('Ymd');

        $urls = [
            'https://site.api.espn.com/apis/site/v2/sports/soccer/fifa.world/scoreboard',
            'https://site.api.espn.com/apis/site/v2/sports/soccer/fifa.world/schedule',
        ];

        foreach ($urls as $url) {
            try {
                $response = Http::timeout(15)->withoutVerifying()->get($url, [
                    'limit' => 200,
                    'dates' => $dateRange,
                ]);

                if (! $response->ok()) continue;

                $fetched = $response->json('events')
                    ?? $response->json('eventsPage.events')
                    ?? [];

                foreach ($fetched as $event) {
                    $events[$event['id']] = $event;
                }

                $this->line("  → " . count($fetched) . " events from " . basename($url));

            } catch (\Exception $e) {
                $this->warn("  ✗ " . $e->getMessage());
            }
        }

        if (empty($events)) {
            $this->error('No events returned.');
            return self::FAILURE;
        }

        $synced = 0;

        foreach ($events as $event) {
            $comp = $event['competitions'][0] ?? null;
            if (! $comp) continue;

            $competitors = collect($comp['competitors'] ?? []);
            $home = $competitors->firstWhere('homeAway', 'home');
            $away = $competitors->firstWhere('homeAway', 'away');

            if (! $home || ! $away) continue;

            $homeName = $home['team']['displayName'] ?? '';
            $awayName = $away['team']['displayName'] ?? '';

            $statusType = $event['status']['type']['name'] ?? 'STATUS_SCHEDULED';
            $status = match (true) {
                in_array($statusType, [
                    'STATUS_IN_PROGRESS',
                    'STATUS_FIRST_HALF',
                    'STATUS_SECOND_HALF',
                    'STATUS_HALFTIME',
                    'STATUS_EXTRA_TIME',
                    'STATUS_PENALTY',
                ]) => 'live',
                in_array($statusType, [
                    'STATUS_FINAL',
                    'STATUS_FULL_TIME',
                    'STATUS_FT',
                    'STATUS_FINAL_AET',
                    'STATUS_FINAL_PEN',
                    'STATUS_FINAL_ET',
                ]) => 'finished',
                default => 'scheduled',
            };

            $stats = $status === 'finished' ? $this->extractStats($home, $away, $comp) : null;

            // Store winner info so elimination logic can handle draws on 90-min score (pens/AET)
            if ($status === 'finished') {
                $stats ??= [];
                $stats['home_winner'] = isset($home['winner']) ? (bool) $home['winner'] : null;
                $stats['away_winner'] = isset($away['winner']) ? (bool) $away['winner'] : null;
            }

            FootballMatch::updateOrCreate(
                ['espn_id' => (string) $event['id']],
                [
                    'home_team'      => $homeName,
                    'away_team'      => $awayName,
                    'home_team_abbr' => $home['team']['abbreviation'] ?? null,
                    'away_team_abbr' => $away['team']['abbreviation'] ?? null,
                    'home_flag'      => $home['team']['flag']['href'] ?? $this->flagUrl($homeName),
                    'away_flag'      => $away['team']['flag']['href'] ?? $this->flagUrl($awayName),
                    'home_score'     => $status !== 'scheduled' ? (int) ($home['score'] ?? 0) : null,
                    'away_score'     => $status !== 'scheduled' ? (int) ($away['score'] ?? 0) : null,
                    'status'         => $status,
                    'status_detail'  => $event['status']['type']['shortDetail'] ?? null,
                    'match_date'     => $event['date'] ?? null,
                    'stage'          => $event['season']['slug'] ?? null,
                    'group_name'     => $comp['series']['summary'] ?? null,
                    'venue'          => $comp['venue']['fullName'] ?? null,
                    'stats'          => $stats,
                ]
            );

            $synced++;
        }

        $this->info("Done — synced {$synced} matches.");

        $this->syncEliminations();

        return self::SUCCESS;
    }

    private function syncEliminations(): void
    {
        try {
            $response = Http::timeout(15)->withoutVerifying()->get(
                'https://site.api.espn.com/apis/v2/sports/soccer/fifa.world/standings'
            );

            if (! $response->ok()) {
                $this->warn('  ✗ Could not fetch standings');
                return;
            }

            $eliminated   = [];
            $best8advance = [];
            $stillIn      = [];

            foreach ($response->json('children') ?? [] as $group) {
                foreach ($group['standings']['entries'] ?? [] as $entry) {
                    $name = strtolower($entry['team']['displayName'] ?? '');
                    $note = strtolower($entry['note']['description'] ?? '');
                    if (! $name) continue;

                    if (str_contains($note, 'eliminat')) {
                        $eliminated[] = $name;
                    } elseif (str_contains($note, 'best 8') || str_contains($note, 'best eight')) {
                        $best8advance[] = $name;
                    } else {
                        $stillIn[] = $name;
                    }
                }
            }

            // For "Best 8 advance" teams, check if they actually appear in any
            // round-of-32 match in our DB — if not, they didn't make the cut.
            if (! empty($best8advance)) {
                $r32Teams = FootballMatch::where('stage', 'round-of-32')
                    ->get()
                    ->flatMap(fn($m) => [strtolower($m->home_team), strtolower($m->away_team)])
                    ->unique()
                    ->values()
                    ->all();

                foreach ($best8advance as $name) {
                    if (in_array($name, $r32Teams)) {
                        $stillIn[] = $name;
                    } else {
                        $eliminated[] = $name;
                    }
                }
            }

            // Apply aliases so our DB team names match ESPN names
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
                'united states'          => 'usa',
                'the netherlands'        => 'netherlands',
                'curacao'                => 'curaçao',
            ];

            $resolveAlias = function (string $name) use ($aliases): string {
                return $aliases[$name] ?? $name;
            };

            $eliminated = array_map($resolveAlias, $eliminated);
            $stillIn    = array_map($resolveAlias, $stillIn);

            // Knockout rounds: mark the loser of each finished knockout match as eliminated.
            // Needed for pens/AET where 90-min scores are level.
            $knockoutStages = [
                'round-of-32', 'round-of-16',
                'quarterfinal', 'quarterfinals',
                'semifinal', 'semifinals',
                'semi-final', 'semi-finals',
                'final',
            ];

            $knockoutMatches = FootballMatch::whereIn('stage', $knockoutStages)
                ->where('status', 'finished')
                ->get();

            foreach ($knockoutMatches as $match) {
                $s = $match->stats ?? [];
                $homeWinner = $s['home_winner'] ?? null;

                if ($homeWinner === null) {
                    // Fall back to score for regular-time finishes
                    if ($match->home_score !== null && $match->home_score !== $match->away_score) {
                        $homeWinner = $match->home_score > $match->away_score;
                    } else {
                        continue; // Can't determine winner
                    }
                }

                $loser  = $resolveAlias(strtolower($homeWinner ? $match->away_team : $match->home_team));
                $winner = $resolveAlias(strtolower($homeWinner ? $match->home_team : $match->away_team));

                if (! in_array($loser, $eliminated)) {
                    $eliminated[] = $loser;
                }
                // Remove loser from stillIn so the reinstated query doesn't undo elimination
                $stillIn = array_values(array_filter($stillIn, fn ($n) => $n !== $loser));

                // Winner is definitely still in (may override a stale standings note)
                if (! in_array($winner, $stillIn)) {
                    $stillIn[] = $winner;
                }
                // Remove winner from eliminated in case standings were stale
                $eliminated = array_values(array_filter($eliminated, fn ($n) => $n !== $winner));
            }

            $updatedOut = Team::whereIn(\DB::raw('LOWER(name)'), $eliminated)
                ->where(fn ($q) => $q->where('is_eliminated', false)->orWhereNull('is_eliminated'))
                ->update(['is_eliminated' => true]);

            $updatedIn = Team::whereIn(\DB::raw('LOWER(name)'), $stillIn)
                ->where('is_eliminated', true)
                ->update(['is_eliminated' => false]);

            $this->line("  → Eliminations: {$updatedOut} newly out, {$updatedIn} reinstated");

        } catch (\Exception $e) {
            $this->warn('  ✗ Elimination sync failed: ' . $e->getMessage());
        }
    }

    private function extractStats(array $home, array $away, array $comp): ?array
    {
        $parse = function (array $competitor): array {
            $map = [];
            foreach ($competitor['statistics'] ?? [] as $stat) {
                $map[$stat['name']] = $stat['displayValue'] ?? $stat['value'] ?? null;
            }
            return $map;
        };

        $h = $parse($home);
        $a = $parse($away);

        $homeId = $home['team']['id'] ?? null;

        // Parse match events (goals, cards) from competition details
        $events = [];
        foreach ($comp['details'] ?? [] as $detail) {
            $typeText = strtolower($detail['type']['text'] ?? '');
            $teamId   = $detail['team']['id'] ?? null;
            $side     = $teamId === $homeId ? 'home' : 'away';
            $minute   = $detail['clock']['displayValue'] ?? null;
            $players  = array_map(
                fn($a) => $a['displayName'] ?? '',
                $detail['athletesInvolved'] ?? []
            );
            $player = $players[0] ?? null;

            if (str_contains($typeText, 'goal') || str_contains($typeText, 'penalty')) {
                $isOwn     = str_contains($typeText, 'own');
                $isPenalty = str_contains($typeText, 'penalty');
                $events[]  = [
                    'type'    => $isOwn ? 'ownGoal' : 'goal',
                    'side'    => $isOwn ? ($side === 'home' ? 'away' : 'home') : $side,
                    'player'  => $player,
                    'minute'  => $minute,
                    'penalty' => $isPenalty && ! $isOwn,
                ];
            } elseif (str_contains($typeText, 'red card') || $typeText === 'red') {
                $events[] = ['type' => 'redCard', 'side' => $side, 'player' => $player, 'minute' => $minute];
            } elseif (str_contains($typeText, 'yellow card') || $typeText === 'yellow') {
                $events[] = ['type' => 'yellowCard', 'side' => $side, 'player' => $player, 'minute' => $minute];
            }
        }

        if (empty($h) && empty($a) && empty($events)) return null;

        $pick = fn(array $m, string $key): ?string => $m[$key] ?? null;

        return [
            'home' => [
                'possession'    => $pick($h, 'possessionPct'),
                'shots'         => $pick($h, 'totalShots'),
                'shotsOnTarget' => $pick($h, 'shotsOnTarget'),
                'corners'       => $pick($h, 'cornerKicks'),
                'fouls'         => $pick($h, 'fouls'),
                'yellowCards'   => $pick($h, 'yellowCards'),
                'redCards'      => $pick($h, 'redCards'),
                'offsides'      => $pick($h, 'offsides'),
                'saves'         => $pick($h, 'saves'),
            ],
            'away' => [
                'possession'    => $pick($a, 'possessionPct'),
                'shots'         => $pick($a, 'totalShots'),
                'shotsOnTarget' => $pick($a, 'shotsOnTarget'),
                'corners'       => $pick($a, 'cornerKicks'),
                'fouls'         => $pick($a, 'fouls'),
                'yellowCards'   => $pick($a, 'yellowCards'),
                'redCards'      => $pick($a, 'redCards'),
                'offsides'      => $pick($a, 'offsides'),
                'saves'         => $pick($a, 'saves'),
            ],
            'events' => $events,
        ];
    }

    private function flagUrl(string $teamName): string
    {
        $code = self::COUNTRY_CODES[strtolower(trim($teamName))] ?? null;
        return $code ? "https://flagcdn.com/{$code}.svg" : '';
    }
}
