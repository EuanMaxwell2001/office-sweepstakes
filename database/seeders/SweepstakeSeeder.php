<?php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\Team;
use Illuminate\Database\Seeder;

class SweepstakeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Euan'   => ['Tunisia', 'Colombia', 'Cabo Verde', 'Jordan', 'Spain'],
            'Ruben'  => ['Egypt', 'Ghana', 'Saudi Arabia', 'Senegal', 'Uzbekistan'],
            'Zara'   => ['Bosnia & Herzegovina', 'Germany', 'New Zealand', 'Belgium', 'Uruguay'],
            'Steven' => ['Iraq', 'Panama', 'Congo', 'Sweden', 'Iran'],
            'Dave'   => ['Algeria', 'Curaçao', 'Switzerland', 'Czechia', 'Turkey'],
            'Dino'   => ['Canada', 'Austria', 'South Africa', 'Ecuador', 'England'],
            'Colin'  => ['Qatar', 'Brazil', 'Scotland', 'South Korea', 'Netherlands'],
            'Hilary' => ['France', 'USA', 'Mexico', 'Croatia', 'Côte d\'Ivoire'],
            'Lauren' => ['Morocco', 'Haiti', 'Japan', 'Australia', 'Norway'],
            'Office' => ['Paraguay', 'Argentina', 'Portugal'],
        ];

        $countryCodes = [
            'Tunisia' => 'tn', 'Colombia' => 'co', 'Cabo Verde' => 'cv', 'Jordan' => 'jo', 'Spain' => 'es',
            'Egypt' => 'eg', 'Ghana' => 'gh', 'Saudi Arabia' => 'sa', 'Senegal' => 'sn', 'Uzbekistan' => 'uz',
            'Bosnia & Herzegovina' => 'ba', 'Germany' => 'de', 'New Zealand' => 'nz', 'Belgium' => 'be', 'Uruguay' => 'uy',
            'Iraq' => 'iq', 'Panama' => 'pa', 'Congo' => 'cd', 'Sweden' => 'se', 'Iran' => 'ir',
            'Algeria' => 'dz', 'Curaçao' => 'cw', 'Switzerland' => 'ch', 'Czechia' => 'cz', 'Turkey' => 'tr',
            'Canada' => 'ca', 'Austria' => 'at', 'South Africa' => 'za', 'Ecuador' => 'ec', 'England' => 'gb-eng',
            'Qatar' => 'qa', 'Brazil' => 'br', 'Scotland' => 'gb-sct', 'South Korea' => 'kr', 'Netherlands' => 'nl',
            'France' => 'fr', 'USA' => 'us', 'Mexico' => 'mx', 'Croatia' => 'hr', 'Côte d\'Ivoire' => 'ci',
            'Morocco' => 'ma', 'Haiti' => 'ht', 'Japan' => 'jp', 'Australia' => 'au', 'Norway' => 'no',
            'Paraguay' => 'py', 'Argentina' => 'ar', 'Portugal' => 'pt',
        ];

        foreach ($data as $personName => $teams) {
            $person = Person::firstOrCreate(
                ['name' => $personName],
                ['is_office' => $personName === 'Office']
            );

            foreach ($teams as $teamName) {
                Team::firstOrCreate(
                    ['person_id' => $person->id, 'name' => $teamName],
                    ['country_code' => $countryCodes[$teamName] ?? null]
                );
            }
        }
    }
}
