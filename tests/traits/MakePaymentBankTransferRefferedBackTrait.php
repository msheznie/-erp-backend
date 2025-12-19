<?php

use Faker\Factory as Faker;
use App\Models\PaymentBankTransferRefferedBack;
use App\Repositories\PaymentBankTransferRefferedBackRepository;

trait MakePaymentBankTransferRefferedBackTrait
{
    /**
     * Create fake instance of PaymentBankTransferRefferedBack and save it in database
     *
     * @param array $paymentBankTransferRefferedBackFields
     * @return PaymentBankTransferRefferedBack
     */
    public function makePaymentBankTransferRefferedBack($paymentBankTransferRefferedBackFields = [])
    {
        /** @var PaymentBankTransferRefferedBackRepository $paymentBankTransferRefferedBackRepo */
        $paymentBankTransferRefferedBackRepo = App::make(PaymentBankTransferRefferedBackRepository::class);
        $theme = $this->fakePaymentBankTransferRefferedBackData($paymentBankTransferRefferedBackFields);
        return $paymentBankTransferRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of PaymentBankTransferRefferedBack
     *
     * @param array $paymentBankTransferRefferedBackFields
     * @return PaymentBankTransferRefferedBack
     */
    public function fakePaymentBankTransferRefferedBack($paymentBankTransferRefferedBackFields = [])
    {
        return new PaymentBankTransferRefferedBack($this->fakePaymentBankTransferRefferedBackData($paymentBankTransferRefferedBackFields));
    }

    /**
     * Get fake data of PaymentBankTransferRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakePaymentBankTransferRefferedBackData($paymentBankTransferRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'paymentBankTransferID' => $fake->randomDigitNotNull,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'bankTransferDocumentCode' => $fake->word,
            'serialNumber' => $fake->randomDigitNotNull,
            'documentDate' => $fake->date('Y-m-d H:i:s'),
            'narration' => $fake->text,
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
            'refferedBackYN' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'createdPcID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s'),
            'exportedYN' => $fake->randomDigitNotNull,
            'exportedUserSystemID' => $fake->randomDigitNotNull,
            'exportedDate' => $fake->date('Y-m-d H:i:s')
        ], $paymentBankTransferRefferedBackFields);
    }
}
