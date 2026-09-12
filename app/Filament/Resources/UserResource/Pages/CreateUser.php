<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Account;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return 'Pembuatan User';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Update status Account
        $accountId = $data['account_name'] ?? null;
        if ($accountId) {
            Account::where('id', $accountId)->update([
                'status' => 'approved',
            ]);

            $account = Account::find($accountId);
            if ($account) {
                $data['name'] = $account->name;
            }
        }

        unset($data['account_name']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $roleName = $this->data['role_name'] ?? null;
        $permissions = $this->data['permissions'] ?? [];

        if ($roleName) {
            // 2. Cari Role (atau Buat Baru jika belum ada)
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'web']
            );

            // 3. Update permission milik role tersebut jika ada yang dicentang
            if (!empty($permissions)) {
                $role->permissions()->sync($permissions);
            }

            // 4. Pasang Role ini ke User baru
            $this->record->assignRole($role);
        }
    }
}
