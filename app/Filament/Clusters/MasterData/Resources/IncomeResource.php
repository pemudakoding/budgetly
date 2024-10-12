<?php

namespace App\Filament\Clusters\MasterData\Resources;

use App\Filament\Clusters\MasterData;
use App\Models\Builders\AccountBuilder;
use App\Models\Builders\IncomeBuilder;
use App\Models\Income;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
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
                Select::make('account_id')
                    ->required()
                    ->label('Account')
                    ->relationship(
                        'account',
                        'name',
                        modifyQueryUsing: fn (AccountBuilder $query): AccountBuilder => $query->whereOwnedBy(auth()->user())
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (IncomeBuilder $query): IncomeBuilder => $query->whereOwnedBy(auth()->user()))
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('account.name')
                    ->searchable()
                    ->badge()
                    ->color(fn (Income $record) => Color::hex($record->account->legend)),
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
