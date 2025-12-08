<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManageResignResource\Pages;
use App\Filament\Resources\ManageResignResource\RelationManagers;
use App\Models\ManageResign;
use App\Models\Resign;
use Filament\Forms;
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

class ManageResignResource extends Resource
{
    protected static ?string $model = Resign::class;
    protected static ?string $navigationLabel = 'Manage Resign';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getNavigationLabel(): string
    {
        $user = Auth::user();

        if ($user->hasRole('hrd-officer') || $user->hasRole('super-admin')) {
            return "Manajemen Resign";
        }
        return 0;
    }
    // untuk mengatur nama resource
    public static function canViewAny(): bool
    {
        return  Auth::user()->hasRole('hrd-officer');
    }


    public static function canCreate(): bool
    {
        return Auth::check() && Auth::user()->can('submit-leave');
    }
    public static function canEdit(Model $record): bool
    {
        return Auth::check() && (Auth::user()->hasRole('hrd-officer') || Auth::user()->hasRole('employee'));
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::check() && Auth::user()->hasRole('hrd-officer');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('user_id')
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                    ),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'failed' => 'Failed',
                    ])->label('Status Kehadiran')->required(),
                Textarea::make('description')->label('Keterangan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Pegawai'),
                TextColumn::make('status')->label('Status'),
                TextColumn::make('description')->label('Keterangan'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('accept')
                    ->label('Accept')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(
                        fn($record) =>
                        Auth::check() &&
                            Auth::user()->hasRole('hrd-officer') &&
                            $record->status !== 'success'
                    )
                    ->action(function (Resign $record) {
                        $record->status = 'success';
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
