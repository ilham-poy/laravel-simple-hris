<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables;
use Filament\Forms\Components\FileUpload;
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
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

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
                            // Jika employee, hanya tampilkan dirinya sendiri
                            if (Auth::user()->hasRole('super-admin')) {
                                return $query;
                            } elseif (Auth::user()->hasAnyRole(['driver', 'warehouse-staff', 'hrd-officer', 'finance'])) {
                                return $query->where('id', Auth::id());
                            }
                        }
                    )->required(),
                // ->visible(fn() => !Auth::user()->hasRole('super-admin')),
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
                FileUpload::make('lampiran')
                    ->multiple()
                    ->label('Lampiran (Opsional)')
                    ->directory('attendance-attachments')
                    ->maxSize(5120) // 5MB
                    // ->visibility('private')
                    ->acceptedFileTypes(['image/*', 'application/pdf'])
                    ->helperText('Unggah file gambar, PDF Maksimal 5MB.')
                    ->dehydrated(fn($state) => filled($state))

                    // 2. Memastikan format data dari database (baik string biasa maupun JSON array) 
                    //    dapat dibaca dengan benar oleh komponen FileUpload
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return [];
                        }

                        // Jika data di DB masih berupa string JSON '["path/file.jpg"]'
                        if (is_string($state) && (str_starts_with($state, '[') || str_starts_with($state, '{'))) {
                            return json_decode($state, true) ?? [];
                        }

                        // Jika data berupa string tunggal 'path/file.jpg'
                        if (is_string($state)) {
                            return [$state];
                        }
                        return $state;
                    })
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
                TextColumn::make('status')->label('Status Kehadiran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'telat' => 'danger', // Kuning
                        'hadir' => 'success', // Hijau
                        'izin' => 'info', // Hijau
                        'sakit' => 'info', // Hijau
                        'alpha' => 'warning',  // Merah
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('keterangan')->label('Keterangan')->default('-'),
                TextColumn::make('lampiran')
                    ->label('Lampiran')
                    ->default('Lihat Lampiran')
                    ->icon('heroicon-o-paper-clip')
                    ->iconColor('primary')
                    ->color('primary')
                    ->alignLeft() // 👈 INI KUNCI UTAMA: Memaksa teks & ikon rata kiri lurus dengan header
                    ->formatStateUsing(fn($state) => empty($state) ? '-' : 'Lihat Lampiran')
                    ->action(
                        Tables\Actions\Action::make('preview_lampiran')
                            ->modalHeading('Daftar Lampiran Absensi')
                            ->infolist([
                                Section::make()->schema([
                                    TextEntry::make('lampiran')
                                        ->hiddenLabel()
                                        ->formatStateUsing(function ($record) {
                                            $raw = $record->lampiran;

                                            if (empty($raw)) {
                                                return 'Tidak ada lampiran';
                                            }

                                            // Handling format String maupun JSON Array
                                            if (is_string($raw) && (str_starts_with($raw, '[') || str_starts_with($raw, '{'))) {
                                                $files = json_decode($raw, true) ?? [];
                                            } elseif (is_array($raw)) {
                                                $files = $raw;
                                            } else {
                                                $files = [$raw];
                                            }

                                            $html = '<div style="display: flex; gap: 12px; flex-wrap: wrap;">';

                                            foreach ($files as $filePath) {
                                                $cleanPath = str_replace('//', '/', $filePath);
                                                $url = Storage::url($cleanPath);
                                                $isPdf = str_ends_with(strtolower($cleanPath), '.pdf');

                                                if ($isPdf) {
                                                    $html .= "
                                        <a href='{$url}' target='_blank' style='display: flex; align-items: center; justify-content: center; width: 120px; height: 120px; border: 1px solid #374151; border-radius: 8px; background: #1f2937; color: #ef4444; font-weight: bold; text-decoration: none;'>
                                            📄 Buka PDF
                                        </a>
                                    ";
                                                } else {
                                                    $html .= "
                                        <a href='{$url}' target='_blank'>
                                            <img src='{$url}' style='width: 120px; height: 120px; object-fit: cover; border-radius: 8px; border: 1px solid #374151;' />
                                        </a>
                                    ";
                                                }
                                            }

                                            $html .= '</div>';

                                            return new HtmlString($html);
                                        }),
                                ]),
                            ])
                    )
                    ->label('Lampiran')
                    ->action(
                        Tables\Actions\Action::make('preview_lampiran')
                            ->label('Lampiran')
                            ->icon('heroicon-o-paper-clip')
                            ->modalHeading('Daftar Lampiran Absensi')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Tutup')
                            ->infolist([
                                Section::make()->schema([
                                    TextEntry::make('lampiran')
                                        ->hiddenLabel()
                                        ->formatStateUsing(function ($record) {
                                            $raw = $record->lampiran;

                                            if (empty($raw)) {
                                                return 'Tidak ada lampiran';
                                            }

                                            // Handling format String maupun JSON Array
                                            if (is_string($raw) && (str_starts_with($raw, '[') || str_starts_with($raw, '{'))) {
                                                $files = json_decode($raw, true) ?? [];
                                            } elseif (is_array($raw)) {
                                                $files = $raw;
                                            } else {
                                                $files = [$raw];
                                            }

                                            $html = '<div style="display: flex; gap: 12px; flex-wrap: wrap;">';

                                            foreach ($files as $filePath) {
                                                $cleanPath = str_replace('//', '/', $filePath);
                                                $url = Storage::url($cleanPath);
                                                $isPdf = str_ends_with(strtolower($cleanPath), '.pdf');

                                                if ($isPdf) {
                                                    $html .= "
                                        <a href='{$url}' target='_blank' style='display: flex; align-items: center; justify-content: center; width: 120px; height: 120px; border: 1px solid #374151; border-radius: 8px; background: #1f2937; color: #ef4444; font-weight: bold; text-decoration: none;'>
                                            📄 Buka PDF
                                        </a>
                                    ";
                                                } else {
                                                    $html .= "
                                        <a href='{$url}' target='_blank'>
                                            <img src='{$url}' style='width: 120px; height: 120px; object-fit: cover; border-radius: 8px; border: 1px solid #374151;' />
                                        </a>
                                    ";
                                                }
                                            }

                                            $html .= '</div>';

                                            return new HtmlString($html);
                                        }),
                                ]),
                            ])
                    )
            ])
            ->defaultSort('tanggal', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                // Jika Super Admin, tampilkan SEMUA data (tanpa filter)
                if ($user->hasAnyPermission(['manage-roles-and-permissions', 'view-employee-data'])) {
                    return $query;
                }

                // Jika BUKAN Super Admin, filter data (misal: hanya data milik user sendiri)
                return $query->where('user_id', $user->id);
            })
            ->filters([
                //
                // 'hadir', 'izin', 'sakit', 'telat', 'alpha'
                SelectFilter::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'telat' => 'Telat',
                        'alpha' => 'Tidak Hadir',
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
