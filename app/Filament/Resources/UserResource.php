<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Account;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function canViewAny(): bool
    {
        return  Auth::user()->hasRole('super-admin');
    }
    public static function canCreate(): bool
    {
        return Auth::check() && Auth::user()->can('manage-roles-and-permissions');
    }
    public static function canEdit(Model $record): bool
    {
        return Auth::check() &&  Auth::user()->hasRole('super-admin');
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::check() && Auth::user()->hasRole('super-admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('name')->required()->disabled()->dehydrated(true),
                Select::make('account_name')
                    ->label('Account Name')
                    ->options(
                        Account::where('status', 'pending')
                            ->pluck('name', 'id')
                    )
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $account = Account::find($state);
                            if ($account) {
                                $set('name', $account->name);
                            }
                        }
                    }),
                TextInput::make('email')->email()->required()->unique(),
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->dehydrateStateUsing(fn($state) => bcrypt($state)),
                Select::make('roles')
                    ->label('Roles')
                    ->multiple()
                    ->options(Role::pluck('name', 'name')->toArray())
                    ->required()
                    ->default(fn($record) => $record?->roles->pluck('name')->toArray())

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Lengkap'),
                TextColumn::make('email')->label('Email Kantor'),
                TextColumn::make('roles')
                    ->label('Roles')
                    ->getStateUsing(fn($record) => $record->roles->pluck('name')->join(', '))
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
