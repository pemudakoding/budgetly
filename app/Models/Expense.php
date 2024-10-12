<?php

namespace App\Models;

use App\Models\Builders\ExpenseBuilder;
use Database\Factories\ExpenseFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property ExpenseCategory $category
 * @property \App\Enums\ExpenseCategory $enumerateCategory
 */
class Expense extends Model
{
    /**
     * @use HasFactory<ExpenseFactory>
     */
    use HasFactory;

    protected $fillable = [
        'name',
        'expense_category_id',
        'user_id',
    ];

    public function newEloquentBuilder($query): ExpenseBuilder
    {
        return new ExpenseBuilder($query);
    }

    /**
     * @return BelongsTo<User, Expense>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /**
     * @return BelongsTo<ExpenseCategory, Expense>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(
            ExpenseCategory::class,
            'expense_category_id'
        );
    }

    /**
     * @return Attribute<ExpenseCategory, string>
     */
    public function enumerateCategory(): Attribute
    {
        return Attribute::make(
            get: fn (): \App\Enums\ExpenseCategory => \App\Enums\ExpenseCategory::tryFrom($this->category->name)
        );
    }
}
