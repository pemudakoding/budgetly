<?php

namespace Database\Seeders;

use App\Enums\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AssignPermissionToRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = Permission::mapPermissions();
        $permissions = array_map(fn (array $permission) => $permission['name'], array_merge(...array_values($permissions)));

        $filterPermissions = function (array $neededPermissions) use ($permissions) {
            return array_filter($permissions, function ($permission) use ($neededPermissions) {
                /** @var Permission $neededPermission */
                foreach ($neededPermissions as $neededPermission) {
                    if (str_contains($permission, $neededPermission->value)) {
                        return true;
                    }
                }

                return false;
            });
        };

        /** @var Role $admin */
        $admin = Role::query()->whereName(\App\Enums\Role::Admin->value)->first();
        $admin->syncPermissions(...$filterPermissions([
            Permission::FinancialSetup,
            Permission::FinancialSetupAccount,
            Permission::BudgetingIncome,
            Permission::BudgetingIncomeBudget,
            Permission::BudgetingExpense,
            Permission::BudgetingExpenseAllocation,
            Permission::BudgetingExpenseRealization,
            Permission::UserManagement,
        ]));

        /** @var Role $admin */
        $admin = Role::query()->whereName(\App\Enums\Role::User->value)->first();
        $admin->syncPermissions(...$filterPermissions([
            Permission::FinancialSetup,
            Permission::FinancialSetupAccount,
            Permission::BudgetingIncome,
            Permission::BudgetingIncomeBudget,
            Permission::BudgetingExpense,
            Permission::BudgetingExpenseAllocation,
            Permission::BudgetingExpenseRealization,
        ]));
    }
}
