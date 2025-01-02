<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomeBudgetHistory extends Model
{
    /** @use HasFactory<\Database\Factories\IncomeBudgetHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'income_budget_id',
        'description',
        'amount',
        'revenue_at',
    ];

    /**
     * @return BelongsTo<IncomeBudget, IncomeBudgetHistory>
     */
    public function budget(): BelongsTo
    {
        return $this->belongsTo(
            IncomeBudget::class,
            'income_budget_id',
        );
    }
}
