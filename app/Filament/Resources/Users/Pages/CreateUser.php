<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    private string $role="";

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if($data['password'] === null){
            Notification::make()
                ->title('Password is required for new user accounts')
                ->danger()
                ->send();
            $this->halt();
        }

        $this->role = $data['Role'];
        //delete Role from data to prevent error
        unset($data['Role']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->assignRole($this->role);
    }
}
