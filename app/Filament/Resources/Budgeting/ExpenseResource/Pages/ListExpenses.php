<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Pages;

use App\Enums\ExpenseCategory;
use App\Filament\Resources\Budgeting\ExpenseResource;
use App\Models\Builders\ExpenseBuilder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Components\Tab;
use Filament\Resources\Concerns\HasTabs;
use Filament\Resources\Pages\ListRecords;

/**
 * @property Form $form
 */
class ListExpenses extends ListRecords implements HasForms
{
    use HasTabs, InteractsWithForms;

    protected static string $resource = ExpenseResource::class;

    protected static string $view = 'filament.resources.budgeting.expense.list-record';

    /**
     * @var array<string>
     */
    public ?array $data = [];

    public function mount(): void
    {
        parent::mount();

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return static::$resource::form($form);
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            ...array_reduce(
                ExpenseCategory::cases(),
                function ($categories, ExpenseCategory $category): array {
                    $categories[lcfirst($category->value)] = Tab::make()->modifyQueryUsing(
                        fn (ExpenseBuilder $query): ExpenseBuilder => $query->whereCategory($category)
                    );

                    return $categories;
                },
                []
            ),
        ];
    }
}
