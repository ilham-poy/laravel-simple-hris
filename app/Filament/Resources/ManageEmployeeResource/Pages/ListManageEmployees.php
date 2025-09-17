<?php

namespace App\Filament\Resources\ManageEmployeeResource\Pages;

use App\Filament\Resources\ManageEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListManageEmployees extends ListRecords
{
    protected static string $resource = ManageEmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
