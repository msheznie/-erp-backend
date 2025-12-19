<?php

use Faker\Factory as Faker;
use App\Models\GposInvoicePayments;
use App\Repositories\GposInvoicePaymentsRepository;

trait MakeGposInvoicePaymentsTrait
{
    /**
     * Create fake instance of GposInvoicePayments and save it in database
     *
     * @param array $gposInvoicePaymentsFields
     * @return GposInvoicePayments
     */
    public function makeGposInvoicePayments($gposInvoicePaymentsFields = [])
    {
        /** @var GposInvoicePaymentsRepository $gposInvoicePaymentsRepo */
        $gposInvoicePaymentsRepo = App::make(GposInvoicePaymentsRepository::class);
        $theme = $this->fakeGposInvoicePaymentsData($gposInvoicePaymentsFields);
        return $gposInvoicePaymentsRepo->create($theme);
    }

    /**
     * Get fake instance of GposInvoicePayments
     *
     * @param array $gposInvoicePaymentsFields
     * @return GposInvoicePayments
     */
    public function fakeGposInvoicePayments($gposInvoicePaymentsFields = [])
    {
        return new GposInvoicePayments($this->fakeGposInvoicePaymentsData($gposInvoicePaymentsFields));
    }

    /**
     * Get fake data of GposInvoicePayments
     *
     * @param array $postFields
     * @return array
     */
    public function fakeGposInvoicePaymentsData($gposInvoicePaymentsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'invoiceID' => $fake->randomDigitNotNull,
            'paymentConfigMasterID' => $fake->randomDigitNotNull,
            'paymentConfigDetailID' => $fake->randomDigitNotNull,
            'glAccountType' => $fake->randomDigitNotNull,
            'GLCode' => $fake->randomDigitNotNull,
            'amount' => $fake->randomDigitNotNull,
            'reference' => $fake->word,
            'customerAutoID' => $fake->randomDigitNotNull,
            'isAdvancePayment' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->randomDigitNotNull,
            'createdPCID' => $fake->word,
            'createdUserID' => $fake->word,
            'createdUserName' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedPCID' => $fake->word,
            'modifiedUserID' => $fake->word,
            'modifiedUserName' => $fake->word,
            'modifiedDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $gposInvoicePaymentsFields);
    }
}
