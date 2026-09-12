<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Account;
use App\Models\User;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $pluralModelLabel = 'Pembuatan User';

    // Icon diganti jadi users agar lebih relevan dari rectangle-stack
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->disabled()
                    ->dehydrated(), // dehydrated(true) adalah default, cukup ditulis begini

                Select::make('account_name')
                    ->label('Account Name')
                    // Optimasi: Gunakan arrow function fn() agar query dieksekusi hanya saat form dibuka (Lazy Load)
                    ->options(fn() => Account::where('status', 'pending')->pluck('name', 'id'))
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if ($state && $account = Account::find($state)) {
                            $set('name', $account->name);
                        }
                    }),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->password()
                    ->required(fn(string $operation): bool => $operation === 'create')
                    // Optimasi: Gunakan Hash::make standar Laravel alih-alih helper bcrypt()
                    ->dehydrateStateUsing(fn(string $state) => Hash::make($state))
                    ->dehydrated(fn(?string $state) => filled($state)),

                Section::make('Hak Akses & Role')
                    ->schema([
                        Select::make('roles')
                            ->label('Pilih Role (Bisa pilih lebih dari satu)')
                            // Mengambil data role yang sudah ada di database
                            ->relationship('roles', 'name')
                            ->multiple() // Standar Spatie: 1 user bisa punya banyak role
                            ->preload()
                            ->searchable()
                            ->required()
                            // FITUR TOMBOL (+) UNTUK BIKIN ROLE BARU LANGSUNG DARI SINI
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama Role Baru')
                                    ->placeholder('Contoh: manager, supervisor')
                                    ->required()
                                    ->unique(table: 'roles', column: 'name'), // Cegah nama role kembar

                                CheckboxList::make('permissions')
                                    ->label('Pilih Hak Akses / Permission')
                                    ->relationship('permissions', 'name') // Otomatis sync permission ke role yang baru dibikin
                                    ->options(fn() => Permission::pluck('name', 'id'))
                                    ->columns(3)
                                    ->gridDirection('row')
                                    ->bulkToggleable(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Optimasi: Tambahkan searchable() agar user mudah dicari di tabel
                TextColumn::make('name')->label('Nama Lengkap')->searchable(),
                TextColumn::make('email')->label('Email Kantor')->searchable(),
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color('primary')
                    ->default('-'),
            ])
            ->headerActions([
                Action::make('manageAllRoles')
                    ->label('Kelola / Hapus Role')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color('danger')
                    ->modalWidth('4xl')
                    ->form([
                        Select::make('selected_role_id')
                            ->label('Pilih Role yang Ingin Di-Edit / Dihapus')
                            ->options(fn() => Role::pluck('name', 'id'))
                            ->live()
                            ->required()
                            ->afterStateUpdated(function ($state, Set $set) {
                                // Optimasi: Kode pencarian role lebih singkat pakai Ternary & Null-safe operator (?->)
                                $role = $state ? Role::find($state) : null;

                                $set('role_name', $role?->name);
                                $set('permissions', $role?->permissions->pluck('id')->toArray() ?? []);
                            }),

                        TextInput::make('role_name')
                            ->label('Nama Role')
                            ->required(),

                        CheckboxList::make('permissions')
                            ->label('Hak Akses / Permission Role')
                            ->options(fn() => Permission::pluck('name', 'id'))
                            ->columns(3)
                            ->gridDirection('row')
                            ->bulkToggleable(),

                        Actions::make([
                            FormAction::make('deleteRole')
                                ->label('Hapus Role Ini dari Sistem')
                                ->icon('heroicon-o-trash')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->visible(fn(Get $get) => filled($get('selected_role_id')))
                                ->action(function (Get $get) {
                                    $roleId = $get('selected_role_id');

                                    // Optimasi: Assign variable di dalam if condition agar lebih hemat baris
                                    if ($roleId && $role = Role::find($roleId)) {
                                        $role->users()->detach();
                                        $role->delete();

                                        Notification::make()
                                            ->title('Role berhasil dihapus!')
                                            ->success()
                                            ->send();

                                        // Optimasi: Pemanggilan route otomatis menggunakan static::getUrl
                                        return redirect(static::getUrl('index'));
                                    }
                                }),
                        ]),
                    ])
                    ->action(function (array $data): void {
                        // Optimasi: Logika update lebih ringkas
                        if ($role = Role::find($data['selected_role_id'] ?? null)) {
                            $role->update(['name' => $data['role_name']]);
                            $role->permissions()->sync($data['permissions'] ?? []);

                            Notification::make()
                                ->title('Role berhasil diperbarui!')
                                ->success()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->label('Hapus User'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
