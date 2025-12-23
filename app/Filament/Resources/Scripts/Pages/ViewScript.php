<?php

namespace App\Filament\Resources\Scripts\Pages;

use App\Filament\Resources\Scripts\ScriptResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Artisan;
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
                ->action(function (Action $action) {
                    
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
                    try {
                        // Run in background to prevent blocking
                        dispatch(function () use ($filePath) {
                            Artisan::call('run:powershell', ['script' => $filePath]);
                        });

                        Notification::make()
                            ->title('Script executed successfully')
                            ->body('Script is running in the background.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Script execution failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            EditAction::make(),
        ];
    }
}
