<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\Attendance;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;
    public function getTitle(): string
    {
        return 'Daftar Kehadiran'; // Ganti judul halaman
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $jamMasuk = Carbon::parse($data['jam_masuk'], 'Asia/Jakarta');
        $start = Carbon::createFromTime(7, 45, 0, 'Asia/Jakarta');
        $end   = Carbon::createFromTime(8, 30, 0, 'Asia/Jakarta');

        if (!$jamMasuk->between($start, $end) && $data['status'] == 'hadir') {
            $data['status'] = 'telat';
        }

        // Cek apakah user sudah absen hari ini
        $alreadyAttendance = Attendance::where('user_id', $data['user_id'])
            ->whereDate('tanggal', now()->toDateString())
            ->exists();

        if ($alreadyAttendance) {
            // Jika sudah absen, kita bisa hentikan proses atau beri notifikasi
            Notification::make()
                ->title('Sudah Absen')
                ->body('Anda sudah melakukan absen hari ini.')
                ->danger()
                ->send();

            // Lempar exception agar record tidak tersimpan
            $this->halt();
            return [];
        }

        // Set tanggal otomatis ke hari ini jika belum ada
        if (empty($data['tanggal'])) {
            $data['tanggal'] = now()->toDateString();
        }

        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
