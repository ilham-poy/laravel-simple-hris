<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeScheduleResource\Pages;
use Filament\Tables\Columns\BadgeColumn;
use Carbon\Carbon;
use App\Models\EmployeeSchedule;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Get;
use Filament\Forms\Set;

class EmployeeScheduleResource extends Resource
{
    protected static ?string $model = EmployeeSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $pluralModelLabel = 'Manajemen Jadwal Karyawan';

    public static function getNavigationLabel(): string
    {
        return "Manajemen Jadwal Karyawan";
    }

    public static function canCreate(): bool
    {
        return Auth::check() && Auth::user()->can('employee:create');
    }

    public static function canEdit(Model $record): bool
    {
        $shiftDate = \Carbon\Carbon::parse($record->tanggal)->startOfDay();
        $hMinusOne = \Carbon\Carbon::now()->addDay()->startOfDay();
        // kalo statusnya sudah approved, maka tidak bisa diubah lagi oleh karyawan
        if (($record->status === 'approved' || $record->status === 'rejected') && !Auth::user()->can('employee:update')) {
            return false;
        }
        if (Auth::user()->can('employee:update')) {
            // HRD tetap TIDAK BISA edit jika status sudah Approved DAN sudah memasuki H-1/lewat
            if (($record->status === 'approved' || $record->status === 'rejected') && $shiftDate <= $hMinusOne) {
                return false;
            }

            return true;
        }
        return Auth::check() && Auth::user()->can('employee:read');
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::check() && Auth::user()->can('employee:read');
    }

    public static function form(Form $form): Form
    {
        $isHrdOrAdmin = Auth::user()?->hasAnyRole(['super-admin', 'hrd-officer']);

        return $form
            ->schema([
                Section::make('Informasi Karyawan & Shift')
                    ->description($isHrdOrAdmin ? 'Pengaturan Shift Kerja Karyawan' : 'Shift kerja telah ditentukan oleh HRD')
                    ->schema([
                        // 1. Pilih Karyawan
                        Select::make('user_id')
                            ->label('Karyawan')
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name',
                                modifyQueryUsing: function ($query) {
                                    if (Auth::user()?->hasRole('hrd-officer')) {
                                        return $query->whereDoesntHave('roles', function ($q) {
                                            $q->where('name', 'super-admin');
                                        });
                                    }

                                    if (Auth::user()?->hasAnyRole(['driver', 'warehouse-staff'])) {
                                        return $query->where('id', Auth::id());
                                    }

                                    return $query;
                                }
                            )
                            ->default(Auth::id())
                            ->disabled(!$isHrdOrAdmin) // Hanya HRD/Admin yang bisa memilih karyawan lain
                            ->dehydrated()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) use ($isHrdOrAdmin) {
                                // Jika yang login Karyawan dan mengubah tanggal/user, cari shift yang sudah diset HRD
                                if (!$isHrdOrAdmin && $get('tanggal') && $state) {
                                    static::autoFillShiftData($set, $state, $get('tanggal'));
                                }
                            }),

                        // 2. Tanggal Shift / Lembur
                        DatePicker::make('tanggal')
                            ->label('Tanggal Shift / Lembur')
                            ->required()
                            ->default(now())
                            ->afterOrEqual(now()->startOfWeek())
                            ->beforeOrEqual(now()->addDays(14))
                            ->disabled(!$isHrdOrAdmin && fn($operation) => $operation === 'edit')
                            ->dehydrated()
                            ->unique(
                                table: 'employee_schedules',
                                column: 'tanggal',
                                ignorable: fn($record) => $record,
                                modifyRuleUsing: function ($rule, Get $get) {
                                    return $rule->where('user_id', $get('user_id'));
                                }
                            )
                            ->validationMessages([
                                'unique' => 'Jadwal karyawan pada tanggal ini sudah ada dalam sistem.',
                            ]),
                        // 3. Jenis Shift (Hanya HRD yang bisa ubah)
                        Select::make('shift_type')
                            ->label('Jenis Shift')
                            ->options([
                                'pagi'  => 'Shift Pagi (08:00 - 16:00)',
                                'siang' => 'Shift Siang (16:00 - 00:00)',
                                'malam' => 'Shift Malam (00:00 - 08:00)',
                                'off'   => 'Libur / OFF',
                            ])
                            ->default('pagi')
                            ->required()
                            ->disabled(!$isHrdOrAdmin) // Dikunci jika yang buka Karyawan
                            ->dehydrated()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                match ($state) {
                                    'pagi'  => ($set('jam_masuk', '08:00:00') && $set('jam_keluar', '16:00:00')),
                                    'siang' => ($set('jam_masuk', '16:00:00') && $set('jam_keluar', '00:00:00')),
                                    'malam' => ($set('jam_masuk', '00:00:00') && $set('jam_keluar', '08:00:00')),
                                    'off'   => ($set('jam_masuk', null) && $set('jam_keluar', null)),
                                    default => null,
                                };
                            }),

