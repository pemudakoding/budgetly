<?php

namespace App\Models;

use App\Models\Builders\ExpenseBudgetBuilder;
use Database\Factories\ExpenseBudgetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static ExpenseBudgetBuilder query()
 */
class ExpenseBudget extends Model
{
    /** @use HasFactory<ExpenseBudgetFactory> */
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'description',
        'amount',
        'realized_at',
        'is_completed'
    ];

    public function newEloquentBuilder($query): ExpenseBudgetBuilder
    {
        return new ExpenseBudgetBuilder($query);
    }

    /**
     * @return BelongsTo<Expense, ExpenseBudget>
     */
    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
