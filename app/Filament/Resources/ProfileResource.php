<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfileResource\Pages;
use App\Filament\Resources\ProfileResource\RelationManagers;
use App\Models\ManageEmployee;
use App\Models\Profile;
use Filament\Forms;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;

class ProfileResource extends Resource
{
    protected static ?string $model = ManageEmployee::class;
    protected static ?string $navigationLabel = 'Profile';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return 'Profile';
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getPluralModelLabel(): string
    {
        return 'Profiles';
    }
    public static function canCreate(): bool
    {
        return Auth::check() && Auth::user()->can('create-employee');
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::check() && Auth::user()->can('edit-employee-data');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('user.name')
                    ->label('Nama Pegawai')
                    ->sortable()
                    ->wrap(),

                TextColumn::make('email_kantor')
                    ->label('Email Kantor')
                    ->wrap(),

                TextColumn::make('email_pribadi')
                    ->label('Email Pribadi')
                    ->wrap(),

                TextColumn::make('no_hp')
                    ->label('Nomor Hp')
                    ->wrap()
                    ->copyable(),

                TextColumn::make('no_keluarga_1')
                    ->label('Nomor Keluarga 1')
                    ->wrap()
                    ->copyable(),

                TextColumn::make('no_keluarga_2')
                    ->label('Nomor Keluarga 2')
                    ->wrap()
                    ->copyable(),

                TextColumn::make('jenis_kelamin')
                    ->label('Gender')
                    ->badge(),

                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->limit(50) // biar tidak terlalu panjang
                    ->tooltip(fn($state) => $state)
                    ->wrap(),

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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListProfiles::route('/'),
            'create' => Pages\CreateProfile::route('/create'),
            'edit' => Pages\EditProfile::route('/{record}/edit'),
        ];
    }
}
