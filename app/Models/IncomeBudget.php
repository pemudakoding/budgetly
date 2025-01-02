<?php

namespace App\Models;

use App\Models\Builders\IncomeBudgetBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static IncomeBudgetBuilder query()
 *
 * @property int $histories_sum_amount
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

    /**
     * @return HasMany<IncomeBudgetHistory>
     */
    public function histories(): HasMany
    {
        return $this->hasMany(
            IncomeBudgetHistory::class,
            'income_budget_id',
        );
    }
}
