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
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
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
            ->whereDate('tanggal', now()->toDateString())
            ->exists();
    }
    // 1. Action 1-Click Absen Hadir
    public function hadirAction(): Action
    {
        return Action::make('hadir')
            ->label('Absen Masuk Sekarang')
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Presensi')
            ->modalDescription('Lakukan presensi masuk untuk shift hari ini?')
            ->action(function () {
                $userId = Auth::id();
                $today  = now()->toDateString();

                // Cari jadwal shift hari ini di employee_schedules
                $schedule = EmployeeSchedule::where('user_id', $userId)
                    ->whereDate('tanggal', $today)
                    ->first();

                if (!$schedule) {
                    Notification::make()
                        ->title('Jadwal Tidak Ditemukan')
                        ->body('Jadwal shift kamu belum diplot oleh HRD untuk hari ini.')
                        ->danger()
                        ->send();
                    return;
                }

                // Cek apakah sudah absen masuk hari ini
                $alreadyAttended = Attendance::where('user_id', $userId)
                    ->whereDate('tanggal', $today)
                    ->exists();

                if ($alreadyAttended) {
                    Notification::make()
                        ->title('Sudah Absen')
                        ->body('Kamu sudah mencatat presensi masuk hari ini.')
                        ->warning()
                        ->send();
                    return;
                }

                // Simpan otomatis tanpa isi form
                Attendance::create([
                    'user_id'              => $userId,
                    'employee_schedule_id' => $schedule->id,
                    'tanggal'              => $today,
                    'jam_masuk'            => now()->format('H:i:s'),
                    'status'               => 'hadir',
                ]);

                Notification::make()
                    ->title('Presensi Berhasil!')
                    ->body('Selamat bekerja!')
                    ->success()
                    ->send();
            });
    }

    // 2. Action Form Modal untuk Izin / Sakit
    public function kendalaAction(): Action
    {
        return Action::make('kendala')
            ->label('Izin / Sakit / Kendala')
            ->color('warning')
            ->icon('heroicon-o-exclamation-triangle')
            ->url(fn(): string => AttendanceResource::getUrl('create'));
    }
}
