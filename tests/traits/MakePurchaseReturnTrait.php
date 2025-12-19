<?php

use Faker\Factory as Faker;
use App\Models\PurchaseReturn;
use App\Repositories\PurchaseReturnRepository;

trait MakePurchaseReturnTrait
{
    /**
     * Create fake instance of PurchaseReturn and save it in database
     *
     * @param array $purchaseReturnFields
     * @return PurchaseReturn
     */
    public function makePurchaseReturn($purchaseReturnFields = [])
    {
        /** @var PurchaseReturnRepository $purchaseReturnRepo */
        $purchaseReturnRepo = App::make(PurchaseReturnRepository::class);
        $theme = $this->fakePurchaseReturnData($purchaseReturnFields);
        return $purchaseReturnRepo->create($theme);
    }

    /**
     * Get fake instance of PurchaseReturn
     *
     * @param array $purchaseReturnFields
     * @return PurchaseReturn
     */
    public function fakePurchaseReturn($purchaseReturnFields = [])
    {
        return new PurchaseReturn($this->fakePurchaseReturnData($purchaseReturnFields));
    }

    /**
     * Get fake data of PurchaseReturn
     *
     * @param array $postFields
     * @return array
     */
    public function fakePurchaseReturnData($purchaseReturnFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'companyFinancePeriodID' => $fake->randomDigitNotNull,
            'FYBiggin' => $fake->date('Y-m-d H:i:s'),
            'FYEnd' => $fake->date('Y-m-d H:i:s'),
            'serialNo' => $fake->randomDigitNotNull,
            'purchaseReturnDate' => $fake->date('Y-m-d H:i:s'),
            'purchaseReturnCode' => $fake->word,
            'purchaseReturnRefNo' => $fake->word,
            'narration' => $fake->text,
            'purchaseReturnLocation' => $fake->randomDigitNotNull,
            'supplierID' => $fake->randomDigitNotNull,
            'supplierPrimaryCode' => $fake->word,
            'supplierName' => $fake->text,
            'supplierDefaultCurrencyID' => $fake->randomDigitNotNull,
            'supplierDefaultER' => $fake->randomDigitNotNull,
            'supplierTransactionCurrencyID' => $fake->randomDigitNotNull,
            'supplierTransactionER' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'companyReportingCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingER' => $fake->randomDigitNotNull,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'totalSupplierDefaultAmount' => $fake->randomDigitNotNull,
            'totalSupplierTransactionAmount' => $fake->randomDigitNotNull,
            'totalLocalAmount' => $fake->randomDigitNotNull,
            'totalComRptAmount' => $fake->randomDigitNotNull,
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedByUserID' => $fake->word,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $purchaseReturnFields);
    }
}
