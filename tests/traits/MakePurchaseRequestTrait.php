<?php

use Faker\Factory as Faker;
use App\Models\PurchaseRequest;
use App\Repositories\PurchaseRequestRepository;

trait MakePurchaseRequestTrait
{
    /**
     * Create fake instance of PurchaseRequest and save it in database
     *
     * @param array $purchaseRequestFields
     * @return PurchaseRequest
     */
    public function makePurchaseRequest($purchaseRequestFields = [])
    {
        /** @var PurchaseRequestRepository $purchaseRequestRepo */
        $purchaseRequestRepo = App::make(PurchaseRequestRepository::class);
        $theme = $this->fakePurchaseRequestData($purchaseRequestFields);
        return $purchaseRequestRepo->create($theme);
    }

    /**
     * Get fake instance of PurchaseRequest
     *
     * @param array $purchaseRequestFields
     * @return PurchaseRequest
     */
    public function fakePurchaseRequest($purchaseRequestFields = [])
    {
        return new PurchaseRequest($this->fakePurchaseRequestData($purchaseRequestFields));
    }

    /**
     * Get fake data of PurchaseRequest
     *
     * @param array $postFields
     * @return array
     */
    public function fakePurchaseRequestData($purchaseRequestFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'departmentID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'companyJobID' => $fake->randomDigitNotNull,
            'serialNumber' => $fake->randomDigitNotNull,
            'purchaseRequestCode' => $fake->word,
            'comments' => $fake->text,
            'location' => $fake->randomDigitNotNull,
            'priority' => $fake->randomDigitNotNull,
            'deliveryLocation' => $fake->randomDigitNotNull,
            'PRRequestedDate' => $fake->date('Y-m-d H:i:s'),
            'docRefNo' => $fake->word,
            'invoiceNumber' => $fake->word,
            'currency' => $fake->randomDigitNotNull,
            'buyerEmpID' => $fake->word,
            'buyerEmpSystemID' => $fake->randomDigitNotNull,
            'buyerEmpName' => $fake->word,
            'buyerEmpEmail' => $fake->word,
            'supplierCodeSystem' => $fake->randomDigitNotNull,
            'supplierName' => $fake->word,
            'supplierAddress' => $fake->word,
            'supplierTransactionCurrencyID' => $fake->randomDigitNotNull,
            'supplierCountryID' => $fake->word,
            'financeCategory' => $fake->randomDigitNotNull,
            'PRConfirmedYN' => $fake->randomDigitNotNull,
            'PRConfirmedBy' => $fake->word,
            'PRConfirmedBySystemID' => $fake->randomDigitNotNull,
            'PRConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'isActive' => $fake->randomDigitNotNull,
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'timesReferred' => $fake->randomDigitNotNull,
            'prClosedYN' => $fake->randomDigitNotNull,
            'prClosedComments' => $fake->text,
            'prClosedByEmpID' => $fake->word,
            'prClosedDate' => $fake->date('Y-m-d H:i:s'),
            'cancelledYN' => $fake->randomDigitNotNull,
            'cancelledByEmpID' => $fake->word,
            'cancelledByEmpName' => $fake->word,
            'cancelledComments' => $fake->text,
            'cancelledDate' => $fake->date('Y-m-d H:i:s'),
            'selectedForPO' => $fake->randomDigitNotNull,
            'selectedForPOByEmpID' => $fake->word,
            'supplyChainOnGoing' => $fake->randomDigitNotNull,
            'poTrackID' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'hidePOYN' => $fake->randomDigitNotNull,
            'hideByEmpID' => $fake->word,
            'hideByEmpName' => $fake->word,
            'hideDate' => $fake->date('Y-m-d H:i:s'),
            'hideComments' => $fake->text,
            'PreviousBuyerEmpID' => $fake->word,
            'delegatedDate' => $fake->date('Y-m-d H:i:s'),
            'delegatedComments' => $fake->text,
            'fromWeb' => $fake->randomDigitNotNull,
            'wo_status' => $fake->randomDigitNotNull,
            'doc_type' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->randomDigitNotNull,
            'isAccrued' => $fake->randomDigitNotNull,
            'budgetYear' => $fake->randomDigitNotNull,
            'prBelongsYear' => $fake->randomDigitNotNull,
            'budgetBlockYN' => $fake->randomDigitNotNull,
            'budgetBlockByEmpID' => $fake->word,
            'budgetBlockByEmpEmailID' => $fake->word,
            'checkBudgetYN' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $purchaseRequestFields);
    }
}
