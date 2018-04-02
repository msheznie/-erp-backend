<?php

use Faker\Factory as Faker;
use App\Models\GRVMaster;
use App\Repositories\GRVMasterRepository;

trait MakeGRVMasterTrait
{
    /**
     * Create fake instance of GRVMaster and save it in database
     *
     * @param array $gRVMasterFields
     * @return GRVMaster
     */
    public function makeGRVMaster($gRVMasterFields = [])
    {
        /** @var GRVMasterRepository $gRVMasterRepo */
        $gRVMasterRepo = App::make(GRVMasterRepository::class);
        $theme = $this->fakeGRVMasterData($gRVMasterFields);
        return $gRVMasterRepo->create($theme);
    }

    /**
     * Get fake instance of GRVMaster
     *
     * @param array $gRVMasterFields
     * @return GRVMaster
     */
    public function fakeGRVMaster($gRVMasterFields = [])
    {
        return new GRVMaster($this->fakeGRVMasterData($gRVMasterFields));
    }

    /**
     * Get fake data of GRVMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeGRVMasterData($gRVMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'grvType' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'companyAddress' => $fake->word,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'FYBiggin' => $fake->date('Y-m-d H:i:s'),
            'FYEnd' => $fake->date('Y-m-d H:i:s'),
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'grvDate' => $fake->date('Y-m-d H:i:s'),
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
            'grvConfirmedByEmpID' => $fake->word,
            'grvConfirmedByName' => $fake->word,
            'grvConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'grvCancelledYN' => $fake->randomDigitNotNull,
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
            'timesReferred' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'invoiceBeforeGRVYN' => $fake->randomDigitNotNull,
            'deliveryConfirmedYN' => $fake->randomDigitNotNull,
            'interCompanyTransferYN' => $fake->randomDigitNotNull,
            'FromCompanyID' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $gRVMasterFields);
    }
}
