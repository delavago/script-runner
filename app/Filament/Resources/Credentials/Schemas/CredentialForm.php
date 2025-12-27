<?php

namespace App\Filament\Resources\Credentials\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CredentialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('type')
                    ->options([
                        'database' => 'Database Credentials',
                        'windows' => 'Windows Credentials',
                        'ssh' => 'SSH',
                        'linux' => 'Linux Credentials',
                    ])
                    ->required(),
                TextInput::make('username'),
                TextInput::make('password')
                ->password()
                ->revealable(),
                Textarea::make('private_key')
                    ->columnSpanFull(),
                TextInput::make('host'),
                TextInput::make('port')
                    ->numeric(),
                TextInput::make('domain'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Toggle::make('active')
                    ->required(),
            ]);
    }
}
