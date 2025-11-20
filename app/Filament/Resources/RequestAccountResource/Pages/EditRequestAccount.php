<?php

namespace App\Filament\Resources\RequestAccountResource\Pages;

use App\Filament\Resources\RequestAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\User;

class EditRequestAccount extends EditRecord
{
    protected static string $resource = RequestAccountResource::class;
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    protected function saved($record): void
    {
        if ($record->user_id) {
            $user = User::find($record->user_id);

            if ($user) {
                $user->update([
                    'name'  => $record->name,
                    'email' => $record->email,
                ]);
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
