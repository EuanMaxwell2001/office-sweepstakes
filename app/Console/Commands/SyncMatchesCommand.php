<?php

namespace App\Console\Commands;

use App\Models\FootballMatch;
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
                str_contains($statusType, 'IN_PROGRESS') => 'live',
                str_contains($statusType, 'FINAL')       => 'finished',
                default                                  => 'scheduled',
            };

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
                ]
            );

            $synced++;
        }

        $this->info("Done — synced {$synced} matches.");
        return self::SUCCESS;
    }

    private function flagUrl(string $teamName): string
    {
        $code = self::COUNTRY_CODES[strtolower(trim($teamName))] ?? null;
        return $code ? "https://flagcdn.com/{$code}.svg" : '';
    }
}
