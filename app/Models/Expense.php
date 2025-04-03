<?php

namespace App\Models;

use App\Models\Builders\ExpenseBuilder;
use Database\Factories\ExpenseFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property ExpenseCategory $category
 * @property \App\Enums\ExpenseCategory $enumerateCategory
 * @property float|int|string $total
 * @property int $id
 */
class Expense extends Model
{
    /**
     * @use HasFactory<ExpenseFactory>
     */
    use HasFactory;

    protected $fillable = [
        'name',
        'expense_category_id',
        'user_id',
        'account_id',
    ];

    public function newEloquentBuilder($query): ExpenseBuilder
    {
        return new ExpenseBuilder($query);
    }

    /**
     * @return BelongsTo<User, Expense>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /**
     * @return BelongsTo<ExpenseCategory, Expense>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(
            ExpenseCategory::class,
            'expense_category_id'
        );
    }

    /**
     * @return HasMany<ExpenseBudget>
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(
            ExpenseBudget::class,
            'expense_id'
        );
    }

    /**
     * @return HasMany<ExpenseAllocation>
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(
            ExpenseAllocation::class,
            'expense_id'
        );
    }

    /**
     * @return Attribute<ExpenseCategory, string>
     */
    public function enumerateCategory(): Attribute
    {
        return Attribute::make(
            get: fn (): \App\Enums\ExpenseCategory => \App\Enums\ExpenseCategory::tryFrom($this->category->name)
        );
    }

    /**
     * @return Attribute<int|float|string, string>
     */
    public function total(): Attribute
    {
        return Attribute::make(
            get: fn (): int|float|string => $this->budgets()->sum('amount')
        );
    }

    /**
     * @return BelongsTo<Account, Expense>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(
            Account::class,
            'account_id'
        );
    }
}
