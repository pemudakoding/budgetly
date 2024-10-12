<?php

namespace App\Filament\Clusters\MasterData\Resources;

use App\Enums\NavigationGroup;
use App\Filament\Clusters\MasterData;
use App\Filament\Resources\MasterData\ExpenseResource\Pages;
use App\Models\Builders\ExpenseBuilder;
use App\Models\Expense;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $cluster = MasterData::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->autocomplete(false)
                    ->required()
                    ->string(),
                Forms\Components\Select::make('expense_category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->exists(\App\Models\ExpenseCategory::class, column: 'id'),
            ])
            ->columns(1);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (ExpenseBuilder $query): ExpenseBuilder => $query->whereOwnedBy(auth()->user()))
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('category.name')
                    ->badge()
                    ->color(fn (Expense $record): string => $record->enumerateCategory->resolveColor())
                    ->icon(fn (Expense $record): string => $record->enumerateCategory->resolveIcon()),
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
            ])
            ->groups([
                Tables\Grouping\Group::make('category.name')
                    ->getTitleFromRecordUsing(fn (Expense $record): string => $record->enumerateCategory->value),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Clusters\MasterData\Resources\ExpenseResource\Pages\ManageExpenses::route('/'),
        ];
    }
}
