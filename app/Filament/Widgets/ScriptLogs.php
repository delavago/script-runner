<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ExecutionLogs\ExecutionLogResource;
use App\models\ExecutionLog;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ScriptLogs extends TableWidget
{
    protected int|string|array $columnSpan = 'full';
    public string $scriptId = '';

    public function mount(string $scriptId): void
    {
        $this->scriptId = $scriptId;
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ExecutionLog::query()
                                ->where('script_id', $this->scriptId)
                                ->orderBy('created_at', 'desc'))
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('script.script_name')
                    ->label('Script')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('Executed By')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Executed At')
                    ->dateTime()
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn(ExecutionLog $record): string => ExecutionLogResource::getUrl('view', [$record->id]))

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
