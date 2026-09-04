<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\EmployeeSchedule;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EmployeeScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua karyawan (driver, warehouse, dll)
        $employees = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'super-admin');
        })->get();

        if ($employees->isEmpty()) {
            return;
        }

        $startDate = Carbon::now()->startOfMonth();
        $endDate   = Carbon::now()->endOfMonth();

        // Acuan Jam Kerja per Shift
        $shiftHours = [
            'pagi'  => ['masuk' => '08:00:00', 'keluar' => '16:00:00'],
            'siang' => ['masuk' => '16:00:00', 'keluar' => '00:00:00'],
            'malam' => ['masuk' => '00:00:00', 'keluar' => '08:00:00'],
        ];

        foreach ($employees as $index => $employee) {
            $currentDate = $startDate->copy();

            // Variasi shift berdasarkan index user
            $assignedShift = ($index % 2 === 0) ? 'pagi' : 'siang';

            while ($currentDate->lte($endDate)) {
                $isSunday = $currentDate->isSunday();
                $shift    = $isSunday ? 'off' : $assignedShift;

                // Lembur di hari Rabu (diset 2 jam penuh, integer)
                $isOvertime  = !$isSunday && $currentDate->isWednesday();
                $totalLembur = $isOvertime ? 2 : 0; // <-- Diubah jadi Integer
                $ketLembur   = $isOvertime ? 'Lembur bongkar muat barang' : null;

                EmployeeSchedule::updateOrCreate(
                    [
                        'user_id' => $employee->id,
                        'tanggal' => $currentDate->toDateString(),
                    ],
                    [
                        'shift_type'        => $shift,
                        'jam_masuk'         => $shift === 'off' ? null : $shiftHours[$shift]['masuk'],
                        'jam_keluar'        => $shift === 'off' ? null : $shiftHours[$shift]['keluar'],
                        'total_lembur'      => $totalLembur,
                        'keterangan_lembur' => $ketLembur,
                        'status'            => 'approved',
                    ]
                );

                $currentDate->addDay();
            }
        }
    }
}
