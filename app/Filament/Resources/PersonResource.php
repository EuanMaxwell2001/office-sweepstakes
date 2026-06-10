<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonResource\Pages;
use App\Models\Person;
use Filament\Forms\Components\FileUpload;
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
use Filament\Tables\Table;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                FileUpload::make('avatar')
                    ->image()
                    ->imageEditor()
                    ->directory('avatars')
                    ->disk('public')
                    ->avatar()
                    ->columnSpanFull(),

                Toggle::make('is_office')
                    ->label('Office team slot')
                    ->helperText('Mark if this is the shared office entry'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn (Person $record) => $record->avatar_url),

                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('teams_count')
                    ->counts('teams')
                    ->label('Teams')
                    ->badge()
                    ->color('success'),

                IconColumn::make('is_office')
                    ->label('Office')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PersonResource\RelationManagers\TeamsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit'   => Pages\EditPerson::route('/{record}/edit'),
        ];
    }
}
