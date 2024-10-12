<?php

namespace App\Filament\Resources\MasterData\ExpenseResource\Actions;

use App\Enums\ExpenseCategory;
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

        $this->label(fn (): string => 'Manage Expense Account');

        $this->icon('heroicon-o-credit-card');

        $this->modalHeading(fn (): string => 'Manage Expense Account');

        $this->modalSubmitActionLabel('Submit');

        $this->successNotificationTitle('Success manage account for your expenses');

        $this->groupedIcon(FilamentIcon::resolve('actions::create-action.grouped') ?? 'heroicon-m-plus');

        $this->record(null);

        $this
            ->form(function () {
                $forms = [];
                $categories = ExpenseCategory::toArray();

                foreach ($categories as $category) {
                    $forms[] = Select::make($category)
                        ->label($category)
                        ->options(Account::query()
                            ->whereUserId(auth()->id())
                            ->pluck('name', 'id')
                        )
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
                        ->first()
                        ?->account_id;
                }

                return $data;
            });

        $this->action(function (): void {
            $this->process(function (array $data): void {
                $categories = array_filter($data);

                foreach ($categories as $category => $accountId) {
                    $categoryId = \App\Models\ExpenseCategory::where('name', $category)->first()->id;

                    $data = [
                        'user_id' => auth()->id(),
                        'expense_category_id' => $categoryId,
                        'account_id' => $accountId,
                    ];

                    ExpenseCategoryAccount::query()->updateOrCreate(
                        ['user_id' => $data['user_id'], 'expense_category_id' => $data['expense_category_id']],
                        $data
                    );
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
