<?php

namespace App\Models;

use Database\Factories\ExpenseCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
}
