<?php

namespace App\Models;

use App\Models\Builders\AccountBuilder;
use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
            'user_id'
        );
    }
}
