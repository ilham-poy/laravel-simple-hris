<?php

namespace App\Filament\Resources\EmployeeScheduleResource\Pages;

use App\Filament\Resources\EmployeeScheduleResource;
use App\Filament\Resources\OvertimeEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeSchedule extends EditRecord
{
    protected static string $resource = EmployeeScheduleResource::class;
    public function getTitle(): string
    {
        return 'Ubah Data Lembur'; // Ganti judul halaman
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
