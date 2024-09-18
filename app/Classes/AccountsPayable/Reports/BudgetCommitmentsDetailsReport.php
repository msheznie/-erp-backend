<?php

namespace App\Classes\AccountsPayable\Reports;

class BudgetCommitmentsDetailsReport
{

    public $glCode;
    public $accountsDescription;
    public $glTypes;
    public $budgetAmount;
    public $commitments;
    public $totalAvailableBudget;
    public $actualAmountSpentTillDateCB; // Actual amount Spent till date. (from current budget)
    public $actualAmountSpentTillDatePC; // Actual amount Spent till date. (from previous years commitment)
    public $commitmentsForCurrentYear;

    public $commitmentsFromPreviosYear;

    public $balance;
    public $total;

    /**
     * @param mixed $glCode
     */
    public function setGlCode($glCode): void
    {
        $this->glCode = $glCode;
    }

    /**
     * @param mixed $accountsDescription
     */
    public function setAccountsDescription($accountsDescription): void
    {
        $this->accountsDescription = $accountsDescription;
    }

    /**
     * @param mixed $glTypes
     */
    public function setGlTypes($glTypes): void
    {
        $this->glTypes = $glTypes;
    }

    /**
     * @param mixed $budgetAmount
     */
    public function setBudgetAmount($budgetAmount): void
    {
        $this->budgetAmount = number_format($budgetAmount,3);
    }

    /**
     * @param mixed $commitments
     */
    public function setCommitments($commitments): void
    {
        $this->commitments = number_format($commitments,3);
    }

    /**
     * @param mixed $totalAvailableBudget
     */
    public function setTotalAvailableBudget($totalAvailableBudget): void
    {
        $this->totalAvailableBudget = number_format($totalAvailableBudget,3);
    }

    /**
     * @param mixed $actualAmountSpentTillDateCB
     */
    public function setActualAmountSpentTillDateCB($actualAmountSpentTillDateCB): void
    {
        $this->actualAmountSpentTillDateCB = number_format($actualAmountSpentTillDateCB,3);
    }

    /**
     * @param mixed $actualAmountSpentTillDatePC
     */
    public function setActualAmountSpentTillDatePC($actualAmountSpentTillDatePC): void
    {
        $this->actualAmountSpentTillDatePC = number_format($actualAmountSpentTillDatePC,3);
    }


    /**
     * @param mixed $commitmentsForCurrentYear
     */
    public function setCommitmentsForCurrentYear($commitmentsForCurrentYear): void
    {
        $this->commitmentsForCurrentYear = number_format($commitmentsForCurrentYear,3);
    }


    /**
     * @param mixed $commitmentsFromPreviosYear
     */
    public function setCommitmentsFromPreviosYear($commitmentsFromPreviosYear): void
    {
        $this->commitmentsFromPreviosYear = $commitmentsFromPreviosYear;
    }

    /**
     * @param mixed $balance
     */
    public function setBalance(): void
    {
//        $balance = ($this->totalAvailableBudget) - ($this->actualAmountSpentTillDateCB) - ($this->actualAmountSpentTillDatePC) - ($this->commitmentsFromCurrenyYear);
        $balance = 1000000;
        $this->balance = number_format($balance,3);
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total): void
    {
        $this->total = number_format($total,3);
    }

}
