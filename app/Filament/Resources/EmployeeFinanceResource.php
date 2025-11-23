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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use App\Models\OvertimeEmployee;
use Filament\Tables\Columns\TextColumn;

class EmployeeFinanceResource extends Resource
{
    protected static ?string $model = EmployeeFinance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

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
                                    $sub->select('user_id')->from('employee_finances');
                                });
                            }

                            return $query;
                        }
                    )
                    ->label('Nama Karyawan')
                    ->reactive()
                    //$state merupakan hasil atau data dari inputan, dalam kasus ini maka nilai state id dari user
                    ->afterStateUpdated(function ($state, callable $set) {
                        $jamLembur = OvertimeEmployee::where('user_id', $state)->sum('total_lembur');
                        $set('jam_lembur', $jamLembur);
                    }),
                TextInput::make('gaji_pokok')->label('Gaji Pokok *tidak usah menggunankan titik')->required(),
                TextInput::make('gaji_lembur')->label('Gaji Lembur *tidak usah menggunankan titik')->required(),
                TextInput::make('jam_lembur')
                    ->numeric()
                    ->label('Banyak Jam Lembur')
                    ->disabled()
                    ->dehydrated(true),
                TextInput::make('tidak_masuk')->label('Pengurangan Gaji Tidak Masuk')->required(),
                Select::make('status_pegawai')
                    ->options([
                        'magang' => 'Magang',
                        'contract' => 'Contract',
                    ])->label('Status Pegawai')->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('user.name')
                    ->label('Nama Pegawai'),
                TextColumn::make('gaji_pokok')->label('Gaji Pokok'),
                TextColumn::make('gaji_lembur')->label('Gaji Lembur'),
                TextColumn::make('jam_lembur')->label('Banyak Jam Lembur'),
                TextColumn::make('total_gaji')->label('Total Gaji'),
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
