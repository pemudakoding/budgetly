<?php

namespace App\Models;

use App\Models\Builders\IncomeBudgetBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static IncomeBudgetBuilder query()
 */
class IncomeBudget extends Model
{
    /** @use HasFactory<\Database\Factories\IncomeBudgetFactory> */
    use HasFactory;

    protected $fillable = [
        'income_id',
        'amount',
        'month',
    ];

    public function newEloquentBuilder($query): IncomeBudgetBuilder
    {
        return new IncomeBudgetBuilder($query);
    }

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
