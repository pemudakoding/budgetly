<?php

namespace App\Models;

use App\Enums\ExpenseCategory;
use App\Models\Builders\ExpenseBuilder;
use Database\Factories\ExpenseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property ExpenseCategory $category
 */
class Expense extends Model
{
    /**
     * @use HasFactory<ExpenseFactory>
     */
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
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

    protected function casts(): array
    {
        return [
            'category' => ExpenseCategory::class,
        ];
    }
}
