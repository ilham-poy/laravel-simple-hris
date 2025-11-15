<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Carbon\Carbon;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $jamMasuk = Carbon::parse($data['jam_masuk'], 'Asia/Jakarta');
        $start = Carbon::createFromTime(8, 0, 0, 'Asia/Jakarta');
        $end   = Carbon::createFromTime(8, 30, 0, 'Asia/Jakarta');

        if (!$jamMasuk->between($start, $end)) {
            $data['status'] = 'telat';
        }

        return $data;
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
