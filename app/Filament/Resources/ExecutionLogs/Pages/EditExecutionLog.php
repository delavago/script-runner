<?php

namespace App\Filament\Resources\ExecutionLogs\Pages;

use App\Filament\Resources\ExecutionLogs\ExecutionLogResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditExecutionLog extends EditRecord
{
    protected static string $resource = ExecutionLogResource::class;
    protected ?string $heading = 'View Payment Reminder';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
