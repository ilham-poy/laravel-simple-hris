<?php

namespace App\Filament\Resources\ManageResignResource\Pages;

use App\Filament\Resources\ManageResignResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListManageResigns extends ListRecords
{
    protected static string $resource = ManageResignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
