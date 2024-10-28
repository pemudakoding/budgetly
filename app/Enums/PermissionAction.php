<?php

namespace App\Enums;

enum PermissionAction: string
{
    case All = '*';
    case View = 'view';
    case Create = 'create';
    case Edit = 'edit';
    case Delete = 'delete';
    case ManageExpenseAccount = 'manage_expense_account';
}
