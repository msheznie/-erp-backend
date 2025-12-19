<?php

use Faker\Factory as Faker;
use App\Models\PurchaseRequestReferred;
use App\Repositories\PurchaseRequestReferredRepository;

trait MakePurchaseRequestReferredTrait
{
    /**
     * Create fake instance of PurchaseRequestReferred and save it in database
     *
     * @param array $purchaseRequestReferredFields
     * @return PurchaseRequestReferred
     */
    public function makePurchaseRequestReferred($purchaseRequestReferredFields = [])
    {
        /** @var PurchaseRequestReferredRepository $purchaseRequestReferredRepo */
        $purchaseRequestReferredRepo = App::make(PurchaseRequestReferredRepository::class);
        $theme = $this->fakePurchaseRequestReferredData($purchaseRequestReferredFields);
        return $purchaseRequestReferredRepo->create($theme);
    }

    /**
     * Get fake instance of PurchaseRequestReferred
     *
     * @param array $purchaseRequestReferredFields
     * @return PurchaseRequestReferred
     */
    public function fakePurchaseRequestReferred($purchaseRequestReferredFields = [])
    {
        return new PurchaseRequestReferred($this->fakePurchaseRequestReferredData($purchaseRequestReferredFields));
    }

    /**
     * Get fake data of PurchaseRequestReferred
     *
     * @param array $postFields
     * @return array
     */
    public function fakePurchaseRequestReferredData($purchaseRequestReferredFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'purchaseRequestID' => $fake->randomDigitNotNull,
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
            'PRConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'isActive' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s'),
            'selectedForPO' => $fake->randomDigitNotNull,
            'approved' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'prClosedYN' => $fake->randomDigitNotNull
        ], $purchaseRequestReferredFields);
    }
}
