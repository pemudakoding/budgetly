<?php

namespace App\Filament\Resources\Budgeting\IncomeResource\RelationManagers;

use App\Enums\Month;
use App\Filament\Forms\MoneyInput;
use App\Models\IncomeBudget;
use Carbon\Carbon;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class BudgetsRelationManager extends RelationManager
{
    protected static string $relationship = 'budgets';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('')
                    ->content('The income that already settled for a month would be updated instead of creating one')
                    ->columnSpan(2),
                MoneyInput::make('amount')
                    ->required(),
                Select::make('month')
                    ->options(Month::toArray())
                    ->required()
                    ->unique(
                        IncomeBudget::class,
                        'month',
                        ignorable: fn (?IncomeBudget $record): ?IncomeBudget => $record?->created_at->year === Carbon::now()->year
                            ? $record
                            : null,
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule): Unique => $rule->where(
                            fn (\Illuminate\Database\Query\Builder $query) => $query
                                ->whereYear('created_at', Carbon::now()->year)
                                ->whereIn('income_id', auth()->user()->incomes->pluck('id'))
                        )
                    ),
            ]);
    }

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->money(),
                Tables\Columns\TextColumn::make('month'),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Start Date')
                            ->default(Carbon::now()->startOfYear()),
                        Forms\Components\DatePicker::make('to')
                            ->label('End Date')
                            ->default(Carbon::now()->endOfYear()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'From '.Carbon::parse($data['from'])->monthName.' '.Carbon::parse($data['to'])->year;
                        }
                        if ($data['to'] ?? null) {
                            $indicators['to'] = 'To '.Carbon::parse($data['to'])->monthName.' '.Carbon::parse($data['to'])->year;
                        }

                        return $indicators;
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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

    public function isReadOnly(): bool
    {
        return false;
    }
}
