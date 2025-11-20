<?php

namespace App\Filament\Resources\EmployeeFinanceResource\Pages;

use App\Filament\Resources\EmployeeFinanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeFinances extends ListRecords
{
    protected static string $resource = EmployeeFinanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
