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
                        Select::make('from')
                            ->default(Carbon::now()->year)
                            ->options(function () {
                                $currentYear = Carbon::now()->year;
                                $startYear = 2024;
                                $years = range($currentYear, $startYear);

                                return array_combine($years, $years);
                            })
                            ->label('Start Date')
                            ->live(),
                        Select::make('to')
                            ->default(Carbon::now()->year)
                            ->options(function (Forms\Get $get) {
                                $currentYear = Carbon::now()->year;
                                $startYear = $get('from');
                                $years = range($currentYear, $startYear);

                                return array_combine($years, $years);
                            })
                            ->label('End Date')
                            ->rules(fn (Forms\Get $get) => ['min:'.$get('from')]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $year): Builder => $query->whereYear('created_at', '>=', $year),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $year): Builder => $query->whereYear('created_at', '<=', $year),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'From '.Carbon::parse($data['from'])->firstOfYear()->monthName.' '.$data['from'];
                        }
                        if ($data['to'] ?? null) {
                            $indicators['to'] = 'To '.Carbon::parse($data['to'])->lastOfYear()->monthName.' '.$data['to'];
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

    public function removeTableFilter(string $filterName, ?string $field = null, bool $isRemovingAllFilters = false): void
    {
        //
    }

    public function removeTableFilters(): void
    {
        //
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
