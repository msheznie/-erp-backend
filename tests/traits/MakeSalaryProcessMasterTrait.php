<?php

use Faker\Factory as Faker;
use App\Models\SalaryProcessMaster;
use App\Repositories\SalaryProcessMasterRepository;

trait MakeSalaryProcessMasterTrait
{
    /**
     * Create fake instance of SalaryProcessMaster and save it in database
     *
     * @param array $salaryProcessMasterFields
     * @return SalaryProcessMaster
     */
    public function makeSalaryProcessMaster($salaryProcessMasterFields = [])
    {
        /** @var SalaryProcessMasterRepository $salaryProcessMasterRepo */
        $salaryProcessMasterRepo = App::make(SalaryProcessMasterRepository::class);
        $theme = $this->fakeSalaryProcessMasterData($salaryProcessMasterFields);
        return $salaryProcessMasterRepo->create($theme);
    }

    /**
     * Get fake instance of SalaryProcessMaster
     *
     * @param array $salaryProcessMasterFields
     * @return SalaryProcessMaster
     */
    public function fakeSalaryProcessMaster($salaryProcessMasterFields = [])
    {
        return new SalaryProcessMaster($this->fakeSalaryProcessMasterData($salaryProcessMasterFields));
    }

    /**
     * Get fake data of SalaryProcessMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSalaryProcessMasterData($salaryProcessMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'CompanyID' => $fake->word,
            'salaryProcessCode' => $fake->word,
            'documentID' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'processPeriod' => $fake->randomDigitNotNull,
            'startDate' => $fake->date('Y-m-d H:i:s'),
            'endDate' => $fake->date('Y-m-d H:i:s'),
            'Currency' => $fake->randomDigitNotNull,
            'salaryMonth' => $fake->randomDigitNotNull,
            'description' => $fake->word,
            'createDate' => $fake->date('Y-m-d H:i:s'),
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'isReferredBack' => $fake->randomDigitNotNull,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedby' => $fake->word,
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedby' => $fake->word,
            'approvedDate' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'isRGLConfirm' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyExchangeRate' => $fake->randomDigitNotNull,
            'rptCurrencyID' => $fake->randomDigitNotNull,
            'rptCurrencyExchangeRate' => $fake->randomDigitNotNull,
            'updateNoOfDaysBtnFlag' => $fake->randomDigitNotNull,
            'updateSalaryBtnFlag' => $fake->randomDigitNotNull,
            'getEmployeeBtnFlag' => $fake->randomDigitNotNull,
            'updateSSOBtnFlag' => $fake->randomDigitNotNull,
            'updateRABenefitBtnFlag' => $fake->randomDigitNotNull,
            'updateTaxStep1BtnFlag' => $fake->randomDigitNotNull,
            'updateTaxStep2BtnFlag' => $fake->randomDigitNotNull,
            'updateTaxStep3BtnFlag' => $fake->randomDigitNotNull,
            'updateTaxStep4BtnFlag' => $fake->randomDigitNotNull,
            'updateHeldSalaryBtnFlag' => $fake->randomDigitNotNull,
            'isHeldSalary' => $fake->randomDigitNotNull,
            'showpaySlip' => $fake->randomDigitNotNull,
            'paymentGenerated' => $fake->randomDigitNotNull,
            'PayMasterAutoId' => $fake->randomDigitNotNull,
            'bankIDForPayment' => $fake->randomDigitNotNull,
            'bankAccountIDForPayment' => $fake->randomDigitNotNull,
            'salaryProcessType' => $fake->randomDigitNotNull,
            'thirteenMonthJVID' => $fake->randomDigitNotNull,
            'gratuityJVID' => $fake->randomDigitNotNull,
            'gratuityReversalJVID' => $fake->randomDigitNotNull,
            'modifieduser' => $fake->word,
            'modifiedpc' => $fake->word,
            'createduserGroup' => $fake->word,
            'createdpc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $salaryProcessMasterFields);
    }
}
