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
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeFinanceResource extends Resource
{
    protected static ?string $model = EmployeeFinance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('user_id')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListEmployeeFinances::route('/'),
            'create' => Pages\CreateEmployeeFinance::route('/create'),
            'edit' => Pages\EditEmployeeFinance::route('/{record}/edit'),
        ];
    }
}
