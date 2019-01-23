<?php

use Faker\Factory as Faker;
use App\Models\QuotationMaster;
use App\Repositories\QuotationMasterRepository;

trait MakeQuotationMasterTrait
{
    /**
     * Create fake instance of QuotationMaster and save it in database
     *
     * @param array $quotationMasterFields
     * @return QuotationMaster
     */
    public function makeQuotationMaster($quotationMasterFields = [])
    {
        /** @var QuotationMasterRepository $quotationMasterRepo */
        $quotationMasterRepo = App::make(QuotationMasterRepository::class);
        $theme = $this->fakeQuotationMasterData($quotationMasterFields);
        return $quotationMasterRepo->create($theme);
    }

    /**
     * Get fake instance of QuotationMaster
     *
     * @param array $quotationMasterFields
     * @return QuotationMaster
     */
    public function fakeQuotationMaster($quotationMasterFields = [])
    {
        return new QuotationMaster($this->fakeQuotationMasterData($quotationMasterFields));
    }

    /**
     * Get fake data of QuotationMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeQuotationMasterData($quotationMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'documentSystemID' => $fake->word,
            'documentID' => $fake->word,
            'documentDate' => $fake->word,
            'documentExpDate' => $fake->word,
            'salesPersonID' => $fake->randomDigitNotNull,
            'versionNo' => $fake->randomDigitNotNull,
            'referenceNo' => $fake->word,
            'narration' => $fake->text,
            'Note' => $fake->text,
            'contactPersonName' => $fake->word,
            'contactPersonNumber' => $fake->word,
            'customerSystemCode' => $fake->randomDigitNotNull,
            'customerCode' => $fake->word,
            'customerName' => $fake->word,
            'customerAddress' => $fake->text,
            'customerTelephone' => $fake->word,
            'customerFax' => $fake->word,
            'customerEmail' => $fake->word,
            'customerReceivableAutoID' => $fake->randomDigitNotNull,
            'customerReceivableSystemGLCode' => $fake->word,
            'customerReceivableGLAccount' => $fake->word,
            'customerReceivableDescription' => $fake->word,
            'customerReceivableType' => $fake->word,
            'transactionCurrencyID' => $fake->randomDigitNotNull,
            'transactionCurrency' => $fake->word,
            'transactionExchangeRate' => $fake->randomDigitNotNull,
            'transactionAmount' => $fake->randomDigitNotNull,
            'transactionCurrencyDecimalPlaces' => $fake->randomDigitNotNull,
            'companyLocalCurrencyID' => $fake->randomDigitNotNull,
            'companyLocalCurrency' => $fake->word,
            'companyLocalExchangeRate' => $fake->randomDigitNotNull,
            'companyLocalAmount' => $fake->randomDigitNotNull,
            'companyLocalCurrencyDecimalPlaces' => $fake->randomDigitNotNull,
            'companyReportingCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingCurrency' => $fake->word,
            'companyReportingExchangeRate' => $fake->randomDigitNotNull,
            'companyReportingAmount' => $fake->randomDigitNotNull,
            'companyReportingCurrencyDecimalPlaces' => $fake->randomDigitNotNull,
            'customerCurrencyID' => $fake->randomDigitNotNull,
            'customerCurrency' => $fake->word,
            'customerCurrencyExchangeRate' => $fake->randomDigitNotNull,
            'customerCurrencyAmount' => $fake->randomDigitNotNull,
            'customerCurrencyDecimalPlaces' => $fake->randomDigitNotNull,
            'isDeleted' => $fake->randomDigitNotNull,
            'deletedEmpID' => $fake->randomDigitNotNull,
            'deletedDate' => $fake->date('Y-m-d H:i:s'),
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedEmpSystemID' => $fake->randomDigitNotNull,
            'approvedbyEmpID' => $fake->word,
            'approvedbyEmpName' => $fake->word,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'closedYN' => $fake->randomDigitNotNull,
            'closedDate' => $fake->date('Y-m-d H:i:s'),
            'closedReason' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'createdUserGroup' => $fake->randomDigitNotNull,
            'createdPCID' => $fake->word,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserName' => $fake->word,
            'modifiedPCID' => $fake->word,
            'modifiedUserID' => $fake->word,
            'modifiedDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedUserName' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $quotationMasterFields);
    }
}
