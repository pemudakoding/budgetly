<?php

namespace App\Enums;

use DateTime;

enum Permissions: string
{
    case FinancialSetup = 'financial-setup';
    case FinancialSetupAccount = 'financial-setup.account';
    case FinancialSetupExpense = 'financial-setup.expense';
    case FinancialSetupIncome = 'financial-setup.income';
    case BudgetingIncome = 'budgeting.income';
    case BudgetingIncomeBudget = 'budgeting.income.budget';
    case BudgetingExpense = 'budgeting.expense';
    case BudgetingExpenseAllocation = 'budgeting.expense.allocation';
    case BudgetingExpenseRealization = 'budgeting.expense.realization';

    public function suffix(PermissionAction $action): string
    {
        return $this->value.'.'.$action->value;
    }

    /**
     * @param string $guard
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
            Permissions::FinancialSetup->value => [PermissionAction::All],
            Permissions::FinancialSetupExpense->value => [PermissionAction::All, PermissionAction::View, PermissionAction::Edit, PermissionAction::Delete, PermissionAction::ManageExpenseAccount],
            Permissions::FinancialSetupIncome->value => [PermissionAction::All, PermissionAction::View, PermissionAction::Edit, PermissionAction::Delete],
            //Budgeting
            Permissions::BudgetingIncome->value => [PermissionAction::All, PermissionAction::View],
            Permissions::BudgetingIncomeBudget->value => [PermissionAction::All, PermissionAction::View, PermissionAction::Create, PermissionAction::Edit, PermissionAction::Delete],
            Permissions::BudgetingExpenseAllocation->value => [PermissionAction::All, PermissionAction::View, PermissionAction::Create, PermissionAction::Edit, PermissionAction::Delete],
            Permissions::BudgetingExpenseRealization->value => [PermissionAction::All, PermissionAction::View, PermissionAction::Create, PermissionAction::Edit, PermissionAction::Delete],
        ];

        return array_map(
            function (string $module) use ($moduleActions, $guard) {
                return array_filter(
                    array_map(
                        fn (PermissionAction $action) => [
                            'name' => Permissions::tryFrom($module)->suffix($action),
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
