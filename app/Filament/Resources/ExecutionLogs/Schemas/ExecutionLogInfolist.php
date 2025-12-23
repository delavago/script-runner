<?php

namespace App\Filament\Resources\ExecutionLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ExecutionLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('script_logs')
                    ->markdown()
                    ->columnSpanFull(),
                TextEntry::make('script.script_name')
                    ->label('Script'),
                TextEntry::make('user.email')
                    ->label('Executed By'),
                TextEntry::make('created_at')
                    ->label('Executed At')
                    ->dateTime()
                    ->placeholder('-')
            ]);
    }
}
