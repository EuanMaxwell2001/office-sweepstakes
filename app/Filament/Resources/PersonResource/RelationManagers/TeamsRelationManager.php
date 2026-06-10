<?php

namespace App\Filament\Resources\PersonResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamsRelationManager extends RelationManager
{
    protected static string $relationship = 'teams';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('country_code')
                ->label('Country Code (ISO 3166-1 alpha-2)')
                ->maxLength(10)
                ->helperText('e.g. gb-eng, fr, de — used for flag display'),
            Toggle::make('is_eliminated')->label('Eliminated'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('flag_url')
                    ->label('Flag')
                    ->getStateUsing(fn ($record) => $record->flag_url)
                    ->width(40)->height(28),
                TextColumn::make('name')->searchable(),
                TextColumn::make('country_code')->label('Code'),
                IconColumn::make('is_eliminated')->boolean()->label('Out'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}
