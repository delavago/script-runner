<?php

namespace App\Filament\Resources\Credentials\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CredentialInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('name'),
                TextEntry::make('type'),
                TextEntry::make('username')
                    ->placeholder('-'),
                TextEntry::make('password')
                    ->placeholder('-'),
                TextEntry::make('private_key')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('host')
                    ->placeholder('-'),
                TextEntry::make('port')
                    ->placeholder('-'),
                TextEntry::make('domain')
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
