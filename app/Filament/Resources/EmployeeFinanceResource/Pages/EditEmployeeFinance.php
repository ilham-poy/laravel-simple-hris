<?php

namespace App\Filament\Resources\EmployeeFinanceResource\Pages;

use App\Filament\Resources\EmployeeFinanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeFinance extends EditRecord
{
    protected static string $resource = EmployeeFinanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
