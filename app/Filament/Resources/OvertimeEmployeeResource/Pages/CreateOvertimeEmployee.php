<?php

namespace App\Filament\Resources\OvertimeEmployeeResource\Pages;

use App\Filament\Resources\OvertimeEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class CreateOvertimeEmployee extends CreateRecord
{
    protected static string $resource = OvertimeEmployeeResource::class;
    protected function beforeCreate(): void
    {
        $tanggal = $this->form->getState()['tanggal'];

        $startOfWeek = \Carbon\Carbon::parse($tanggal)->startOfWeek();
        $endOfWeek = \Carbon\Carbon::parse($tanggal)->endOfWeek();

        $count = DB::table('overtime_employees')
            ->where('user_id', auth()->id())
            ->whereBetween('tanggal', [$startOfWeek, $endOfWeek])
            ->where('status', 'success')
            ->count();


        if ($count >= 3) {
            Notification::make()
                ->title('Gagal mengajukan lembur')
                ->body('Kamu sudah mengajukan lembur lebih dari 3 kali minggu ini.')
                ->danger()
                ->send();

            $this->halt(); // stop proses create
        }
    }
}
