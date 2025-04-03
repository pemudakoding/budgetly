<?php

namespace App\Models;

use App\Models\Builders\AccountBuilder;
use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    /** @use HasFactory<AccountFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'legend',
    ];

    public function newEloquentBuilder($query): AccountBuilder
    {
        return new AccountBuilder($query);
    }

    /**
     * @return BelongsTo<User, Account>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id',
        );
    }

    /**
     * @return HasMany<AccountTransfer>
     */
    public function transfers(): HasMany
    {
        return $this->hasMany(
            AccountTransfer::class,
            'from_account_id',
        );
    }

    /**
     * @return HasMany<AccountTransfer>
     */
    public function receivedTransfers(): HasMany
    {
        return $this->hasMany(
            AccountTransfer::class,
            'to_account_id',
        );
    }

    /**
     * @return HasMany<Expense>
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(
            Expense::class,
            'account_id',
        );
    }
}
