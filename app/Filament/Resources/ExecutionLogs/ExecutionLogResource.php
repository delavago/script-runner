<?php

namespace App\Filament\Resources\ExecutionLogs;

use App\Filament\Resources\ExecutionLogs\Pages\CreateExecutionLog;
use App\Filament\Resources\ExecutionLogs\Pages\EditExecutionLog;
use App\Filament\Resources\ExecutionLogs\Pages\ListExecutionLogs;
use App\Filament\Resources\ExecutionLogs\Pages\ViewExecutionLog;
use App\Filament\Resources\ExecutionLogs\Schemas\ExecutionLogForm;
use App\Filament\Resources\ExecutionLogs\Schemas\ExecutionLogInfolist;
use App\Filament\Resources\ExecutionLogs\Tables\ExecutionLogsTable;
use App\Models\ExecutionLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExecutionLogResource extends Resource
{
    protected static ?string $model = ExecutionLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Log';

    public static function form(Schema $schema): Schema
    {
        return ExecutionLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExecutionLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExecutionLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExecutionLogs::route('/'),
            'create' => CreateExecutionLog::route('/create'),
            'view' => ViewExecutionLog::route('/{record}'),
            'edit' => EditExecutionLog::route('/{record}/edit'),
        ];
    }
}
