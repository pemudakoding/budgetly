<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Pages;

use App\Enums\ExpenseCategory;
use App\Filament\Resources\Budgeting\ExpenseResource;
use App\Models\Builders\ExpenseBuilder;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Concerns\HasTabs;
use Filament\Resources\Pages\ListRecords;
use Livewire\Attributes\Url;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property Form $form
 */
class ListExpenses extends ListRecords implements HasForms
{
    use ExposesTableToWidgets, HasTabs, InteractsWithForms;

    protected static string $resource = ExpenseResource::class;

    protected static string $view = 'filament.resources.budgeting.expense.list-record';

    #[Url(keep: true)]
    public string|int $year = '';

    #[Url(keep: true)]
    public string $month = '';

    /**
     * @var array<string>
     */
    public ?array $data = [];

    public function mount(): void
    {
        parent::mount();

        if ($this->year === '') {
            $this->year = Carbon::now()->year;
        }

        if ($this->month === '') {
            $this->month = str_pad((string) Carbon::now()->month, 2, '0', STR_PAD_LEFT);
        }

        if ($this->month > 12) {
            abort(Response::HTTP_NOT_ACCEPTABLE);
        }

        $this->form->fill([
            'year' => $this->year,
            'month' => $this->month,
        ]);
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
                    $categories[$category->render()] = Tab::make()
                        ->icon($category->resolveIcon())
                        ->modifyQueryUsing(
                            fn (ExpenseBuilder $query): ExpenseBuilder => $query->whereCategory($category)
                        );

                    return $categories;
                },
                []
            ),
        ];
    }
}
