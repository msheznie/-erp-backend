<?php

use Faker\Factory as Faker;
use App\Models\FreeBillingMasterPerforma;
use App\Repositories\FreeBillingMasterPerformaRepository;

trait MakeFreeBillingMasterPerformaTrait
{
    /**
     * Create fake instance of FreeBillingMasterPerforma and save it in database
     *
     * @param array $freeBillingMasterPerformaFields
     * @return FreeBillingMasterPerforma
     */
    public function makeFreeBillingMasterPerforma($freeBillingMasterPerformaFields = [])
    {
        /** @var FreeBillingMasterPerformaRepository $freeBillingMasterPerformaRepo */
        $freeBillingMasterPerformaRepo = App::make(FreeBillingMasterPerformaRepository::class);
        $theme = $this->fakeFreeBillingMasterPerformaData($freeBillingMasterPerformaFields);
        return $freeBillingMasterPerformaRepo->create($theme);
    }

    /**
     * Get fake instance of FreeBillingMasterPerforma
     *
     * @param array $freeBillingMasterPerformaFields
     * @return FreeBillingMasterPerforma
     */
    public function fakeFreeBillingMasterPerforma($freeBillingMasterPerformaFields = [])
    {
        return new FreeBillingMasterPerforma($this->fakeFreeBillingMasterPerformaData($freeBillingMasterPerformaFields));
    }

    /**
     * Get fake data of FreeBillingMasterPerforma
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFreeBillingMasterPerformaData($freeBillingMasterPerformaFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'BillProcessNO' => $fake->randomDigitNotNull,
            'PerformaInvoiceNo' => $fake->randomDigitNotNull,
            'PerformaInvoiceText' => $fake->word,
            'Ticketno' => $fake->randomDigitNotNull,
            'clientID' => $fake->word,
            'contractID' => $fake->word,
            'performaDate' => $fake->date('Y-m-d H:i:s'),
            'performaStatus' => $fake->randomDigitNotNull,
            'BillProcessDate' => $fake->word,
            'SelectedForPerformaYN' => $fake->randomDigitNotNull,
            'InvoiceNo' => $fake->word,
            'PerformaOpConfirmed' => $fake->randomDigitNotNull,
            'PerformaFinanceConfirmed' => $fake->randomDigitNotNull,
            'performaOpConfirmedBy' => $fake->word,
            'performaOpConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'performaFinanceConfirmedBy' => $fake->word,
            'performaFinanceConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedBy' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'confirmedByName' => $fake->word,
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedBy' => $fake->word,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'documentID' => $fake->word,
            'companyID' => $fake->word,
            'serviceLineCode' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'billingCode' => $fake->word,
            'performaSerialNo' => $fake->randomDigitNotNull,
            'performaCode' => $fake->word,
            'rentalStartDate' => $fake->date('Y-m-d H:i:s'),
            'rentalEndDate' => $fake->date('Y-m-d H:i:s'),
            'rentalType' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'modifiedUserID' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s'),
            'performaMasterID' => $fake->randomDigitNotNull,
            'isTrasportRental' => $fake->randomDigitNotNull,
            'disableRental' => $fake->randomDigitNotNull,
            'IsOpStbDaysFromMIT' => $fake->randomDigitNotNull
        ], $freeBillingMasterPerformaFields);
    }
}
