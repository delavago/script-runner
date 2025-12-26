<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->required(),
                Select::make('role')
                    // map the roles from the database to the format {'name' => 'name'}
                    ->options(Role::all()->pluck('name', 'name')->toArray())
                    ->default(fn ($record) => $record?->roles?->first()?->name)
                    ->afterStateHydrated(function (Select $component, $state, $record) {
                        if ($record) {
                            $component->state($record->roles->first()?->name);
                        }
                    })
                    // ->dehydrated(false)
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password(),
            ]);
    }
}
