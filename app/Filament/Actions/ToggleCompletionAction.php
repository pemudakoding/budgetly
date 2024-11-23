<?php

namespace App\Filament\Actions;

use App\Models\ExpenseBudget;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ToggleCompletionAction extends BulkAction
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'toggle-completion';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('budgetly::actions.expense.toggle_completion.label'));

        $this->modalHeading(fn (): string => __('budgetly::actions.expense.toggle_completion.modal_label', ['label' => $this->getPluralModelLabel()]));

        $this->modalSubmitActionLabel(__('budgetly::actions.expense.toggle_completion.label'));

        $this->successNotificationTitle(__('budgetly::actions.expense.toggle_completion.notification'));

        $this->color('primary');

        $this->icon('heroicon-s-arrow-path');

        $this->requiresConfirmation();

        $this->modalIcon('heroicon-s-arrow-path');

        $this->action(function (): void {
            $this->process(static fn (Collection $records) => $records->each( fn (ExpenseBudget $record) => $record->update(['is_completed' => ! $record->is_completed])));

            $this->success();
        });

        $this->deselectRecordsAfterCompletion();
    }
}
