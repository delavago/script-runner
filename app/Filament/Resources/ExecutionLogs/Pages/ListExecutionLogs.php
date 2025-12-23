<?php

namespace App\Filament\Resources\ExecutionLogs\Pages;

use App\Filament\Resources\ExecutionLogs\ExecutionLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExecutionLogs extends ListRecords
{
    protected static string $resource = ExecutionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
