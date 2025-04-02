<?php

namespace App\Concerns;

use App\Models\AccountTransfer;
use App\Models\Expense;
use App\Models\Income;
use App\Models\IncomeBudget;
use App\ValueObjects\Money;

trait AcccountBalanceCalculation
{
    /**
     * Calculate the total non-fluctuating income budget for the given account IDs.
     *
     * @param  array<int|string>  $accountIds
     */
    public static function calculateIncomeBudget(array $accountIds): float
    {
        return Income::query()
            ->whereIn('account_id', $accountIds)
            ->where('is_fluctuating', false)
            ->withSum('budgets', 'amount')
            ->get()
            ->sum('budgets_sum_amount');
    }

    /**
     * Calculate the total fluctuating income budget for the given account IDs.
     * This includes budgets' histories.
     *
     * @param  array<int|string>  $accountIds
     */
    public static function calculateFluctuatingIncomeBudget(array $accountIds): float
    {
        return Income::query()
            ->whereIn('account_id', $accountIds)
            ->where('is_fluctuating', true)
            ->with('budgets.histories')
            ->get()
            ->flatMap(fn (Income $income) => $income->budgets)
            ->flatMap(fn (IncomeBudget $budget) => $budget->histories)
            ->sum('amount');
    }

    /**
     * Calculate the total expense budget for the given account IDs.
     *
     * @param  array<int|string>  $accountIds
     */
    public static function calculateExpenseBudget(array $accountIds): float
    {
        return Expense::query()
            ->whereHas('category.accounts', function ($query) use ($accountIds) {
                $query->whereIn('account_id', $accountIds);
            })
            ->withSum('budgets', 'amount')
            ->get()
            ->sum('budgets_sum_amount');
    }

    /**
     * Calculate the total transfer in account for the given account IDs.
     *
     * @param  array<int|string>  $accountIds
     */
    public static function calculateTransferInAccount(array $accountIds): float
    {
        /** @var float $result */
        $result = AccountTransfer::query()
            ->whereIn('to_account_id', $accountIds)
            ->sum('amount');

        return $result;
    }

    /**
     * Calculate the total transfer out account for the given account IDs.
     *
     * @param  array<int|string>  $accountIds
     */
    public static function calculateTransferOutAccount(array $accountIds): float
    {
        /** @var float $result */
        $result = AccountTransfer::query()
            ->whereIn('from_account_id', $accountIds)
            ->sum('amount');

        return $result;
    }

    /**
     * Calculate the remaining balance after considering income and expense budgets.
     *
     * @param  array<int|string>  $accountIds
     */
    public static function calculateRemainingBalance(array $accountIds, bool $format = false): float|string
    {
        $incomeBudget = self::calculateIncomeBudget($accountIds);
        $fluctuatingIncomeBudget = self::calculateFluctuatingIncomeBudget($accountIds);
        $transferInAccount = self::calculateTransferInAccount($accountIds);
        $expenseBudget = self::calculateExpenseBudget($accountIds);
        $transferOutAccount = self::calculateTransferOutAccount($accountIds);

        $total = ($incomeBudget + $fluctuatingIncomeBudget + $transferInAccount) - ($expenseBudget + $transferOutAccount);

        if ($format) {
            return Money::format($total);
        }

        return $total;
    }
}
