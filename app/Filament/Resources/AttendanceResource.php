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
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Filament\Forms\Components\Hidden;
use Illuminate\Database\Eloquent\Model;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    //untuk mengatur di bredcump
    protected static ?string $pluralModelLabel = 'Kehadiran';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // ? INI 
    // !! Penjelasan Fungsi, Query, Sintaks, dan Logika
    // * Penjelasan Keseluruhan
    // TODO

    // * Jadi dalam Attendance yang bisa Full Access CRUD hanya permission manage-roles-and-permissions, 
    // * dan yang bisa liat semua data adalah super admin dan hrd-officer,
    //  *sedangkan employee hanya bisa liat data dia sendiri.
    // untuk mengatur nama resource
    public static function getNavigationLabel(): string
    {
        if (! Auth::check()) {
            return 'Absent';
        }
        if (Auth::user()->hasAnyPermission(['manage-roles-and-permissions', 'view-employee-data', 'edit-employee-data'])) {
            return 'Manajemen Kehadiran';
        } elseif (Auth::user()->can('submit-attendance')) {
            return 'Absen Kehadiran';
        }
    }


    public static function canEdit(Model $record): bool
    {
        return Auth::check() && (Auth::user()->can([
            'manage-roles-and-permissions',

        ]));
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::check() && (Auth::user()->can(['manage-roles-and-permissions']));
    }

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
                            if (Auth::user()->can('manage-employee')) {
                                return $query->whereDoesntHave('roles', fn($q) => $q->where('name', 'super-admin'));
                            }

                            // Jika employee, hanya tampilkan dirinya sendiri
                            if (Auth::user()->can('submit-attendance')) {
                                return $query->where('id', Auth::id());
                            }

                            // Default: tampilkan semua
                            return $query;
                        }
                    )->required()
                    ->visible(fn() => !Auth::user()->hasRole('super admin')),
                DatePicker::make('tanggal')
                    ->required()
                    ->minDate(today())
                    ->maxDate(today()),

                TextInput::make('durasi_keterlambatan')->label('Durasi Keterlambatan (Gunakan Satuan Menit)'),
                Forms\Components\Hidden::make('jam_masuk')
                    ->default(
                        Carbon::now('Asia/Jakarta')->format('H:i:s')
                    )
                    ->dehydrated(true),
                Select::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin'  => 'Izin',
                        'sakit' => 'Sakit',
                        'telat' => 'Telat',
                        'alpha' => 'Tidak Hadir',
                    ])
                    ->label('Status Kehadiran')
                    ->required()
                    ->dehydrated(true),
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

            ])
            ->defaultSort('tanggal', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                // !! fungsi untuk filter apabila ada permission 
                // !! submit-attendance maka akan dijalankan query wherenya
                if ($user->can('submit-attendance')) {
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
