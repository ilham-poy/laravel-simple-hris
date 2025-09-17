<?php

namespace App\Filament\Resources\OvertimeEmployeeResource\Pages;

use App\Filament\Resources\OvertimeEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOvertimeEmployee extends EditRecord
{
    protected static string $resource = OvertimeEmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
