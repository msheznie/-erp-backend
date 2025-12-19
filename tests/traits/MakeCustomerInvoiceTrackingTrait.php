<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\CustomerInvoiceTracking;
use App\Repositories\CustomerInvoiceTrackingRepository;

trait MakeCustomerInvoiceTrackingTrait
{
    /**
     * Create fake instance of CustomerInvoiceTracking and save it in database
     *
     * @param array $customerInvoiceTrackingFields
     * @return CustomerInvoiceTracking
     */
    public function makeCustomerInvoiceTracking($customerInvoiceTrackingFields = [])
    {
        /** @var CustomerInvoiceTrackingRepository $customerInvoiceTrackingRepo */
        $customerInvoiceTrackingRepo = \App::make(CustomerInvoiceTrackingRepository::class);
        $theme = $this->fakeCustomerInvoiceTrackingData($customerInvoiceTrackingFields);
        return $customerInvoiceTrackingRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerInvoiceTracking
     *
     * @param array $customerInvoiceTrackingFields
     * @return CustomerInvoiceTracking
     */
    public function fakeCustomerInvoiceTracking($customerInvoiceTrackingFields = [])
    {
        return new CustomerInvoiceTracking($this->fakeCustomerInvoiceTrackingData($customerInvoiceTrackingFields));
    }

    /**
     * Get fake data of CustomerInvoiceTracking
     *
     * @param array $customerInvoiceTrackingFields
     * @return array
     */
    public function fakeCustomerInvoiceTrackingData($customerInvoiceTrackingFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'documentID' => $fake->word,
            'companyID' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'FYBiggin' => $fake->date('Y-m-d H:i:s'),
            'FYEnd' => $fake->date('Y-m-d H:i:s'),
            'companyFinancePeriodID' => $fake->randomDigitNotNull,
            'FYPeriodDateFrom' => $fake->date('Y-m-d H:i:s'),
            'FYPeriodDateTo' => $fake->date('Y-m-d H:i:s'),
            'customerInvoiceTrackingCode' => $fake->word,
            'manualTrackingNo' => $fake->word,
            'customerID' => $fake->randomDigitNotNull,
            'contractNumber' => $fake->word,
            'serviceLineCode' => $fake->word,
            'comments' => $fake->text,
            'approvalType' => $fake->randomDigitNotNull,
            'submittedYN' => $fake->randomDigitNotNull,
            'submittedEmpID' => $fake->word,
            'submittedEmpName' => $fake->word,
            'submittedDate' => $fake->date('Y-m-d H:i:s'),
            'submittedYear' => $fake->randomDigitNotNull,
            'closeYN' => $fake->randomDigitNotNull,
            'closedByEmpID' => $fake->word,
            'closedByEmpName' => $fake->word,
            'closedDate' => $fake->date('Y-m-d H:i:s'),
            'totalBatchAmount' => $fake->randomDigitNotNull,
            'totalApprovedAmount' => $fake->randomDigitNotNull,
            'totalRejectedAmount' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $customerInvoiceTrackingFields);
    }
}
