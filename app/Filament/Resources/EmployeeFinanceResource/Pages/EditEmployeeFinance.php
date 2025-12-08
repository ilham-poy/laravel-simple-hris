<?php

namespace App\Filament\Resources\EmployeeFinanceResource\Pages;

use App\Filament\Resources\EmployeeFinanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;

class EditEmployeeFinance extends EditRecord
{
    protected static string $resource = EmployeeFinanceResource::class;
    public function getTitle(): string
    {
        return 'Ubah Gaji Karyawan'; // Ganti judul halaman
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            $record->update($data);
            return $record;
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                Notification::make()
                    ->title('Gagal Update')
                    ->body('Data gaji untuk bulan ini sudah ada.')
                    ->danger()
                    ->send();

                $this->halt();
            }

            throw $e;
        }
    }
}
