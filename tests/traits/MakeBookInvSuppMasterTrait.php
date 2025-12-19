<?php

use Faker\Factory as Faker;
use App\Models\BookInvSuppMaster;
use App\Repositories\BookInvSuppMasterRepository;

trait MakeBookInvSuppMasterTrait
{
    /**
     * Create fake instance of BookInvSuppMaster and save it in database
     *
     * @param array $bookInvSuppMasterFields
     * @return BookInvSuppMaster
     */
    public function makeBookInvSuppMaster($bookInvSuppMasterFields = [])
    {
        /** @var BookInvSuppMasterRepository $bookInvSuppMasterRepo */
        $bookInvSuppMasterRepo = App::make(BookInvSuppMasterRepository::class);
        $theme = $this->fakeBookInvSuppMasterData($bookInvSuppMasterFields);
        return $bookInvSuppMasterRepo->create($theme);
    }

    /**
     * Get fake instance of BookInvSuppMaster
     *
     * @param array $bookInvSuppMasterFields
     * @return BookInvSuppMaster
     */
    public function fakeBookInvSuppMaster($bookInvSuppMasterFields = [])
    {
        return new BookInvSuppMaster($this->fakeBookInvSuppMasterData($bookInvSuppMasterFields));
    }

    /**
     * Get fake data of BookInvSuppMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBookInvSuppMasterData($bookInvSuppMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'FYBiggin' => $fake->date('Y-m-d H:i:s'),
            'FYEnd' => $fake->date('Y-m-d H:i:s'),
            'companyFinancePeriodID' => $fake->randomDigitNotNull,
            'FYPeriodDateFrom' => $fake->date('Y-m-d H:i:s'),
            'FYPeriodDateTo' => $fake->date('Y-m-d H:i:s'),
            'bookingInvCode' => $fake->word,
            'bookingDate' => $fake->date('Y-m-d H:i:s'),
            'comments' => $fake->text,
            'secondaryRefNo' => $fake->word,
            'supplierID' => $fake->randomDigitNotNull,
            'supplierGLCode' => $fake->word,
            'supplierInvoiceNo' => $fake->word,
            'supplierInvoiceDate' => $fake->date('Y-m-d H:i:s'),
            'supplierTransactionCurrencyID' => $fake->randomDigitNotNull,
            'supplierTransactionCurrencyER' => $fake->randomDigitNotNull,
            'companyReportingCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingER' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'bookingAmountTrans' => $fake->randomDigitNotNull,
            'bookingAmountLocal' => $fake->randomDigitNotNull,
            'bookingAmountRpt' => $fake->randomDigitNotNull,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'postedDate' => $fake->date('Y-m-d H:i:s'),
            'documentType' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'interCompanyTransferYN' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->word,
            'cancelYN' => $fake->randomDigitNotNull,
            'cancelComment' => $fake->text,
            'cancelDate' => $fake->date('Y-m-d H:i:s'),
            'canceledByEmpSystemID' => $fake->randomDigitNotNull,
            'canceledByEmpID' => $fake->word,
            'canceledByEmpName' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $bookInvSuppMasterFields);
    }
}
