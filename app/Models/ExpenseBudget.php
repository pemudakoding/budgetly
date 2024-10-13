<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseBudget extends Model
{
    /** @use HasFactory<\Database\Factories\ExpenseBudgetFactory> */
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'description',
        'amount',
    ];

    /**
     * @return BelongsTo<Expense, ExpenseBudget>
     */
    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
