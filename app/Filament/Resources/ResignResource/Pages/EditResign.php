<?php

namespace App\Filament\Resources\ResignResource\Pages;

use App\Filament\Resources\ResignResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditResign extends EditRecord
{
    protected static string $resource = ResignResource::class;
    public function getTitle(): string
    {
        return 'Ubah Data Resign'; // Ganti judul halaman
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
