<?php

namespace App\Filament\Resources\ManageResignResource\Pages;

use App\Filament\Resources\ManageResignResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateManageResign extends CreateRecord
{

    protected static string $resource = ManageResignResource::class;
    public function getTitle(): string
    {
        return 'Mengajukan Resign'; // Ganti judul halaman
    }
}
