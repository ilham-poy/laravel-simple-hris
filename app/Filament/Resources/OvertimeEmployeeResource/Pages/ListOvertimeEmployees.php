<?php

namespace App\Filament\Resources\OvertimeEmployeeResource\Pages;

use App\Filament\Resources\OvertimeEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOvertimeEmployees extends ListRecords
{
    protected static string $resource = OvertimeEmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
