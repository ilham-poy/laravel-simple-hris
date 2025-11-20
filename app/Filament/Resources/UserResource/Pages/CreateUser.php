<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;

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
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // ID account yang dipilih dari Select
        $accountId = $data['account_name'];

        // Update status account menjadi "accept"
        Account::where('id', $accountId)->update([
            'status' => 'accept',
        ]);

        // Ambil data account
        $account = Account::findOrFail($accountId);

        // Set nama user = nama account
        $data['name'] = $account->name;

        // (Opsional) jika tidak ingin menyimpan account_name ke users
        unset($data['account_name']);

        return $data;
    }
}