                        // 4. Jam Operasional Shift (Hanya HRD yang bisa ubah)
                        Grid::make(2)->schema([
                            TimePicker::make('jam_masuk')
                                ->label('Jam Masuk')
                                ->default('08:00:00')
                                ->disabled(!$isHrdOrAdmin)
                                ->dehydrated()
                                ->required(fn(Get $get) => $get('shift_type') !== 'off'),

                            TimePicker::make('jam_keluar')
                                ->label('Jam Keluar')
                                ->default('16:00:00')
                                ->disabled(!$isHrdOrAdmin)
                                ->dehydrated()
                                ->required(fn(Get $get) => $get('shift_type') !== 'off'),
                        ]),
                    ])->columns(2),

                Section::make('Pengajuan & Detail Lembur (Overtime)')
                    ->description('Bisa diisi oleh Karyawan untuk pengajuan lembur')
                    ->schema([
                        // 5. Durasi Lembur (Karyawan & HRD Bebas Mengisi)
                        Select::make('total_lembur')
                            ->label('Durasi Lembur (Jam)')
                            ->options([
                                '0.00' => 'Tidak Lembur (0 Jam)',
                                '1.00' => '1 Jam',
                                '1.50' => '1.5 Jam',
                                '2.00' => '2 Jam',
                                '2.50' => '2.5 Jam',
                                '3.00' => '3 Jam',
                                '4.00' => '4 Jam',
                            ])
                            ->default('0.00')
                            ->required(),

                        // 6. Status Approval (Hanya HRD / Admin yang bisa ubah)
                        Select::make('status')
                            ->label('Status Approval')
                            ->options([
                                'pending'  => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default($isHrdOrAdmin ? 'pending' : 'pending')
                            ->disabled(!$isHrdOrAdmin) // Karyawan mengajukan selalu berstatus Pending
                            ->dehydrated()
                            ->required(),

                        // 7. Keterangan Lembur (Karyawan & HRD Bebas Mengisi)
                        Textarea::make('keterangan_lembur')
                            ->label('Alasan / Keterangan Lembur')
                            ->placeholder('Contoh: Bongkar muat barang masuk gudang')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    /**
     * Helper untuk mengambil data shift yang sudah ditentukan HRD jika Karyawan membuat pengajuan Overtime.
     */
    protected static function autoFillShiftData(Set $set, $userId, $tanggal): void
    {
        $existingSchedule = EmployeeSchedule::where('user_id', $userId)
            ->where('tanggal', $tanggal)
            ->first();

        if ($existingSchedule) {
            $set('shift_type', $existingSchedule->shift_type);
            $set('jam_masuk', $existingSchedule->jam_masuk);
            $set('jam_keluar', $existingSchedule->jam_keluar);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                BadgeColumn::make('shift_type')
                    ->label('Shift')
                    ->colors([
                        'success'   => 'pagi',
                        'warning'   => 'siang',
                        'danger'    => 'malam',
                        'secondary' => 'off',
                    ])
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                TextColumn::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->time('H:i')
                    ->placeholder('-'),

                TextColumn::make('jam_keluar')
                    ->label('Jam Keluar')
                    ->time('H:i')
                    ->placeholder('-'),

                TextColumn::make('total_lembur')
                    ->label('Lembur (Jam)')
                    ->suffix(' Jam')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status Approval')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),
            ])
            ->defaultSort('tanggal', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();

                if ($user->can('employee:update')) {
                    return $query;
                }

                return $query->where('user_id', $user->id);
            })
            ->filters([
                Tables\Filters\SelectFilter::make('shift_type')
                    ->options([
                        'pagi'  => 'Pagi',
                        'siang' => 'Siang',
                        'malam' => 'Malam',
                        'off'   => 'OFF',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                // Action Accept/Approve Lembur oleh HRD
                Action::make('accept')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->visible(
                        fn($record) =>
                        Auth::check() &&
                            Auth::user()->hasAnyRole(['super-admin', 'hrd-officer']) &&
                            $record->status === 'pending'
                    )
                    ->action(function (EmployeeSchedule $record) {
                        $startOfWeek = Carbon::parse($record->tanggal)->startOfWeek();
                        $endOfWeek   = Carbon::parse($record->tanggal)->endOfWeek();

                        $count = EmployeeSchedule::where('user_id', $record->user_id)
                            ->whereBetween('tanggal', [$startOfWeek, $endOfWeek])
                            ->where('status', 'approved')
                            ->where('total_lembur', '>', 0)
                            ->where('id', '!=', $record->id)
                            ->count();

                        if ($count >= 3) {
                            $record->update(['status' => 'rejected']);

                            Notification::make()
                                ->title('Lembur Otomatis Ditolak')
                                ->body('Karyawan ini sudah mencapai batas maksimal 3x lembur dalam seminggu.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update(['status' => 'approved']);

                        Notification::make()
                            ->title('Lembur Berhasil Disetujui')
                            ->success()
                            ->send();
                    }),

                // Action Reject Lembur oleh HRD
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->visible(
                        fn($record) =>
                        Auth::check() &&
                            Auth::user()->hasAnyRole(['super-admin', 'hrd-officer']) &&
                            $record->status === 'pending' &&
                            $record->total_lembur > 0
                    )
                    ->action(function (EmployeeSchedule $record) {
                        $record->update(['status' => 'rejected']);

                        Notification::make()
                            ->title('Lembur Berhasil Ditolak')
                            ->warning()
                            ->send();
                    }),
                Tables\Actions\EditAction::make()

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()->hasAnyRole(['super-admin', 'hrd-officer'])),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmployeeSchedules::route('/'),
            'create' => Pages\CreateEmployeeSchedule::route('/create'),
            'edit'   => Pages\EditEmployeeSchedule::route('/{record}/edit'),
        ];
    }
}
