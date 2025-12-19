<?php

use Faker\Factory as Faker;
use App\Models\CustReceivePaymentDetRefferedHistory;
use App\Repositories\CustReceivePaymentDetRefferedHistoryRepository;

trait MakeCustReceivePaymentDetRefferedHistoryTrait
{
    /**
     * Create fake instance of CustReceivePaymentDetRefferedHistory and save it in database
     *
     * @param array $custReceivePaymentDetRefferedHistoryFields
     * @return CustReceivePaymentDetRefferedHistory
     */
    public function makeCustReceivePaymentDetRefferedHistory($custReceivePaymentDetRefferedHistoryFields = [])
    {
        /** @var CustReceivePaymentDetRefferedHistoryRepository $custReceivePaymentDetRefferedHistoryRepo */
        $custReceivePaymentDetRefferedHistoryRepo = App::make(CustReceivePaymentDetRefferedHistoryRepository::class);
        $theme = $this->fakeCustReceivePaymentDetRefferedHistoryData($custReceivePaymentDetRefferedHistoryFields);
        return $custReceivePaymentDetRefferedHistoryRepo->create($theme);
    }

    /**
     * Get fake instance of CustReceivePaymentDetRefferedHistory
     *
     * @param array $custReceivePaymentDetRefferedHistoryFields
     * @return CustReceivePaymentDetRefferedHistory
     */
    public function fakeCustReceivePaymentDetRefferedHistory($custReceivePaymentDetRefferedHistoryFields = [])
    {
        return new CustReceivePaymentDetRefferedHistory($this->fakeCustReceivePaymentDetRefferedHistoryData($custReceivePaymentDetRefferedHistoryFields));
    }

    /**
     * Get fake data of CustReceivePaymentDetRefferedHistory
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCustReceivePaymentDetRefferedHistoryData($custReceivePaymentDetRefferedHistoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'custRecivePayDetAutoID' => $fake->randomDigitNotNull,
            'custReceivePaymentAutoID' => $fake->randomDigitNotNull,
            'arAutoID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'matchingDocID' => $fake->randomDigitNotNull,
            'addedDocumentSystemID' => $fake->randomDigitNotNull,
            'addedDocumentID' => $fake->word,
            'bookingInvCodeSystem' => $fake->randomDigitNotNull,
            'bookingInvCode' => $fake->word,
            'bookingDate' => $fake->date('Y-m-d H:i:s'),
            'comments' => $fake->word,
            'custTransactionCurrencyID' => $fake->randomDigitNotNull,
            'custTransactionCurrencyER' => $fake->randomDigitNotNull,
            'companyReportingCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingER' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'bookingAmountTrans' => $fake->randomDigitNotNull,
            'bookingAmountLocal' => $fake->randomDigitNotNull,
            'bookingAmountRpt' => $fake->randomDigitNotNull,
            'custReceiveCurrencyID' => $fake->randomDigitNotNull,
            'custReceiveCurrencyER' => $fake->randomDigitNotNull,
            'custbalanceAmount' => $fake->randomDigitNotNull,
            'receiveAmountTrans' => $fake->randomDigitNotNull,
            'receiveAmountLocal' => $fake->randomDigitNotNull,
            'receiveAmountRpt' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $custReceivePaymentDetRefferedHistoryFields);
    }
}
