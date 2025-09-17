<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    // protected function afterCreate(): void
    // {
    //     $this->record->assignRole($this->form->getState()['role']);
    // }
    protected function afterCreate(): void
    {
        $roles = $this->data['roles'] ?? [];

        // Assign multiple roles
        if (!empty($roles)) {
            $this->record->assignRole($roles);
        }
    }
}
