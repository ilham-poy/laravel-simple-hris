<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResignResource\Pages;
use App\Filament\Resources\ResignResource\RelationManagers;
use App\Models\Resign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;

class ResignResource extends Resource
{
    protected static ?string $model = Resign::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // public static function canViewAny(): bool
    // {
    //     return Auth::user()->hasRole('employee');
    // }
    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()->hasRole('employee');
    }

    public static function canCreate(): bool
    {
        return Auth::check() && Auth::user()->can('submit-leave');
    }
    public static function canEdit(Model $record): bool
    {
        return Auth::check() && (Auth::user()->hasRole('hrd-officer') || Auth::user()->hasRole('employee'));
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
                        modifyQueryUsing: fn($query) => $query->where('id', Auth::id())
                    ),
                Textarea::make('description')->label('Keterangan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('user.name')
                    ->label('Nama'),
                TextColumn::make('status')->label('Status'),
                TextColumn::make('description')->label('Keterangan'),
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
                Tables\Actions\EditAction::make()->visible(fn($record) => $record->status !== 'success'),
                Tables\Actions\DeleteAction::make()->visible(fn($record) => $record->status !== 'success')
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
            'index' => Pages\ListResigns::route('/'),
            'create' => Pages\CreateResign::route('/create'),
            'edit' => Pages\EditResign::route('/{record}/edit'),
        ];
    }
}
