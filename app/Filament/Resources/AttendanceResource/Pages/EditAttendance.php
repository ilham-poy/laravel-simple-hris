<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;
    protected function authorizeAccess(): void
    {
        if (Auth::user()->hasRole('employee')) {
            abort(403, 'Anda tidak diizinkan mengedit data kehadiran.');
        }
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
