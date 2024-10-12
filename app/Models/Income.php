<?php

namespace App\Models;

use App\Models\Builders\IncomeBuilder;
use Database\Factories\IncomeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    /** @use HasFactory<IncomeFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'account_id',
    ];

    public function newEloquentBuilder($query): IncomeBuilder
    {
        return new IncomeBuilder($query);
    }

    /**
     * @return BelongsTo<Account, Income>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
