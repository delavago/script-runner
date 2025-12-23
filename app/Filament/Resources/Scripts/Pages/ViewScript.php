<?php

namespace App\Filament\Resources\Scripts\Pages;

use App\Filament\Resources\Scripts\ScriptResource;
use App\Filament\Widgets\ScriptLogs;
use App\Jobs\RunBashScript;
use App\Jobs\RunPowershellScript;
use App\Jobs\RunPythonScript;
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
                    
                    if (!$this->record->active) {
                        Notification::make()
                            ->title('Script is disabled')
                            ->body('This script has been disabled and cannot be executed.')
                            ->warning()
                            ->send();
                        return;
                    }

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

                    $fileType = $this->record->file_type;

                    match ($fileType) {
                        'powershell' => RunPowershellScript::dispatch(
                            $filePath,
                            $this->record->id,
                            auth()->id()
                        ),
                        'python' => RunPythonScript::dispatch(
                            $filePath,
                            $this->record->id,
                            auth()->id()
                        ),
                        'bash' => RunBashScript::dispatch(
                            $filePath,
                            $this->record->id,
                            auth()->id()
                        ),
                        default => null,
                    };

                    if (!in_array($fileType, ['powershell', 'python', 'bash'])) {
                        Notification::make()
                            ->title('Unsupported script type')
                            ->body("Script type '{$fileType}' is not supported.")
                            ->danger()
                            ->send();
                        return;
                    }

                    Notification::make()
                        ->title('Script queued for execution')
                        ->body("The {$fileType} script is running in the background.")
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
