<?php

use Faker\Factory as Faker;
use App\Models\PurchaseOrderProcessDetails;
use App\Repositories\PurchaseOrderProcessDetailsRepository;

trait MakePurchaseOrderProcessDetailsTrait
{
    /**
     * Create fake instance of PurchaseOrderProcessDetails and save it in database
     *
     * @param array $purchaseOrderProcessDetailsFields
     * @return PurchaseOrderProcessDetails
     */
    public function makePurchaseOrderProcessDetails($purchaseOrderProcessDetailsFields = [])
    {
        /** @var PurchaseOrderProcessDetailsRepository $purchaseOrderProcessDetailsRepo */
        $purchaseOrderProcessDetailsRepo = App::make(PurchaseOrderProcessDetailsRepository::class);
        $theme = $this->fakePurchaseOrderProcessDetailsData($purchaseOrderProcessDetailsFields);
        return $purchaseOrderProcessDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of PurchaseOrderProcessDetails
     *
     * @param array $purchaseOrderProcessDetailsFields
     * @return PurchaseOrderProcessDetails
     */
    public function fakePurchaseOrderProcessDetails($purchaseOrderProcessDetailsFields = [])
    {
        return new PurchaseOrderProcessDetails($this->fakePurchaseOrderProcessDetailsData($purchaseOrderProcessDetailsFields));
    }

    /**
     * Get fake data of PurchaseOrderProcessDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakePurchaseOrderProcessDetailsData($purchaseOrderProcessDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'POProcessMasterID' => $fake->randomDigitNotNull,
            'purchaseRequestID' => $fake->randomDigitNotNull,
            'purchaseRequestDetailsID' => $fake->randomDigitNotNull,
            'poDeliveryLocation' => $fake->randomDigitNotNull,
            'itemCode' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->text,
            'unitOfMeasure' => $fake->randomDigitNotNull,
            'comments' => $fake->text,
            'quantityRequested' => $fake->randomDigitNotNull,
            'orderedQty' => $fake->randomDigitNotNull,
            'supplierPOqty' => $fake->randomDigitNotNull,
            'supplierCost' => $fake->randomDigitNotNull,
            'selectedSupplier' => $fake->randomDigitNotNull,
            'catalogueMasterID' => $fake->randomDigitNotNull,
            'catalogueDetailID' => $fake->randomDigitNotNull,
            'partNumber' => $fake->word,
            'itemClientReferenceNumberMasterID' => $fake->randomDigitNotNull,
            'clientReferenceNumber' => $fake->word,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingER' => $fake->randomDigitNotNull,
            'selectedForPO' => $fake->randomDigitNotNull,
            'itemFinanceCategoryID' => $fake->randomDigitNotNull,
            'itemFinanceCategorySubID' => $fake->randomDigitNotNull,
            'financeGLcodebBSSystemID' => $fake->randomDigitNotNull,
            'financeGLcodebBS' => $fake->word,
            'financeGLcodePLSystemID' => $fake->randomDigitNotNull,
            'financeGLcodePL' => $fake->word,
            'includePLForGRVYN' => $fake->randomDigitNotNull,
            'isAccrued' => $fake->randomDigitNotNull,
            'budgetYear' => $fake->randomDigitNotNull,
            'prBelongsYear' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $purchaseOrderProcessDetailsFields);
    }
}
