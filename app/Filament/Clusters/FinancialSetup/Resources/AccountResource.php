<?php

namespace App\Filament\Clusters\FinancialSetup\Resources;

use App\Filament\Clusters\FinancialSetup;
use App\Models\Account;
use App\Models\Builders\AccountBuilder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $cluster = FinancialSetup::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\ColorPicker::make('legend')
                    ->required()
                    ->default(sprintf('#%06x', mt_rand(0, 0xFFFFFF)))
                    ->regex('/^#([a-f0-9]{6}|[a-f0-9]{3})\b$/'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (AccountBuilder $query): AccountBuilder => $query->whereOwnedBy(auth()->user()))
            ->columns([
                Tables\Columns\ColorColumn::make('legend')
                    ->width('20px'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->date(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->date(),
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
            'index' => \App\Filament\Clusters\FinancialSetup\Resources\AccountResource\Pages\ManageAccounts::route('/'),
        ];
    }
}
