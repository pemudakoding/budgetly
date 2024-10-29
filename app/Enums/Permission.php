<?php

namespace App\Enums;

use DateTime;

enum Permission: string
{
    case FinancialSetup = 'financial-setup';
    case FinancialSetupAccount = 'financial-setup.account';
    case BudgetingIncome = 'budgeting.income';
    case BudgetingIncomeBudget = 'budgeting.income.budget';
    case BudgetingExpense = 'budgeting.expense';
    case BudgetingExpenseAllocation = 'budgeting.expense.allocation';
    case BudgetingExpenseRealization = 'budgeting.expense.realization';
    case UserManagement = 'user-management';

    public function suffix(PermissionAction $action): string
    {
        return $this->value.'.'.$action->value;
    }

    /**
     * @return array<int, array<int, array{
     *       name: string,
     *       guard_name: string,
     *       created_at: DateTime,
     *       updated_at: DateTime
     *   }>>
     */
    public static function mapPermissions(string $guard = 'web'): array
    {
        /** @var array<string, list<PermissionAction>> $moduleActions */
        $moduleActions = [
            // Financial Setup
            Permission::FinancialSetup->value => [PermissionAction::All],
            Permission::FinancialSetupAccount->value => [PermissionAction::All, PermissionAction::View, PermissionAction::Update, PermissionAction::Delete],
            // Budgeting
            Permission::BudgetingIncome->value => [PermissionAction::All, PermissionAction::View],
            Permission::BudgetingIncomeBudget->value => [PermissionAction::All, PermissionAction::View, PermissionAction::Create, PermissionAction::Update, PermissionAction::Delete],
            Permission::BudgetingExpense->value => [PermissionAction::All, PermissionAction::View, PermissionAction::Create, PermissionAction::Update, PermissionAction::Delete, PermissionAction::ManageExpenseAccount],
            Permission::BudgetingExpenseAllocation->value => [PermissionAction::All, PermissionAction::View, PermissionAction::Create, PermissionAction::Update, PermissionAction::Delete],
            Permission::BudgetingExpenseRealization->value => [PermissionAction::All, PermissionAction::View, PermissionAction::Create, PermissionAction::Update, PermissionAction::Delete],
            // Settings
            Permission::UserManagement->value => [PermissionAction::All, PermissionAction::View, PermissionAction::Create, PermissionAction::Update, PermissionAction::Delete],
        ];

        return array_map(
            function (string $module) use ($moduleActions, $guard) {
                return array_filter(
                    array_map(
                        fn (PermissionAction $action) => [
                            'name' => Permission::tryFrom($module)->suffix($action),
                            'guard_name' => $guard,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        $moduleActions[$module],
                    )
                );
            },
            array_keys($moduleActions)
        );
    }
}
