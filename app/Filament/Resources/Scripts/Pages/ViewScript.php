<?php

namespace App\Filament\Resources\Scripts\Pages;

use App\Filament\Resources\Scripts\ScriptResource;
use App\Filament\Widgets\ScriptLogs;
use App\Jobs\RunPowershellScript;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;

class ViewScript extends ViewRecord
{
    protected static string $resource = ScriptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('run_script')
                ->label('Run Script')
                ->icon('heroicon-o-play')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    
                    $attachment = is_array($this->record->attachment) 
                        ? $this->record->attachment[0] ?? null 
                        : $this->record->attachment;

                    if (!$attachment) {
                        Notification::make()
                            ->title('No script file attached')
                            ->danger()
                            ->send();
                        return;
                    }

                    $filePath = Storage::path($attachment);
                    
                    if (!file_exists($filePath)) {
                        Notification::make()
                            ->title('Script file not found')
                            ->danger()
                            ->send();
                        return;
                    }

                    RunPowershellScript::dispatch(
                        $filePath,
                        $this->record->id,
                        auth()->id()
                    );

                    Notification::make()
                        ->title('Script queued for execution')
                        ->body('The PowerShell script is running in the background.')
                        ->success()
                        ->send();
                }),
            EditAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            ScriptLogs::make(['scriptId' => $this->record->id]),
        ];
    }

}
