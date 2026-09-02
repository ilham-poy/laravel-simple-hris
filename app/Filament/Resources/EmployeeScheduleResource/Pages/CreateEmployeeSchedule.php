<?php

namespace App\Filament\Resources\EmployeeScheduleResource\Pages;

use App\Filament\Resources\EmployeeScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class CreateEmployeeSchedule extends CreateRecord
{
    protected static string $resource = EmployeeScheduleResource::class;
    public function getTitle(): string
    {
        $data['status'] = 'pending';
        return 'Mengajukan Lembur'; // Ganti judul halaman
    }
}
