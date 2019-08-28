<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\SalaryProcessDetail;
use App\Repositories\SalaryProcessDetailRepository;

trait MakeSalaryProcessDetailTrait
{
    /**
     * Create fake instance of SalaryProcessDetail and save it in database
     *
     * @param array $salaryProcessDetailFields
     * @return SalaryProcessDetail
     */
    public function makeSalaryProcessDetail($salaryProcessDetailFields = [])
    {
        /** @var SalaryProcessDetailRepository $salaryProcessDetailRepo */
        $salaryProcessDetailRepo = \App::make(SalaryProcessDetailRepository::class);
        $theme = $this->fakeSalaryProcessDetailData($salaryProcessDetailFields);
        return $salaryProcessDetailRepo->create($theme);
    }

    /**
     * Get fake instance of SalaryProcessDetail
     *
     * @param array $salaryProcessDetailFields
     * @return SalaryProcessDetail
     */
    public function fakeSalaryProcessDetail($salaryProcessDetailFields = [])
    {
        return new SalaryProcessDetail($this->fakeSalaryProcessDetailData($salaryProcessDetailFields));
    }

    /**
     * Get fake data of SalaryProcessDetail
     *
     * @param array $salaryProcessDetailFields
     * @return array
     */
    public function fakeSalaryProcessDetailData($salaryProcessDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'salaryProcessMasterID' => $fake->randomDigitNotNull,
            'CompanyID' => $fake->word,
            'location' => $fake->randomDigitNotNull,
            'designationID' => $fake->randomDigitNotNull,
            'departmentID' => $fake->randomDigitNotNull,
            'schedulemasterID' => $fake->randomDigitNotNull,
            'empGrade' => $fake->randomDigitNotNull,
            'empGroup' => $fake->randomDigitNotNull,
            'processPeriod' => $fake->randomDigitNotNull,
            'startDate' => $fake->date('Y-m-d H:i:s'),
            'endDate' => $fake->date('Y-m-d H:i:s'),
            'empID' => $fake->word,
            'currency' => $fake->randomDigitNotNull,
            'noOfDays' => $fake->randomDigitNotNull,
            'bankMasterID' => $fake->randomDigitNotNull,
            'bankName' => $fake->word,
            'SwiftCode' => $fake->word,
            'accountNo' => $fake->word,
            'fixedPayments' => $fake->randomDigitNotNull,
            'fixedPaymentAdjustments' => $fake->randomDigitNotNull,
            'radioactiveBenifits' => $fake->randomDigitNotNull,
            'OverTime' => $fake->randomDigitNotNull,
            'extraDayPay' => $fake->randomDigitNotNull,
            'noPay' => $fake->randomDigitNotNull,
            'jobBonus' => $fake->randomDigitNotNull,
            'desertAllowance' => $fake->randomDigitNotNull,
            'monthlyAddition' => $fake->randomDigitNotNull,
            'MA_IsSSO' => $fake->randomDigitNotNull,
            'monthlyDedcution' => $fake->randomDigitNotNull,
            'balancePayments' => $fake->randomDigitNotNull,
            'loanDeductions' => $fake->randomDigitNotNull,
            'mobileCharges' => $fake->randomDigitNotNull,
            'passiEmployee' => $fake->randomDigitNotNull,
            'passiEmployer' => $fake->randomDigitNotNull,
            'pasiEmployerUE' => $fake->randomDigitNotNull,
            'splitSalary' => $fake->randomDigitNotNull,
            'taxAmount' => $fake->randomDigitNotNull,
            'expenseClaimAmount' => $fake->randomDigitNotNull,
            'netSalary' => $fake->randomDigitNotNull,
            'grossSalary' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'rptCurrencyID' => $fake->randomDigitNotNull,
            'rptCurrencyER' => $fake->randomDigitNotNull,
            'rptAmount' => $fake->randomDigitNotNull,
            'isRA' => $fake->randomDigitNotNull,
            'isHold' => $fake->randomDigitNotNull,
            'isSettled' => $fake->randomDigitNotNull,
            'holdSalary' => $fake->randomDigitNotNull,
            'heldSalaryPay' => $fake->randomDigitNotNull,
            'finalsettlementmasterID' => $fake->randomDigitNotNull,
            'modifieduser' => $fake->word,
            'modifiedpc' => $fake->word,
            'createduserGroup' => $fake->word,
            'createdpc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $salaryProcessDetailFields);
    }
}
