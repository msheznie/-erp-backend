<?php

use Faker\Factory as Faker;
use App\Models\PurchaseReturnDetails;
use App\Repositories\PurchaseReturnDetailsRepository;

trait MakePurchaseReturnDetailsTrait
{
    /**
     * Create fake instance of PurchaseReturnDetails and save it in database
     *
     * @param array $purchaseReturnDetailsFields
     * @return PurchaseReturnDetails
     */
    public function makePurchaseReturnDetails($purchaseReturnDetailsFields = [])
    {
        /** @var PurchaseReturnDetailsRepository $purchaseReturnDetailsRepo */
        $purchaseReturnDetailsRepo = App::make(PurchaseReturnDetailsRepository::class);
        $theme = $this->fakePurchaseReturnDetailsData($purchaseReturnDetailsFields);
        return $purchaseReturnDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of PurchaseReturnDetails
     *
     * @param array $purchaseReturnDetailsFields
     * @return PurchaseReturnDetails
     */
    public function fakePurchaseReturnDetails($purchaseReturnDetailsFields = [])
    {
        return new PurchaseReturnDetails($this->fakePurchaseReturnDetailsData($purchaseReturnDetailsFields));
    }

    /**
     * Get fake data of PurchaseReturnDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakePurchaseReturnDetailsData($purchaseReturnDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'purhaseReturnAutoID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'grvAutoID' => $fake->randomDigitNotNull,
            'grvDetailsID' => $fake->randomDigitNotNull,
            'itemCode' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->text,
            'supplierPartNumber' => $fake->word,
            'unitOfMeasure' => $fake->randomDigitNotNull,
            'GRVQty' => $fake->randomDigitNotNull,
            'comment' => $fake->text,
            'noQty' => $fake->randomDigitNotNull,
            'supplierDefaultCurrencyID' => $fake->randomDigitNotNull,
            'supplierDefaultER' => $fake->randomDigitNotNull,
            'supplierTransactionCurrencyID' => $fake->randomDigitNotNull,
            'supplierTransactionER' => $fake->randomDigitNotNull,
            'companyReportingCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingER' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'GRVcostPerUnitLocalCur' => $fake->randomDigitNotNull,
            'GRVcostPerUnitSupDefaultCur' => $fake->randomDigitNotNull,
            'GRVcostPerUnitSupTransCur' => $fake->randomDigitNotNull,
            'GRVcostPerUnitComRptCur' => $fake->randomDigitNotNull,
            'netAmount' => $fake->randomDigitNotNull,
            'netAmountLocal' => $fake->randomDigitNotNull,
            'netAmountRpt' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $purchaseReturnDetailsFields);
    }
}
