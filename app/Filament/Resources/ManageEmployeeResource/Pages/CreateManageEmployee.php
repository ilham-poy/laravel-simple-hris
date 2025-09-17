<?php

namespace App\Filament\Resources\ManageEmployeeResource\Pages;

use App\Filament\Resources\ManageEmployeeResource;
use Filament\Actions;
use App\Models\ActivityLog;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateManageEmployee extends CreateRecord
{
    protected static string $resource = ManageEmployeeResource::class;
    protected function afterCreate(): void
    {
        ActivityLog::create([
            'action' => 'create',
            'model_type' => class_basename($this->record),
            'model_id' => $this->record->id,
            'performed_by' => Auth::id(),
        ]);
    }
}
