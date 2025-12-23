<?php

namespace App\Filament\Resources\ExecutionLogs\Pages;

use App\Filament\Resources\ExecutionLogs\ExecutionLogResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewExecutionLog extends ViewRecord
{
    protected static string $resource = ExecutionLogResource::class;
    protected ?string $heading = 'Logs';

    protected function getHeaderActions(): array
    {
        return [
            // EditAction::make(),
        ];
    }
}
