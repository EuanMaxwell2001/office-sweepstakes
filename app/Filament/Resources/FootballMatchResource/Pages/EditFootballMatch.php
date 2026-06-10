<?php

namespace App\Filament\Resources\FootballMatchResource\Pages;

use App\Filament\Resources\FootballMatchResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFootballMatch extends EditRecord
{
    protected static string $resource = FootballMatchResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
