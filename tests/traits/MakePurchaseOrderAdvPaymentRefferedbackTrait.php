<?php

use Faker\Factory as Faker;
use App\Models\PurchaseOrderAdvPaymentRefferedback;
use App\Repositories\PurchaseOrderAdvPaymentRefferedbackRepository;

trait MakePurchaseOrderAdvPaymentRefferedbackTrait
{
    /**
     * Create fake instance of PurchaseOrderAdvPaymentRefferedback and save it in database
     *
     * @param array $purchaseOrderAdvPaymentRefferedbackFields
     * @return PurchaseOrderAdvPaymentRefferedback
     */
    public function makePurchaseOrderAdvPaymentRefferedback($purchaseOrderAdvPaymentRefferedbackFields = [])
    {
        /** @var PurchaseOrderAdvPaymentRefferedbackRepository $purchaseOrderAdvPaymentRefferedbackRepo */
        $purchaseOrderAdvPaymentRefferedbackRepo = App::make(PurchaseOrderAdvPaymentRefferedbackRepository::class);
        $theme = $this->fakePurchaseOrderAdvPaymentRefferedbackData($purchaseOrderAdvPaymentRefferedbackFields);
        return $purchaseOrderAdvPaymentRefferedbackRepo->create($theme);
    }

    /**
     * Get fake instance of PurchaseOrderAdvPaymentRefferedback
     *
     * @param array $purchaseOrderAdvPaymentRefferedbackFields
     * @return PurchaseOrderAdvPaymentRefferedback
     */
    public function fakePurchaseOrderAdvPaymentRefferedback($purchaseOrderAdvPaymentRefferedbackFields = [])
    {
        return new PurchaseOrderAdvPaymentRefferedback($this->fakePurchaseOrderAdvPaymentRefferedbackData($purchaseOrderAdvPaymentRefferedbackFields));
    }

    /**
     * Get fake data of PurchaseOrderAdvPaymentRefferedback
     *
     * @param array $postFields
     * @return array
     */
    public function fakePurchaseOrderAdvPaymentRefferedbackData($purchaseOrderAdvPaymentRefferedbackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'poAdvPaymentID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineID' => $fake->word,
            'poID' => $fake->randomDigitNotNull,
            'grvAutoID' => $fake->randomDigitNotNull,
            'poCode' => $fake->word,
            'poTermID' => $fake->randomDigitNotNull,
            'supplierID' => $fake->randomDigitNotNull,
            'SupplierPrimaryCode' => $fake->word,
            'reqDate' => $fake->date('Y-m-d H:i:s'),
            'narration' => $fake->word,
            'currencyID' => $fake->randomDigitNotNull,
            'reqAmount' => $fake->randomDigitNotNull,
            'reqAmountTransCur_amount' => $fake->randomDigitNotNull,
            'confirmedYN' => $fake->randomDigitNotNull,
            'approvedYN' => $fake->randomDigitNotNull,
            'selectedToPayment' => $fake->randomDigitNotNull,
            'fullyPaid' => $fake->randomDigitNotNull,
            'isAdvancePaymentYN' => $fake->randomDigitNotNull,
            'dueDate' => $fake->date('Y-m-d H:i:s'),
            'LCPaymentYN' => $fake->randomDigitNotNull,
            'requestedByEmpID' => $fake->word,
            'requestedByEmpName' => $fake->word,
            'reqAmountInPOTransCur' => $fake->randomDigitNotNull,
            'reqAmountInPOLocalCur' => $fake->randomDigitNotNull,
            'reqAmountInPORptCur' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $purchaseOrderAdvPaymentRefferedbackFields);
    }
}
