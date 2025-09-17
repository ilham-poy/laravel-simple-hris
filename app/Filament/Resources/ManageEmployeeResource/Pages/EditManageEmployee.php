<?php

namespace App\Filament\Resources\ManageEmployeeResource\Pages;

use App\Filament\Resources\ManageEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditManageEmployee extends EditRecord
{
    protected static string $resource = ManageEmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
