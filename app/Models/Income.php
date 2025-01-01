<?php

namespace App\Models;

use App\Models\Builders\IncomeBuilder;
use Database\Factories\IncomeFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int|string|float $total
 */
class Income extends Model
{
    /** @use HasFactory<IncomeFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'account_id',
        'is_fluctuating',
    ];

    public function newEloquentBuilder($query): IncomeBuilder
    {
        return new IncomeBuilder($query);
    }

    /**
     * @return BelongsTo<User, Income>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /**
     * @return BelongsTo<Account, Income>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @return HasOne<IncomeBudget>
     */
    public function currentMonthBudget(): HasOne
    {
        return $this->hasOne(
            IncomeBudget::class,
            'income_id',
        )
            ->whereMonth('created_at', Carbon::now()->month);
    }

    /**
     * @return HasMany<IncomeBudget>
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(
            IncomeBudget::class,
            'income_id',
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
}
