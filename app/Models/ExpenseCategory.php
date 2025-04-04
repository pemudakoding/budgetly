<?php

namespace App\Models;

use Database\Factories\ExpenseCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property float $expense_budgets_sum_amount
 */
class ExpenseCategory extends Model
{
    /**
     * @use HasFactory<ExpenseCategoryFactory>
     */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * @return HasManyThrough<ExpenseBudget>
     */
    public function expenseBudgets(): HasManyThrough
    {
        return $this->hasManyThrough(
            ExpenseBudget::class,
            Expense::class,
            'expense_category_id',
            'expense_id',
            'id'
        );
    }

    /**
     * @return HasMany<ExpenseCategoryAccount>
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(ExpenseCategoryAccount::class, 'expense_category_id', 'id');
    }
}
