<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FootballMatchResource\Pages;
use App\Models\FootballMatch;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;

class FootballMatchResource extends Resource
{
    protected static ?string $model = FootballMatch::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Matches';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('home_team')->required(),
                TextInput::make('away_team')->required(),
                TextInput::make('home_score')->numeric()->nullable(),
                TextInput::make('away_score')->numeric()->nullable(),
                Select::make('status')
                    ->options(['scheduled' => 'Scheduled', 'live' => 'Live', 'finished' => 'Finished'])
                    ->required(),
                TextInput::make('stage'),
                TextInput::make('group_name')->label('Group'),
                TextInput::make('venue'),
                DateTimePicker::make('match_date'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('match_date', 'desc')
            ->columns([
                TextColumn::make('match_date')
                    ->label('Date')
                    ->dateTime('D d M, H:i')
                    ->sortable(),

                TextColumn::make('home_team')
                    ->label('Home')
                    ->weight('bold'),

                TextColumn::make('score')
                    ->label('Score')
                    ->getStateUsing(fn (FootballMatch $r) => $r->home_score !== null
                        ? "{$r->home_score} – {$r->away_score}"
                        : 'vs')
                    ->alignCenter(),

                TextColumn::make('away_team')
                    ->label('Away')
                    ->weight('bold'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'live'      => 'danger',
                        'finished'  => 'success',
                        default     => 'gray',
                    }),

                TextColumn::make('stage')->toggleable(),
                TextColumn::make('group_name')->label('Group')->toggleable(),
                TextColumn::make('venue')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(['scheduled' => 'Scheduled', 'live' => 'Live', 'finished' => 'Finished']),
                SelectFilter::make('stage'),
            ])
            ->headerActions([
                Action::make('sync')
                    ->label('Sync from ESPN')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function () {
                        Artisan::call('sweepstake:sync-matches');
                    })
                    ->successNotificationTitle('Matches synced!'),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFootballMatches::route('/'),
            'edit'   => Pages\EditFootballMatch::route('/{record}/edit'),
        ];
    }
}
