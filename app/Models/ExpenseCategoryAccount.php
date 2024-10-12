<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategoryAccount extends Model
{
    protected $fillable = [
        'expense_category_id', 'account_id', 'user_id',
    ];
}
