<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManageResignResource\Pages;
use App\Filament\Resources\ManageResignResource\RelationManagers;
use App\Models\ManageResign;
use App\Models\Resign;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Override;

class ManageResignResource extends Resource
{
    protected static ?string $model = Resign::class;
    // protected static ?string $navigationLabel = 'Manajemen Resign';

    // protected static ?string $pluralModelLabel = 'Manajemen Resign';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getNavigationLabel(): string
    {
        $user = Auth::user();

        if ($user->hasAnyPermission(['employee:update', 'role:update'])) {
            return 'Manajemen Resign';
        }
        return 'Mengajukan Resign';
    }
    public static function getPluralLabel(): string
    {
        $user = Auth::user();

        // Pastikan user terautentikasi sebelum cek permission
        if ($user && $user->hasAnyPermission(['employee:update', 'role:update'])) {
            return 'Manajemen Resign';
        }

        return 'Mengajukan Resign';
    }
    // // untuk mengatur nama resource
    // public static function canViewAny(): bool
    // {
    //     return  Auth::user()->hasRole('hrd-officer', 'super-admin');
    // }


    // public static function canCreate(): bool
    // {
    //     return Auth::check() && Auth::user()->can('submit-leave');
    // }
    // public static function canEdit(Model $record): bool
    // {
    //     return Auth::check() && (Auth::user()->hasRole('employee'));
    // }

    // public static function canDelete(Model $record): bool
    // {
    //     return Auth::check() && Auth::user()->hasRole('hrd-officer');
    // }


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
                            // Jika employee, hanya tampilkan dirinya sendiri
                            if (Auth::user()->can('role:update')) {
                                return $query;
                            } elseif (Auth::user()->hasAnyPermission(['attendance:create'])) {
                                return $query->where('id', Auth::id());
                            }
                        }
                    ),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->disabled(fn() => !Auth::user()->hasAnyPermission(['role:update', 'employee:update']))
                    ->label('Status Resign')
                    ->required()
                    ->default('pending'),
                Textarea::make('description')->label('Keterangan')->required(),
                FileUpload::make('lampiran_surat')
                    ->label('Lampiran Surat')
                    ->directory('resign-attachments')
                    ->maxSize(10240) // 10MB
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->enableDownload()
                    ->enableOpen()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Pegawai'),
                TextColumn::make('status')->label('Status')->badge()->color(fn(string $state): string => match ($state) {
                    'approved' => 'success', // Hijau
                    'pending' => 'warning', // kuning
                    'rejected' => 'danger',  // Merah
                    // default => 'gray',
                }),
                TextColumn::make('description')->label('Keterangan'),
                TextColumn::make('lampiran_surat')
                    ->label('Lampiran Surat')
                    ->formatStateUsing(function ($state) {
                        if ($state) {
                            return '<a href="' . asset('storage/' . $state) . '" target="_blank">Lihat Lampiran</a>';
                        }
                        return 'Tidak ada lampiran';
                    })
                    ->html(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('approved')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(
                        fn($record) =>
                        Auth::check() &&
                            Auth::user()->hasRole('hrd-officer') &&
                            $record->status !== 'approved'
                    )
                    ->action(function (Resign $record) {
                        $record->status = 'approved';
                        $record->save();
                    }),
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
            'index' => Pages\ListManageResigns::route('/'),
            'create' => Pages\CreateManageResign::route('/create'),
            'edit' => Pages\EditManageResign::route('/{record}/edit'),
        ];
    }
}
