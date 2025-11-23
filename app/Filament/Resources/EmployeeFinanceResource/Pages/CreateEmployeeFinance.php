<?php

namespace App\Filament\Resources\EmployeeFinanceResource\Pages;

use App\Filament\Resources\EmployeeFinanceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\Attendance;

class CreateEmployeeFinance extends CreateRecord
{
    protected static string $resource = EmployeeFinanceResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();
        $alpa  = Attendance::where("user_id", $data['user_id'])
            ->where('status', 'alpha')->where('tanggal', [$start, $end])->count();

        $total =  $data['gaji_pokok'] + ($data['jam_lembur'] *
            $data['gaji_lembur']) -  ($data['tidak_masuk'] * $alpa);

        $data['total_gaji'] = $total;
        // dd($data);

        return $data;
    }
}
