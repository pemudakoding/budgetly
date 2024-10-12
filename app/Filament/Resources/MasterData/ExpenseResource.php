<?php

namespace App\Filament\Resources\MasterData;

use App\Enums\ExpenseCategory;
use App\Enums\NavigationGroup;
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

    protected static ?string $navigationGroup = NavigationGroup::MasterData->value;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->autocomplete(false)
                    ->required()
                    ->string(),
                Forms\Components\Select::make('category')
                    ->options(ExpenseCategory::toArray())
                    ->required()
                    ->in(array_keys(ExpenseCategory::toArray())),
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
                TextColumn::make('category')
                    ->badge()
                    ->color(fn (Expense $record): string => $record->category->resolveColor())
                    ->icon(fn (Expense $record): string => $record->category->resolveIcon()),
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
                Tables\Grouping\Group::make('category')
                    ->getTitleFromRecordUsing(fn (Expense $record): string => $record->category->value),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageExpenses::route('/'),
        ];
    }
}
