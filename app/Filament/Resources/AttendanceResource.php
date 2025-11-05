<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                //

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
                    )
                    ->visible(fn() => !Auth::user()->hasRole('super admin')),

                DatePicker::make('tanggal'),
                TimePicker::make('jam_masuk')->label('Jam Masuk'),

                TextInput::make('durasi_keterlambatan')->label('Durasi Keterlambatan (Gunakan Satuan Menit)'),
                Select::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'telat' => 'Telat',
                        'alpha' => 'Tidak Hadir',
                    ])->label('Status Kehadiran')->required(),
                Textarea::make('keterangan')->label('Keterangan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('user.name')
                    ->label('Nama Karyawan')
                    ->searchable(),
                TextColumn::make('tanggal')->label('Tanggal Kehadiran'),
                TextColumn::make('jam_masuk')->label('Jam Masuk'),
                TextColumn::make('status')->label('Status Kehadiran'),
                TextColumn::make('keterangan')->label('Keterangan')->default('-'),

            ])->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                if ($user->hasRole('employee')) {
                    $query->where('user_id', $user->id);
                }
            })
            ->filters([
                //
                SelectFilter::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(
                        fn($record): bool => Auth::user()->hasRole('hrd-officer')
                    ),
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
