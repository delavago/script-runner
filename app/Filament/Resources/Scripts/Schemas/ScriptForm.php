<?php

namespace App\Filament\Resources\Scripts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ScriptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uuid')
                    ->label('UUID')
                    ->required(),
                TextInput::make('script_name')
                    ->required(),
                TextInput::make('description'),
                TextInput::make('file_name')
                    ->required(),
                TextInput::make('file_type')
                    ->required(),
                Textarea::make('file_path')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('active')
                    ->required(),
            ]);
    }
}
