<?php

namespace App\Filament\Clusters\FinancialSetup\Resources\ExpenseResource\Actions;

use App\Enums\ExpenseCategory;
use App\Enums\Permission;
use App\Enums\PermissionAction;
use App\Handlers\EligibleTo;
use App\Models\Account;
use App\Models\ExpenseCategoryAccount;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Components\Select;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class ManageAccountAction extends Action
{
    use CanCustomizeProcess;

    protected ?Closure $getRelationshipUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'manage-expense-account';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (): string => __('filament-panels::pages/financial-setup.expense.actions.manage-account'));

        $this->icon('heroicon-o-credit-card');

        $this->modalHeading(fn (): string => __('filament-panels::pages/financial-setup.expense.actions.manage-account'));

        $this->modalSubmitActionLabel('Submit');

        $this->successNotificationTitle(__('filament-notifications::financial-setup.manage-expense-account.success'));

        $this->groupedIcon(FilamentIcon::resolve('actions::create-action.grouped') ?? 'heroicon-m-plus');

        $this->record(null);

        $this->visible(condition: EligibleTo::do(Permission::BudgetingExpense, PermissionAction::ManageExpenseAccount));

        $this
            ->form(function () {
                $forms = [];
                $categories = ExpenseCategory::toArray();

                foreach ($categories as $category) {
                    $forms[] = Select::make($category)
                        ->label(ExpenseCategory::tryFrom($category)->render())
                        ->options(
                            Account::query()
                                ->whereUserId(auth()->id())
                                ->pluck('name', 'id')
                        )
                        ->multiple()
                        ->exists(Account::class, 'id');
                }

                return $forms;
            })
            ->fillForm(function () {
                $data = [];
                $categories = \App\Models\ExpenseCategory::all(['id',  'name']);

                foreach ($categories as $category) {
                    $data[$category->name] = ExpenseCategoryAccount::where('user_id', auth()->id())
                        ->where('expense_category_id', $category->id)
                        ->pluck('account_id');
                }

                return $data;
            });

        $this->action(function (): void {
            $this->process(function (array $data): void {
                $categories = array_filter($data);

                ExpenseCategoryAccount::query()->where('user_id', auth()->id())->delete();

                foreach ($categories as $category => $accountId) {
                    $categoryId = \App\Models\ExpenseCategory::where('name', $category)->first()->id;

                    foreach ($accountId as $id) {
                        $data = [
                            'user_id' => auth()->id(),
                            'expense_category_id' => $categoryId,
                            'account_id' => $id,
                        ];

                        ExpenseCategoryAccount::query()->create($data);
                    }
                }
            });

            $this->success();
        });
    }

    public function relationship(?Closure $relationship): static
    {
        $this->getRelationshipUsing = $relationship;

        return $this;
    }

    public function shouldClearRecordAfter(): bool
    {
        return true;
    }

    /**
     * @return Relation<Model>|Builder<Model>|null
     */
    public function getRelationship(): Relation|Builder|null
    {
        return $this->evaluate($this->getRelationshipUsing);
    }
}
