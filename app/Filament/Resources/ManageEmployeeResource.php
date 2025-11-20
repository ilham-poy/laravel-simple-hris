<?php

namespace App\Filament\Resources;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

use App\Filament\Resources\ManageEmployeeResource\Pages;
use App\Filament\Resources\ManageEmployeeResource\RelationManagers;
use App\Models\ManageEmployee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageEmployeeResource extends Resource
{
    protected static ?string $model = ManageEmployee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // !!! yang bisa liat manage employe adalah role hrd
    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && ($user->hasRole('hrd-officer') || $user->hasRole('super-admin')) && ($user->can('view-employee-data')
            ||  $user->can('manage-roles-and-permissions'));
    }

    public static function canCreate(): bool
    {
        return Auth::check() && Auth::user()->can('create-employee');
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::check() && Auth::user()->can('edit-employee-data');
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::check() && Auth::user()->can('delete-employee');
    }


    public static function afterUpdate(Model $record): void
    {
        ActivityLog::create([
            'action' => 'update',
            'model_type' => class_basename($record),
            'model_id' => $record->id,
            'performed_by' => auth()->id(),
        ]);
    }
    public static function afterDelete(Model $record): void
    {
        ActivityLog::create([
            'action' => 'delete',
            'model_type' => class_basename($record),
            'model_id' => $record->id,
            'performed_by' => auth()->id(),
        ]);
    }





    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_lengkap')->label('Nama Lengkap')->required(),
                Select::make('user_id')
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                        modifyQueryUsing: function ($query) {
                            $query->whereDoesntHave('roles', function ($q) {
                                $q->where('name', 'super-admin');
                            });

                            if (Auth::user()->hasRole('hrd-officer')) {
                                $query->whereNotIn('id', function ($sub) {
                                    $sub->select('user_id')->from('manage_employees');
                                });
                            }

                            return $query;
                        }
                    )
                    ->label('Nama Karyawan')
                    ->required()
                    ->reactive() // penting supaya bisa trigger perubahan
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $user = User::find($state);
                            if ($user) {
                                $set('email_kantor', $user->email); // isi otomatis field email_kantor
                            }
                        }
                    }),

                TextInput::make('email_kantor')
                    ->label('Email Kantor')
                    ->email()
                    ->required()
                    ->disabled()
                    ->dehydrated(true),
                TextInput::make('email_pribadi')->label('Email Pribadi')->email()->required(),
                TextInput::make('no_hp')->label('Nomor Hp')->required(),
                TextInput::make('no_keluarga_1')->label('Nomor Hp Keluarga 1')->required(),
                TextInput::make('no_keluarga_2')->label('Nomor Hp Keluarga 2'),
                Select::make('jenis_kelamin')
                    ->options([
                        'pria' => 'Pria',
                        'perempuan' => 'Perempuan',
                    ])->label('Jenis Kelamin')->required(),
                Textarea::make('alamat')->label('Alamat')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_lengkap')->label('Nama Lengkap'),
                TextColumn::make('email_kantor')->label('Email Kantor'),
                TextColumn::make('no_hp')->label('Nomor Hp'),
                TextColumn::make('jenis_kelamin')->label('Jenis Kelamin'),
                TextColumn::make('alamat')->label('Alamat'),
            ])
            ->filters([
                SelectFilter::make('jenis_kelamin')
                    ->options([
                        'pria' => 'Pria',
                        'perempuan' => 'Perempuan',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListManageEmployees::route('/'),
            'create' => Pages\CreateManageEmployee::route('/create'),
            'edit' => Pages\EditManageEmployee::route('/{record}/edit'),
        ];
    }
}
