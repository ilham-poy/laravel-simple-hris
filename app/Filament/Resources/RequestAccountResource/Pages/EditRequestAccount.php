<?php

namespace App\Filament\Resources\RequestAccountResource\Pages;

use App\Filament\Resources\RequestAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRequestAccount extends EditRecord
{
    protected static string $resource = RequestAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
