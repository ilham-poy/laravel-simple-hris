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
}
