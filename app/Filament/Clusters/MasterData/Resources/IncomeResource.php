<?php

namespace App\Filament\Clusters\MasterData\Resources;

use App\Enums\NavigationGroup;
use App\Filament\Clusters\MasterData;
use App\Filament\Resources\MasterData\IncomeResource\Pages;
use App\Models\Builders\IncomeBuilder;
use App\Models\Income;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $cluster = MasterData::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (IncomeBuilder $query): IncomeBuilder => $query->whereOwnedBy(auth()->user()))
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Clusters\MasterData\Resources\IncomeResource\Pages\ManageIncomes::route('/'),
        ];
    }
}
