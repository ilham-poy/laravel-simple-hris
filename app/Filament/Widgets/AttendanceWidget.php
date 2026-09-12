<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Filament\Resources\AttendanceResource;
use App\Models\EmployeeSchedule;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.attendance-widget';
    protected int | string | array $columnSpan = 'full';

    public function hasAttendedToday(): bool
    {
        return Attendance::where('user_id', Auth::id())
            ->whereDate('tanggal', now('Asia/Jakarta')->toDateString())
            ->exists();
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // HRD dan Super Admin bebas akses tanpa batasan waktu
        if ($user->hasAnyRole(['super-admin', 'hrd-officer']) || $user->can('role:update')) {
            return true;
        }

        // Ambil jadwal shift karyawan hari ini
        $todaySchedule = EmployeeSchedule::where('user_id', $user->id)
            ->whereDate('tanggal', Carbon::today('Asia/Jakarta'))
            ->where('shift_type', '!=', 'off')
            ->first();

        if (!$todaySchedule) {
            return false;
        }

        $now = Carbon::now('Asia/Jakarta');
        $dateString = Carbon::parse($todaySchedule->tanggal)->format('Y-m-d');
        $shiftStart = Carbon::parse($dateString . ' ' . $todaySchedule->jam_masuk, 'Asia/Jakarta');

        $earliestAllowed = $shiftStart->copy()->subHour();   // H-1 jam
        $latestAllowed   = $shiftStart->copy()->addHours(2);  // H+2 jam

        return $now->between($earliestAllowed, $latestAllowed);
    }

    public function hadirAction(): Action
    {
        return Action::make('hadir')
            ->label('Absen Masuk Sekarang')
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->disabled(fn() => !static::canCreate() || $this->hasAttendedToday())
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Presensi')
            ->modalDescription('Lakukan presensi masuk untuk shift hari ini?')
            ->action(function () {
                $now    = Carbon::now('Asia/Jakarta');
                $userId = Auth::id();
                $today  = $now->toDateString();

                // Double Check Akses Rentang Waktu
                if (!static::canCreate()) {
                    Notification::make()
                        ->title('Akses Presensi Ditutup')
                        ->body('Presensi hanya dapat dilakukan mulai 1 jam sebelum shift hingga 2 jam setelah shift dimulai.')
                        ->danger()
                        ->send();
                    return;
                }

                $schedule = EmployeeSchedule::where('user_id', $userId)
                    ->whereDate('tanggal', $today)
                    ->where('shift_type', '!=', 'off')
                    ->first();

                if (!$schedule) {
                    Notification::make()
                        ->title('Jadwal Tidak Ditemukan')
                        ->body('Jadwal shift kamu belum diplot oleh HRD untuk hari ini.')
                        ->danger()
                        ->send();
                    return;
                }

                if ($this->hasAttendedToday()) {
                    Notification::make()
                        ->title('Sudah Absen')
                        ->body('Kamu sudah mencatat presensi hari ini.')
                        ->warning()
                        ->send();
                    return;
                }

                // Hitung keterlambatan (jika ada)
                $dateString = Carbon::parse($schedule->tanggal)->format('Y-m-d');
                $jamMasukShift = Carbon::parse($dateString . ' ' . $schedule->jam_masuk, 'Asia/Jakarta');

                $status = 'hadir';
                $durasiTelat = null;

                // Jika jam sekarang melewati jam masuk shift, tandai 'telat' & hitung menitnya
                if ($now->greaterThan($jamMasukShift)) {
                    $status = 'telat';
                    $durasiTelat = (int) $jamMasukShift->diffInMinutes($now);
                }

                Attendance::create([
                    'user_id'               => $userId,
                    'employee_schedule_id'  => $schedule->id,
                    'tanggal'               => $today,
                    'jam_masuk'             => $now->format('H:i:s'),
                    'status'                => $status,
                    'durasi_keterlambatan' => $durasiTelat,
                    'validasi'              => 'pending',
                ]);

                $msgBody = $status === 'telat'
                    ? "Presensi dicatat (Telat {$durasiTelat} menit). Selamat bekerja!"
                    : 'Selamat bekerja!';

                Notification::make()
                    ->title('Presensi Berhasil!')
                    ->body($msgBody)
                    ->success()
                    ->send();
            });
    }

    public function kendalaAction(): Action
    {
        return Action::make('kendala')
            ->label('Izin / Sakit / Kendala')
            ->color('warning')
            ->icon('heroicon-o-exclamation-triangle')
            ->disabled(fn() => !static::canCreate() || $this->hasAttendedToday())
            ->url(fn(): string => AttendanceResource::getUrl('create'));
    }
}
