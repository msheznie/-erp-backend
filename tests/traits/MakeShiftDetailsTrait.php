<?php

use Faker\Factory as Faker;
use App\Models\ShiftDetails;
use App\Repositories\ShiftDetailsRepository;

trait MakeShiftDetailsTrait
{
    /**
     * Create fake instance of ShiftDetails and save it in database
     *
     * @param array $shiftDetailsFields
     * @return ShiftDetails
     */
    public function makeShiftDetails($shiftDetailsFields = [])
    {
        /** @var ShiftDetailsRepository $shiftDetailsRepo */
        $shiftDetailsRepo = App::make(ShiftDetailsRepository::class);
        $theme = $this->fakeShiftDetailsData($shiftDetailsFields);
        return $shiftDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of ShiftDetails
     *
     * @param array $shiftDetailsFields
     * @return ShiftDetails
     */
    public function fakeShiftDetails($shiftDetailsFields = [])
    {
        return new ShiftDetails($this->fakeShiftDetailsData($shiftDetailsFields));
    }

    /**
     * Get fake data of ShiftDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeShiftDetailsData($shiftDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'wareHouseID' => $fake->randomDigitNotNull,
            'empID' => $fake->randomDigitNotNull,
            'counterID' => $fake->randomDigitNotNull,
            'startTime' => $fake->date('Y-m-d H:i:s'),
            'endTime' => $fake->date('Y-m-d H:i:s'),
            'isClosed' => $fake->word,
            'cashSales' => $fake->randomDigitNotNull,
            'giftCardTopUp' => $fake->randomDigitNotNull,
            'startingBalance_transaction' => $fake->randomDigitNotNull,
            'endingBalance_transaction' => $fake->randomDigitNotNull,
            'different_transaction' => $fake->randomDigitNotNull,
            'cashSales_local' => $fake->randomDigitNotNull,
            'giftCardTopUp_local' => $fake->randomDigitNotNull,
            'startingBalance_local' => $fake->randomDigitNotNull,
            'endingBalance_local' => $fake->randomDigitNotNull,
            'different_local' => $fake->randomDigitNotNull,
            'cashSales_reporting' => $fake->randomDigitNotNull,
            'giftCardTopUp_reporting' => $fake->randomDigitNotNull,
            'closingCashBalance_transaction' => $fake->randomDigitNotNull,
            'closingCashBalance_local' => $fake->randomDigitNotNull,
            'startingBalance_reporting' => $fake->randomDigitNotNull,
            'endingBalance_reporting' => $fake->randomDigitNotNull,
            'different_local_reporting' => $fake->randomDigitNotNull,
            'closingCashBalance_reporting' => $fake->randomDigitNotNull,
            'transactionCurrencyID' => $fake->randomDigitNotNull,
            'transactionCurrency' => $fake->word,
            'transactionExchangeRate' => $fake->randomDigitNotNull,
            'transactionCurrencyDecimalPlaces' => $fake->randomDigitNotNull,
            'companyLocalCurrencyID' => $fake->randomDigitNotNull,
            'companyLocalCurrency' => $fake->word,
            'companyLocalExchangeRate' => $fake->randomDigitNotNull,
            'companyLocalCurrencyDecimalPlaces' => $fake->randomDigitNotNull,
            'companyReportingCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingCurrency' => $fake->word,
            'companyReportingExchangeRate' => $fake->randomDigitNotNull,
            'companyReportingCurrencyDecimalPlaces' => $fake->randomDigitNotNull,
            'companyID' => $fake->randomDigitNotNull,
            'companyCode' => $fake->word,
            'segmentID' => $fake->randomDigitNotNull,
            'segmentCode' => $fake->word,
            'createdUserGroup' => $fake->randomDigitNotNull,
            'createdPCID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserName' => $fake->word,
            'modifiedPCID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUserID' => $fake->word,
            'modifiedDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedUserName' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s'),
            'id_store' => $fake->randomDigitNotNull,
            'is_sync' => $fake->randomDigitNotNull
        ], $shiftDetailsFields);
    }
}
