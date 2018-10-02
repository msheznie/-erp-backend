<?php

use Faker\Factory as Faker;
use App\Models\PaymentBankTransfer;
use App\Repositories\PaymentBankTransferRepository;

trait MakePaymentBankTransferTrait
{
    /**
     * Create fake instance of PaymentBankTransfer and save it in database
     *
     * @param array $paymentBankTransferFields
     * @return PaymentBankTransfer
     */
    public function makePaymentBankTransfer($paymentBankTransferFields = [])
    {
        /** @var PaymentBankTransferRepository $paymentBankTransferRepo */
        $paymentBankTransferRepo = App::make(PaymentBankTransferRepository::class);
        $theme = $this->fakePaymentBankTransferData($paymentBankTransferFields);
        return $paymentBankTransferRepo->create($theme);
    }

    /**
     * Get fake instance of PaymentBankTransfer
     *
     * @param array $paymentBankTransferFields
     * @return PaymentBankTransfer
     */
    public function fakePaymentBankTransfer($paymentBankTransferFields = [])
    {
        return new PaymentBankTransfer($this->fakePaymentBankTransferData($paymentBankTransferFields));
    }

    /**
     * Get fake data of PaymentBankTransfer
     *
     * @param array $postFields
     * @return array
     */
    public function fakePaymentBankTransferData($paymentBankTransferFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'bankTransferDocumentCode' => $fake->word,
            'serialNumber' => $fake->randomDigitNotNull,
            'documentDate' => $fake->date('Y-m-d H:i:s'),
            'bankMasterID' => $fake->randomDigitNotNull,
            'bankAccountAutoID' => $fake->randomDigitNotNull,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedByUserID' => $fake->word,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'createdPcID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $paymentBankTransferFields);
    }
}
