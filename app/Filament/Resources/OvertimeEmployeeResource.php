<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OvertimeEmployeeResource\Pages;
use App\Filament\Resources\OvertimeEmployeeResource\RelationManagers;
use App\Models\OvertimeEmployee;
use Illuminate\Validation\ValidationException;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;

class OvertimeEmployeeResource extends Resource
{
    protected static ?string $model = OvertimeEmployee::class;


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    // public static function canViewAny(): bool
    // {
    //     return  Auth::user()->hasRole('hrd-officer') || Auth::user()->hasRole('employee');
    // }
    // public static function canCreate(): bool
    // {
    //     return Auth::check() && Auth::user()->can('submit-overtime');
    // }
    // public static function canEdit(Model $record): bool
    // {
    //     return Auth::check() && (Auth::user()->hasRole('hrd-officer') || Auth::user()->hasRole('employee'));
    // }

    // public static function canDelete(Model $record): bool
    // {
    //     return Auth::check() && Auth::user()->hasRole('hrd-officer');
    // }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                        modifyQueryUsing: function ($query) {
                            // Jika HRD, tampilkan semua user kecuali super admin
                            if (Auth::user()->hasRole('hrd-officer')) {
                                return $query->whereDoesntHave('roles', function ($q) {
                                    $q->where('name', 'super-admin');
                                });
                            }

                            // Jika employee, hanya tampilkan dirinya sendiri
                            if (Auth::user()->hasRole('employee')) {
                                return $query->where('id', Auth::id());
                            }

                            // Default: tampilkan semua
                            return $query;
                        }
                    )->required()
                    ->visible(fn() => !Auth::user()->hasRole('super admin')),
                Select::make('total_lembur')
                    ->label('Berapa Jam Lembur')
                    ->options([
                        '2' => '2 Jam',
                        '4' => '4 Jam',
                    ])
                    ->required(),
                DatePicker::make('tanggal')
                    ->required()
                    ->afterOrEqual(now()->startOfWeek())
                    ->beforeOrEqual(now()->addDays(14))
                    ->hint(fn() => session('errors')?->first('tanggal'))

            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('user.name')
                    ->label('Nama Karyawan'),
                TextColumn::make('tanggal')->label('Tanggal Lembur'),
                TextColumn::make('total_lembur')->label('Total Jam Lembur'),
                TextColumn::make('status')->label('Status Lembur'),
            ])->defaultSort('tanggal', 'desc')->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                if ($user->hasRole('employee')) {
                    $query->where('user_id', $user->id);
                }
            })
            ->filters([
                //
            ])
            ->actions([
                Action::make('accept')
                    ->label('Accept')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(
                        fn($record) =>
                        Auth::check() &&
                            Auth::user()->hasRole('hrd-officer') &&
                            $record->status !== 'success' && $record->status !== 'failed'
                    )
                    ->action(function ($record) {

                        $userId   = $record->user_id;
                        $tanggal  = $record->tanggal;
                        $startOfWeek = \Carbon\Carbon::parse($tanggal)->startOfWeek();
                        $endOfWeek   = \Carbon\Carbon::parse($tanggal)->endOfWeek();

                        // Hitung lembur success minggu ini
                        $count = DB::table('overtime_employees')
                            ->where('user_id', $userId)
                            ->whereBetween('tanggal', [$startOfWeek, $endOfWeek])
                            ->where('status', 'success')
                            ->count();

                        // Jika sudah 3 → otomatis reject
                        if ($count >= 3) {

                            $record->status = 'failed';
                            $record->save();

                            Notification::make()
                                ->title('Lembur otomatis ditolak')
                                ->body('Karyawan ini sudah memiliki 3 lembur minggu ini.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->status = 'success';
                        $record->save();

                        Notification::make()
                            ->title("Lembur berhasil di-accept")
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
                            Auth::user()->hasRole('hrd-officer') &&
                            $record->status !== 'failed' && $record->status !== 'success'

                    )
                    ->action(function ($record) {
                        $record->status = 'failed';
                        $record->save();
                    }),
                Tables\Actions\EditAction::make()->visible(fn($record) =>
                Auth::check() &&
                    Auth::user()->hasRole('employee') &&
                    $record->status !== 'success' && $record->status !== 'failed'),

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
            'index' => Pages\ListOvertimeEmployees::route('/'),
            'create' => Pages\CreateOvertimeEmployee::route('/create'),
            'edit' => Pages\EditOvertimeEmployee::route('/{record}/edit'),
        ];
    }
}
