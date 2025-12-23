<?php

namespace App\Filament\Resources\ExecutionLogs\Pages;

use App\Filament\Resources\ExecutionLogs\ExecutionLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExecutionLog extends CreateRecord
{
    protected static string $resource = ExecutionLogResource::class;
}
