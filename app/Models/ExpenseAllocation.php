<?php

namespace App\Models;

use App\Models\Builders\ExpenseAllocationBuilder;
use Database\Factories\ExpenseAllocationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseAllocation extends Model
{
    /** @use HasFactory<ExpenseAllocationFactory> */
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'amount',
        'month',
    ];

    public function newEloquentBuilder($query): ExpenseAllocationBuilder
    {
        return new ExpenseAllocationBuilder($query);
    }

    /**
     * @return BelongsTo<Expense, ExpenseAllocation>
     */
    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
