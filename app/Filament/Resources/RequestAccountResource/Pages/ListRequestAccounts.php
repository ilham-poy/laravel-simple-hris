<?php

namespace App\Filament\Resources\RequestAccountResource\Pages;

use App\Filament\Resources\RequestAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRequestAccounts extends ListRecords
{
    protected static string $resource = RequestAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
