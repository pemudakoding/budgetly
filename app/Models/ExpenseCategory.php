<?php

namespace App\Models;

use Database\Factories\ExpenseCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    /**
     * @use HasFactory<ExpenseCategoryFactory>
     */
    use HasFactory;

    protected $fillable = [
        'name',
    ];
}
