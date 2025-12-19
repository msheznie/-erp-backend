<?php

use Faker\Factory as Faker;
use App\Models\BookInvSuppMasterRefferedBack;
use App\Repositories\BookInvSuppMasterRefferedBackRepository;

trait MakeBookInvSuppMasterRefferedBackTrait
{
    /**
     * Create fake instance of BookInvSuppMasterRefferedBack and save it in database
     *
     * @param array $bookInvSuppMasterRefferedBackFields
     * @return BookInvSuppMasterRefferedBack
     */
    public function makeBookInvSuppMasterRefferedBack($bookInvSuppMasterRefferedBackFields = [])
    {
        /** @var BookInvSuppMasterRefferedBackRepository $bookInvSuppMasterRefferedBackRepo */
        $bookInvSuppMasterRefferedBackRepo = App::make(BookInvSuppMasterRefferedBackRepository::class);
        $theme = $this->fakeBookInvSuppMasterRefferedBackData($bookInvSuppMasterRefferedBackFields);
        return $bookInvSuppMasterRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of BookInvSuppMasterRefferedBack
     *
     * @param array $bookInvSuppMasterRefferedBackFields
     * @return BookInvSuppMasterRefferedBack
     */
    public function fakeBookInvSuppMasterRefferedBack($bookInvSuppMasterRefferedBackFields = [])
    {
        return new BookInvSuppMasterRefferedBack($this->fakeBookInvSuppMasterRefferedBackData($bookInvSuppMasterRefferedBackFields));
    }

    /**
     * Get fake data of BookInvSuppMasterRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBookInvSuppMasterRefferedBackData($bookInvSuppMasterRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'bookingSuppMasInvAutoID' => $fake->randomDigitNotNull,
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
            'supplierGLCodeSystemID' => $fake->randomDigitNotNull,
            'supplierGLCode' => $fake->word,
            'UnbilledGRVAccountSystemID' => $fake->randomDigitNotNull,
            'UnbilledGRVAccount' => $fake->word,
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
            'approvedByUserID' => $fake->word,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'postedDate' => $fake->date('Y-m-d H:i:s'),
            'documentType' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'interCompanyTransferYN' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->word,
            'createdDateAndTime' => $fake->date('Y-m-d H:i:s'),
            'cancelYN' => $fake->randomDigitNotNull,
            'cancelComment' => $fake->text,
            'cancelDate' => $fake->date('Y-m-d H:i:s'),
            'canceledByEmpSystemID' => $fake->randomDigitNotNull,
            'canceledByEmpID' => $fake->word,
            'canceledByEmpName' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $bookInvSuppMasterRefferedBackFields);
    }
}
