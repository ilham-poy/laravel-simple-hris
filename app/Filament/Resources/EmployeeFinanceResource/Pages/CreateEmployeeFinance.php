<?php

namespace App\Filament\Resources\EmployeeFinanceResource\Pages;

use App\Filament\Resources\EmployeeFinanceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\Attendance;
use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;

class CreateEmployeeFinance extends CreateRecord
{
    protected static string $resource = EmployeeFinanceResource::class;

    public function getTitle(): string
    {
        return 'Gaji Karyawan';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Paksa jam_lembur bertipe integer murni
        $data['jam_lembur'] = (int) ($data['jam_lembur'] ?? 0);

        $workMonth = Carbon::parse($data['work_month']);
        $start     = $workMonth->copy()->startOfMonth();
        $end       = $workMonth->copy()->endOfMonth();

        $alpa = Attendance::where('user_id', $data['user_id'])
            ->where('status', 'alpha')
            ->whereBetween('tanggal', [$start, $end])
            ->count();

        $total = $data['gaji_pokok']
            + ($data['jam_lembur'] * $data['gaji_lembur'])
            - ($data['tidak_masuk'] * $alpa);

        $data['total_gaji'] = max(0, $total);

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return static::getModel()::create($data);
        } catch (QueryException $e) {
            // MySQL Duplicate Entry Code 1062
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
                Notification::make()
                    ->title('Gagal Menyimpan Data')
                    ->body('Data gaji karyawan ini untuk bulan yang dipilih sudah ada.')
                    ->danger()
                    ->send();

                $this->halt();
            }

            // Lempar error jika disebabkan oleh hal lain di luar 1062
            throw $e;
        }
    }
}
