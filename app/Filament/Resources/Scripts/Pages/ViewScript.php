<?php

namespace App\Filament\Resources\Scripts\Pages;

use App\Filament\Resources\Scripts\ScriptResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewScript extends ViewRecord
{
    protected static string $resource = ScriptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
