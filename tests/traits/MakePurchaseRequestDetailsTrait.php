<?php

use Faker\Factory as Faker;
use App\Models\PurchaseRequestDetails;
use App\Repositories\PurchaseRequestDetailsRepository;

trait MakePurchaseRequestDetailsTrait
{
    /**
     * Create fake instance of PurchaseRequestDetails and save it in database
     *
     * @param array $purchaseRequestDetailsFields
     * @return PurchaseRequestDetails
     */
    public function makePurchaseRequestDetails($purchaseRequestDetailsFields = [])
    {
        /** @var PurchaseRequestDetailsRepository $purchaseRequestDetailsRepo */
        $purchaseRequestDetailsRepo = App::make(PurchaseRequestDetailsRepository::class);
        $theme = $this->fakePurchaseRequestDetailsData($purchaseRequestDetailsFields);
        return $purchaseRequestDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of PurchaseRequestDetails
     *
     * @param array $purchaseRequestDetailsFields
     * @return PurchaseRequestDetails
     */
    public function fakePurchaseRequestDetails($purchaseRequestDetailsFields = [])
    {
        return new PurchaseRequestDetails($this->fakePurchaseRequestDetailsData($purchaseRequestDetailsFields));
    }

    /**
     * Get fake data of PurchaseRequestDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakePurchaseRequestDetailsData($purchaseRequestDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'purchaseRequestID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'itemCategoryID' => $fake->randomDigitNotNull,
            'itemCode' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->text,
            'itemFinanceCategoryID' => $fake->randomDigitNotNull,
            'itemFinanceCategorySubID' => $fake->randomDigitNotNull,
            'financeGLcodebBSSystemID' => $fake->randomDigitNotNull,
            'financeGLcodebBS' => $fake->word,
            'financeGLcodePLSystemID' => $fake->randomDigitNotNull,
            'financeGLcodePL' => $fake->word,
            'includePLForGRVYN' => $fake->randomDigitNotNull,
            'partNumber' => $fake->word,
            'quantityRequested' => $fake->randomDigitNotNull,
            'estimatedCost' => $fake->randomDigitNotNull,
            'totalCost' => $fake->randomDigitNotNull,
            'budgetYear' => $fake->randomDigitNotNull,
            'budjetAmtLocal' => $fake->randomDigitNotNull,
            'budjetAmtRpt' => $fake->randomDigitNotNull,
            'quantityOnOrder' => $fake->randomDigitNotNull,
            'comments' => $fake->text,
            'unitOfMeasure' => $fake->randomDigitNotNull,
            'itemClientReferenceNumberMasterID' => $fake->randomDigitNotNull,
            'clientReferenceNumber' => $fake->word,
            'quantityInHand' => $fake->randomDigitNotNull,
            'maxQty' => $fake->randomDigitNotNull,
            'minQty' => $fake->randomDigitNotNull,
            'poQuantity' => $fake->randomDigitNotNull,
            'specificationGrade' => $fake->word,
            'jobNo' => $fake->word,
            'technicalDataSheetAttachment' => $fake->word,
            'selectedForPO' => $fake->randomDigitNotNull,
            'prClosedYN' => $fake->randomDigitNotNull,
            'fullyOrdered' => $fake->randomDigitNotNull,
            'poTrackingID' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $purchaseRequestDetailsFields);
    }
}
