<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomeBudget extends Model
{
    /** @use HasFactory<\Database\Factories\IncomeBudgetFactory> */
    use HasFactory;

    protected $fillable = [
        'income_id',
        'amount',
        'month',
    ];

    /**
     * @return BelongsTo<Income, IncomeBudget>
     */
    public function income(): BelongsTo
    {
        return $this->belongsTo(
            Income::class,
            'income_id',
        );
    }
}
