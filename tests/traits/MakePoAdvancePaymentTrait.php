<?php

use Faker\Factory as Faker;
use App\Models\PoAdvancePayment;
use App\Repositories\PoAdvancePaymentRepository;

trait MakePoAdvancePaymentTrait
{
    /**
     * Create fake instance of PoAdvancePayment and save it in database
     *
     * @param array $poAdvancePaymentFields
     * @return PoAdvancePayment
     */
    public function makePoAdvancePayment($poAdvancePaymentFields = [])
    {
        /** @var PoAdvancePaymentRepository $poAdvancePaymentRepo */
        $poAdvancePaymentRepo = App::make(PoAdvancePaymentRepository::class);
        $theme = $this->fakePoAdvancePaymentData($poAdvancePaymentFields);
        return $poAdvancePaymentRepo->create($theme);
    }

    /**
     * Get fake instance of PoAdvancePayment
     *
     * @param array $poAdvancePaymentFields
     * @return PoAdvancePayment
     */
    public function fakePoAdvancePayment($poAdvancePaymentFields = [])
    {
        return new PoAdvancePayment($this->fakePoAdvancePaymentData($poAdvancePaymentFields));
    }

    /**
     * Get fake data of PoAdvancePayment
     *
     * @param array $postFields
     * @return array
     */
    public function fakePoAdvancePaymentData($poAdvancePaymentFields = [])
    {
        $fake = Faker::create();

        return array_merge([
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
            'narration' => $fake->text,
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
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $poAdvancePaymentFields);
    }
}
