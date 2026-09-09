<?php

namespace App\Filament\Resources\EmployeeScheduleResource\Pages;

use App\Filament\Resources\EmployeeScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class CreateEmployeeSchedule extends CreateRecord
{
    protected static string $resource = EmployeeScheduleResource::class;
    public function getTitle(): string
    {
        $data['status'] = 'pending';
        return 'Mengajukan Lembur'; // Ganti judul halaman
    }
    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->title('Gagal Menyimpan Jadwal')
            ->body('Jadwal untuk karyawan pada tanggal tersebut sudah ada.')
            ->danger()
            ->send();

        parent::onValidationError($exception);
    }
}
