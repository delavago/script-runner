<?php

namespace App\Filament\Resources\Scripts;

use App\Filament\Resources\Scripts\Pages\CreateScript;
use App\Filament\Resources\Scripts\Pages\EditScript;
use App\Filament\Resources\Scripts\Pages\ListScripts;
use App\Filament\Resources\Scripts\Pages\ViewScript;
use App\Filament\Resources\Scripts\Schemas\ScriptForm;
use App\Filament\Resources\Scripts\Schemas\ScriptInfolist;
use App\Filament\Resources\Scripts\Tables\ScriptsTable;
use App\Models\Script;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ScriptResource extends Resource
{
    protected static ?string $model = Script::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Scripts';

    public static function form(Schema $schema): Schema
    {
        return ScriptForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ScriptInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScriptsTable::configure($table);
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
            'index' => ListScripts::route('/'),
            'create' => CreateScript::route('/create'),
            'view' => ViewScript::route('/{record}'),
            'edit' => EditScript::route('/{record}/edit'),
        ];
    }
}
