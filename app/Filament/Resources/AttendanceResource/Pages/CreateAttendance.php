<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Carbon\Carbon;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $jamMasuk = Carbon::parse($data['jam_masuk'], 'Asia/Jakarta');
        $start = Carbon::createFromTime(8, 0, 0, 'Asia/Jakarta');
        $end   = Carbon::createFromTime(8, 30, 0, 'Asia/Jakarta');

        if (!$jamMasuk->between($start, $end) && $data['status'] == 'hadir') {
            $data['status'] = 'telat';
        }

        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
