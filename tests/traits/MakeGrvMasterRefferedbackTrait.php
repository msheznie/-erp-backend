<?php

use Faker\Factory as Faker;
use App\Models\GrvMasterRefferedback;
use App\Repositories\GrvMasterRefferedbackRepository;

trait MakeGrvMasterRefferedbackTrait
{
    /**
     * Create fake instance of GrvMasterRefferedback and save it in database
     *
     * @param array $grvMasterRefferedbackFields
     * @return GrvMasterRefferedback
     */
    public function makeGrvMasterRefferedback($grvMasterRefferedbackFields = [])
    {
        /** @var GrvMasterRefferedbackRepository $grvMasterRefferedbackRepo */
        $grvMasterRefferedbackRepo = App::make(GrvMasterRefferedbackRepository::class);
        $theme = $this->fakeGrvMasterRefferedbackData($grvMasterRefferedbackFields);
        return $grvMasterRefferedbackRepo->create($theme);
    }

    /**
     * Get fake instance of GrvMasterRefferedback
     *
     * @param array $grvMasterRefferedbackFields
     * @return GrvMasterRefferedback
     */
    public function fakeGrvMasterRefferedback($grvMasterRefferedbackFields = [])
    {
        return new GrvMasterRefferedback($this->fakeGrvMasterRefferedbackData($grvMasterRefferedbackFields));
    }

    /**
     * Get fake data of GrvMasterRefferedback
     *
     * @param array $postFields
     * @return array
     */
    public function fakeGrvMasterRefferedbackData($grvMasterRefferedbackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'grvAutoID' => $fake->randomDigitNotNull,
            'grvTypeID' => $fake->randomDigitNotNull,
            'grvType' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'companyAddress' => $fake->word,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'companyFinancePeriodID' => $fake->randomDigitNotNull,
            'FYBiggin' => $fake->date('Y-m-d H:i:s'),
            'FYEnd' => $fake->date('Y-m-d H:i:s'),
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'grvDate' => $fake->date('Y-m-d H:i:s'),
            'stampDate' => $fake->date('Y-m-d H:i:s'),
            'grvSerialNo' => $fake->randomDigitNotNull,
            'grvPrimaryCode' => $fake->word,
            'grvDoRefNo' => $fake->word,
            'grvNarration' => $fake->text,
            'grvLocation' => $fake->randomDigitNotNull,
            'grvDOpersonName' => $fake->word,
            'grvDOpersonResID' => $fake->word,
            'grvDOpersonTelNo' => $fake->word,
            'grvDOpersonVehicleNo' => $fake->word,
            'supplierID' => $fake->randomDigitNotNull,
            'supplierPrimaryCode' => $fake->word,
            'supplierName' => $fake->word,
            'supplierAddress' => $fake->text,
            'supplierTelephone' => $fake->word,
            'supplierFax' => $fake->word,
            'supplierEmail' => $fake->word,
            'liabilityAccountSysemID' => $fake->randomDigitNotNull,
            'liabilityAccount' => $fake->word,
            'UnbilledGRVAccountSystemID' => $fake->randomDigitNotNull,
            'UnbilledGRVAccount' => $fake->word,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'companyReportingCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingER' => $fake->randomDigitNotNull,
            'supplierDefaultCurrencyID' => $fake->randomDigitNotNull,
            'supplierDefaultER' => $fake->randomDigitNotNull,
            'supplierTransactionCurrencyID' => $fake->randomDigitNotNull,
            'supplierTransactionER' => $fake->randomDigitNotNull,
            'grvConfirmedYN' => $fake->randomDigitNotNull,
            'grvConfirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'grvConfirmedByEmpID' => $fake->word,
            'grvConfirmedByName' => $fake->word,
            'grvConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'grvCancelledYN' => $fake->randomDigitNotNull,
            'grvCancelledBySystemID' => $fake->randomDigitNotNull,
            'grvCancelledBy' => $fake->word,
            'grvCancelledByName' => $fake->word,
            'grvCancelledDate' => $fake->date('Y-m-d H:i:s'),
            'grvTotalComRptCurrency' => $fake->randomDigitNotNull,
            'grvTotalLocalCurrency' => $fake->randomDigitNotNull,
            'grvTotalSupplierDefaultCurrency' => $fake->randomDigitNotNull,
            'grvTotalSupplierTransactionCurrency' => $fake->randomDigitNotNull,
            'grvDiscountPercentage' => $fake->randomDigitNotNull,
            'grvDiscountAmount' => $fake->randomDigitNotNull,
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedByUserID' => $fake->word,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'invoiceBeforeGRVYN' => $fake->randomDigitNotNull,
            'deliveryConfirmedYN' => $fake->randomDigitNotNull,
            'interCompanyTransferYN' => $fake->randomDigitNotNull,
            'FromCompanySystemID' => $fake->randomDigitNotNull,
            'FromCompanyID' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'TIMESTAMP' => $fake->date('Y-m-d H:i:s')
        ], $grvMasterRefferedbackFields);
    }
}
