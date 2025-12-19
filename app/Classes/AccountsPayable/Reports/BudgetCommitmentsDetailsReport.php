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
        $this->budgetAmount = $budgetAmount;
    }

    /**
     * @param mixed $commitments
     */
    public function setCommitments($commitments): void
    {
        $this->commitments = $commitments;
    }

    /**
     * @param mixed $totalAvailableBudget
     */
    public function setTotalAvailableBudget($totalAvailableBudget): void
    {
        $this->totalAvailableBudget = $totalAvailableBudget;
    }

    /**
     * @param mixed $actualAmountSpentTillDateCB
     */
    public function setActualAmountSpentTillDateCB($actualAmountSpentTillDateCB): void
    {
        $this->actualAmountSpentTillDateCB = $actualAmountSpentTillDateCB;
    }

    /**
     * @param mixed $actualAmountSpentTillDatePC
     */
    public function setActualAmountSpentTillDatePC($actualAmountSpentTillDatePC): void
    {
        $this->actualAmountSpentTillDatePC = $actualAmountSpentTillDatePC;
    }


    /**
     * @param mixed $commitmentsForCurrentYear
     */
    public function setCommitmentsForCurrentYear($commitmentsForCurrentYear): void
    {
        $this->commitmentsForCurrentYear = $commitmentsForCurrentYear;
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


        if(!is_numeric($this->actualAmountSpentTillDateCB))
            $this->actualAmountSpentTillDateCB = 0;

        if(!is_numeric($this->actualAmountSpentTillDatePC))
            $this->actualAmountSpentTillDatePC = 0;

        if(!is_numeric($this->commitmentsForCurrentYear))
            $this->commitmentsForCurrentYear = 0;

        if(!is_numeric($this->commitmentsFromPreviosYear))
            $this->commitmentsFromPreviosYear = 0;

        $balance = ($this->totalAvailableBudget) - ($this->actualAmountSpentTillDateCB) - ($this->actualAmountSpentTillDatePC) - ($this->commitmentsForCurrentYear) - ($this->commitmentsFromPreviosYear);
        $this->balance = $balance;
    }


    /**
     * @param mixed $total
     */
    public function setTotal(): void
    {
        $this->total += $this->balance;
    }

    public function getTotal()
    {
        return $this->balance;
    }


}
