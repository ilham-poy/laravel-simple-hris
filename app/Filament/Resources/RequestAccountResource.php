<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RequestAccountResource\Pages;
use App\Filament\Resources\RequestAccountResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Account;
use Dom\Text;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class RequestAccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && ($user->hasRole('hrd-officer') || $user->hasRole('super-admin')) && ($user->can('view-employee-data')
            ||  $user->can('manage-roles-and-permissions'));
    }
    public static function canEdit(Model $record): bool
    {
        return Auth::check() && Auth::user()->can('manage-roles-and-permissions');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('name')->label('Nama Lengkap')->required(),
                TextInput::make('jabatan')->label('Jabatan & Divisi')->required(),
                TextInput::make('keterangan')->label('Detail Jobs')->required(),
                Select::make('status')->options(function () {
                    if (Auth::user()->hasRole('hrd-officer')) {
                        return ['pending' => 'Pending'];
                    }

                    if (Auth::user()->hasRole('super-admin')) {
                        return [
                            'pending' => 'Pending',
                            'accept'  => 'Selesai',
                            'failed'  => 'Gagal',
                        ];
                    }
                })->label('Status')->required()
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')
                    ->label('Nama Karyawan')
                    ->searchable(),
                TextColumn::make('jabatan')->label('Jabatan'),
                TextColumn::make('keterangan')->label('Keterangan'),
                TextColumn::make('status')->label('Status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(fn($record) =>
                Auth::check() &&
                    Auth::user()->hasRole('employee') &&
                    $record->status !== 'success' && $record->status !== 'failed'),
                Tables\Actions\DeleteAction::make(),
                Action::make('accept')
                    ->label('Accept')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(
                        fn($record) =>
                        Auth::check() &&
                            Auth::user()->hasRole('super-admin') &&
                            $record->status !== 'failed' && $record->status !== 'accept'
                    )
                    ->action(function ($record) {

                        $record->status = 'accept';
                        $record->save();
                        Notification::make()
                            ->title("Account Sudah Selesai Dibuat")
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->visible(
                        fn($record) =>
                        Auth::check() &&
                            Auth::user()->hasRole('super-admin') &&
                            $record->status !== 'failed' && $record->status !== 'accept'

                    )
                    ->action(function ($record) {
                        $record->status = 'failed';
                        $record->save();
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRequestAccounts::route('/'),
            'create' => Pages\CreateRequestAccount::route('/create'),
            'edit' => Pages\EditRequestAccount::route('/{record}/edit'),
        ];
    }
}
