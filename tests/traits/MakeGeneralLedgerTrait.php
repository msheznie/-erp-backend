<?php

use Faker\Factory as Faker;
use App\Models\GeneralLedger;
use App\Repositories\GeneralLedgerRepository;

trait MakeGeneralLedgerTrait
{
    /**
     * Create fake instance of GeneralLedger and save it in database
     *
     * @param array $generalLedgerFields
     * @return GeneralLedger
     */
    public function makeGeneralLedger($generalLedgerFields = [])
    {
        /** @var GeneralLedgerRepository $generalLedgerRepo */
        $generalLedgerRepo = App::make(GeneralLedgerRepository::class);
        $theme = $this->fakeGeneralLedgerData($generalLedgerFields);
        return $generalLedgerRepo->create($theme);
    }

    /**
     * Get fake instance of GeneralLedger
     *
     * @param array $generalLedgerFields
     * @return GeneralLedger
     */
    public function fakeGeneralLedger($generalLedgerFields = [])
    {
        return new GeneralLedger($this->fakeGeneralLedgerData($generalLedgerFields));
    }

    /**
     * Get fake data of GeneralLedger
     *
     * @param array $postFields
     * @return array
     */
    public function fakeGeneralLedgerData($generalLedgerFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'masterCompanyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'documentSystemCode' => $fake->randomDigitNotNull,
            'documentCode' => $fake->word,
            'documentDate' => $fake->date('Y-m-d H:i:s'),
            'documentYear' => $fake->randomDigitNotNull,
            'documentMonth' => $fake->randomDigitNotNull,
            'chequeNumber' => $fake->randomDigitNotNull,
            'invoiceNumber' => $fake->word,
            'invoiceDate' => $fake->date('Y-m-d H:i:s'),
            'chartOfAccountSystemID' => $fake->randomDigitNotNull,
            'glCode' => $fake->word,
            'glAccountType' => $fake->word,
            'holdingShareholder' => $fake->word,
            'holdingPercentage' => $fake->randomDigitNotNull,
            'nonHoldingPercentage' => $fake->randomDigitNotNull,
            'documentConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'documentConfirmedBy' => $fake->word,
            'documentFinalApprovedDate' => $fake->date('Y-m-d H:i:s'),
            'documentFinalApprovedBy' => $fake->word,
            'documentNarration' => $fake->text,
            'contractUID' => $fake->randomDigitNotNull,
            'clientContractID' => $fake->word,
            'supplierCodeSystem' => $fake->randomDigitNotNull,
            'venderName' => $fake->word,
            'documentTransCurrencyID' => $fake->randomDigitNotNull,
            'documentTransCurrencyER' => $fake->randomDigitNotNull,
            'documentTransAmount' => $fake->randomDigitNotNull,
            'documentLocalCurrencyID' => $fake->randomDigitNotNull,
            'documentLocalCurrencyER' => $fake->randomDigitNotNull,
            'documentLocalAmount' => $fake->randomDigitNotNull,
            'documentRptCurrencyID' => $fake->randomDigitNotNull,
            'documentRptCurrencyER' => $fake->randomDigitNotNull,
            'documentRptAmount' => $fake->randomDigitNotNull,
            'empID' => $fake->word,
            'employeePaymentYN' => $fake->randomDigitNotNull,
            'isRelatedPartyYN' => $fake->randomDigitNotNull,
            'hideForTax' => $fake->randomDigitNotNull,
            'documentType' => $fake->randomDigitNotNull,
            'advancePaymentTypeID' => $fake->randomDigitNotNull,
            'isPdcChequeYN' => $fake->randomDigitNotNull,
            'isAddon' => $fake->randomDigitNotNull,
            'isAllocationJV' => $fake->randomDigitNotNull,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserID' => $fake->word,
            'createdUserPC' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $generalLedgerFields);
    }
}
