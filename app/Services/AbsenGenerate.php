<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Attendance;

class AbsenGenerate
{
    public function __invoke()
    {
        $deadline = Carbon::createFromTime(9, 30, 0, 'Asia/Jakarta');

        // Ambil semua user
        $users = User::all();

        foreach ($users as $user) {
            $hasAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('tanggal', Carbon::today())
                ->exists();

            if (!$hasAttendance) {
                Attendance::create([
                    'user_id' => $user->id,
                    'tanggal' => Carbon::today(),
                    'status'  => 'tidak_hadir',
                    'jam_masuk' => null,
                ]);
            }
        }
    }
}
