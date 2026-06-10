<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Models\Person;
use App\Models\Team;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-flag';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                Select::make('person_id')
                    ->label('Person')
                    ->options(Person::orderBy('name')->pluck('name', 'id'))
                    ->required()
                    ->searchable(),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('country_code')
                    ->label('Country Code (flagcdn)')
                    ->maxLength(10)
                    ->helperText('e.g. gb-eng, fr, de'),

                Toggle::make('is_eliminated')
                    ->label('Eliminated from tournament'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('flag_url')
                    ->label('Flag')
                    ->getStateUsing(fn (Team $record) => $record->flag_url)
                    ->width(40)->height(28),

                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('person.name')
                    ->label('Owner')
                    ->sortable()
                    ->badge(),

                TextColumn::make('country_code')
                    ->label('Code'),

                IconColumn::make('is_eliminated')
                    ->label('Eliminated')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('person_id')
                    ->label('Owner')
                    ->options(Person::orderBy('name')->pluck('name', 'id')),

                TernaryFilter::make('is_eliminated')
                    ->label('Eliminated'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit'   => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
