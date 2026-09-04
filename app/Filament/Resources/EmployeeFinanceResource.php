<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeFinanceResource\Pages;
use App\Filament\Resources\EmployeeFinanceResource\RelationManagers;
use App\Models\EmployeeFinance;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\EmployeeSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use App\Models\OvertimeEmployee;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;

class EmployeeFinanceResource extends Resource
{
    protected static ?string $model = EmployeeFinance::class;
    // Untuk Mengatur Nama Resource
    protected static ?string $modelLabel = 'Gaji Karyawan';
    protected static ?string $pluralModelLabel = 'Gaji Karyawan';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function calculateTotalSalary(callable $set, callable $get): void
    {
        $gajiPokok  = (int) $get('gaji_pokok');
        $jamLembur  = (int) $get('jam_lembur');
        $gajiLembur = (int) $get('gaji_lembur');
        $potongan   = (int) $get('tidak_masuk');

        // Rumus: (Gaji Pokok + (Jam Lembur * Rate Lembur)) - Potongan
        // Jika 'gaji_lembur' sudah total nominal (bukan rate/jam), hapus perkalian ($jamLembur *)
        $total = ($gajiPokok + ($jamLembur * $gajiLembur)) - $potongan;

        $set('total_gaji', max(0, $total));
    }
    // public static function getNavigationLabel(): string
    // {
    //     return 'Gaji Karyawan';
    // }
    // public static function canCreate(): bool
    // {
    //     return Auth::check() && Auth::user()->hasRole('hrd-officer');
    // }


    // public static function canEdit(Model $record): bool
    // {
    //     return Auth::check() && (Auth::user()->hasRole('hrd-officer'));
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
                        modifyQueryUsing: fn($query) => $query->whereDoesntHave('roles', function ($q) {
                            $q->where('name', 'super-admin');
                        })
                    )
                    ->label('Nama Karyawan')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $workMonth = $get('work_month');
                        if ($state && $workMonth) {
                            $date = Carbon::parse($workMonth);
                            $jamLembur = EmployeeSchedule::where('user_id', $state)
                                ->whereYear('tanggal', $date->year)
                                ->whereMonth('tanggal', $date->month)
                                ->sum('total_lembur');

                            // Tambahkan (int) agar pasti bulat
                            $set('jam_lembur', (int) $jamLembur);
                        }
                    }),

                DatePicker::make('work_month')
                    ->label('Bulan Kerja')
                    ->required()
                    ->live()
                    ->native(false)
                    ->displayFormat('F Y')
                    ->format('Y-m-d')
                    ->closeOnDateSelection()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if ($state) {
                            $salary = Carbon::parse($state)->addMonth()->startOfMonth();
                            $set('salary_month', $salary->format('Y-m-d'));

                            $userId = $get('user_id');
                            if ($userId) {
                                $date = Carbon::parse($state);
                                $jamLembur = EmployeeSchedule::where('user_id', $userId)
                                    ->whereYear('tanggal', $date->year)
                                    ->whereMonth('tanggal', $date->month)
                                    ->sum('total_lembur');

                                // Tambahkan (int) agar pasti bulat
                                $set('jam_lembur', (int) $jamLembur);
                            }
                        }
                    }),

                DatePicker::make('salary_month')
                    ->label('Bulan Gajian')
                    ->disabled()
                    ->native(false)
                    ->displayFormat('F Y')
                    ->format('Y-m-d')
                    ->dehydrated(true)
                    ->required(),

                TextInput::make('jam_lembur')
                    ->numeric()
                    ->label('Banyak Jam Lembur')
                    ->disabled()
                    ->default(0)
                    ->dehydrated(true),

                TextInput::make('gaji_pokok')
                    ->label('Gaji Pokok')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(callable $set, callable $get) => self::calculateTotalSalary($set, $get)),

                TextInput::make('gaji_lembur')
                    ->label('Gaji Lembur (Per Jam / Total)')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(callable $set, callable $get) => self::calculateTotalSalary($set, $get)),

                TextInput::make('tidak_masuk')
                    ->label('Pengurangan Gaji Tidak Masuk')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(callable $set, callable $get) => self::calculateTotalSalary($set, $get)),

                // Field total_gaji yang sebelumnya hilang
                TextInput::make('total_gaji')
                    ->label('Total Gaji Diterima')
                    ->numeric()
                    ->prefix('Rp')
                    ->readOnly()
                    ->dehydrated(true)
                    ->required(),

                Select::make('status_pegawai')
                    ->options([
                        'magang'   => 'Magang',
                        'contract' => 'Contract',
                        'fulltime' => 'Full Time',
                    ])
                    ->default('fulltime')
                    ->label('Status Pegawai')
                    ->required(),

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('user.name')
                    ->label('Nama Pegawai'),
                TextColumn::make('gaji_pokok')->label('Gaji Pokok'),
                TextColumn::make('gaji_lembur')->label('Gaji Lembur'),
                TextColumn::make('jam_lembur')->label('Banyak Jam Lembur'),
                TextColumn::make('total_gaji')->label('Total Gaji'),
                TextColumn::make('work_month')->label('Bulan Kerja'),
                TextColumn::make('salary_month')->label('Bulan Gajian'),
            ])->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                if ($user->hasRole('employee')) {
                    $query->where('user_id', $user->id);
                }
            })
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(
                        fn($record): bool => Auth::user()->hasRole('hrd-officer')
                    )
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
            'index' => Pages\ListEmployeeFinances::route('/'),
            'create' => Pages\CreateEmployeeFinance::route('/create'),
            'edit' => Pages\EditEmployeeFinance::route('/{record}/edit'),
        ];
    }
}
