<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\CustomerInvoiceTrackingDetail;
use App\Repositories\CustomerInvoiceTrackingDetailRepository;

trait MakeCustomerInvoiceTrackingDetailTrait
{
    /**
     * Create fake instance of CustomerInvoiceTrackingDetail and save it in database
     *
     * @param array $customerInvoiceTrackingDetailFields
     * @return CustomerInvoiceTrackingDetail
     */
    public function makeCustomerInvoiceTrackingDetail($customerInvoiceTrackingDetailFields = [])
    {
        /** @var CustomerInvoiceTrackingDetailRepository $customerInvoiceTrackingDetailRepo */
        $customerInvoiceTrackingDetailRepo = \App::make(CustomerInvoiceTrackingDetailRepository::class);
        $theme = $this->fakeCustomerInvoiceTrackingDetailData($customerInvoiceTrackingDetailFields);
        return $customerInvoiceTrackingDetailRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerInvoiceTrackingDetail
     *
     * @param array $customerInvoiceTrackingDetailFields
     * @return CustomerInvoiceTrackingDetail
     */
    public function fakeCustomerInvoiceTrackingDetail($customerInvoiceTrackingDetailFields = [])
    {
        return new CustomerInvoiceTrackingDetail($this->fakeCustomerInvoiceTrackingDetailData($customerInvoiceTrackingDetailFields));
    }

    /**
     * Get fake data of CustomerInvoiceTrackingDetail
     *
     * @param array $customerInvoiceTrackingDetailFields
     * @return array
     */
    public function fakeCustomerInvoiceTrackingDetailData($customerInvoiceTrackingDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'customerInvoiceTrackingID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'customerID' => $fake->randomDigitNotNull,
            'custInvoiceDirectAutoID' => $fake->randomDigitNotNull,
            'bookingInvCode' => $fake->word,
            'bookingDate' => $fake->date('Y-m-d H:i:s'),
            'customerInvoiceNo' => $fake->word,
            'customerInvoiceDate' => $fake->date('Y-m-d H:i:s'),
            'invoiceDueDate' => $fake->date('Y-m-d H:i:s'),
            'contractID' => $fake->word,
            'PerformaInvoiceNo' => $fake->randomDigitNotNull,
            'wanNO' => $fake->word,
            'PONumber' => $fake->word,
            'rigNo' => $fake->word,
            'wellNo' => $fake->word,
            'amount' => $fake->randomDigitNotNull,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'customerApprovedYN' => $fake->randomDigitNotNull,
            'customerApprovedDate' => $fake->date('Y-m-d H:i:s'),
            'customerApprovedByEmpID' => $fake->word,
            'customerApprovedByEmpSystemID' => $fake->randomDigitNotNull,
            'customerApprovedByEmpName' => $fake->word,
            'customerApprovedByDate' => $fake->word,
            'approvedAmount' => $fake->randomDigitNotNull,
            'customerRejectedYN' => $fake->randomDigitNotNull,
            'customerRejectedDate' => $fake->date('Y-m-d H:i:s'),
            'customerRejectedByEmpID' => $fake->word,
            'customerRejectedByEmpSystemID' => $fake->randomDigitNotNull,
            'customerRejectedByEmpName' => $fake->word,
            'customerRejectedByDate' => $fake->date('Y-m-d H:i:s'),
            'rejectedAmount' => $fake->randomDigitNotNull,
            'remarks' => $fake->text,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $customerInvoiceTrackingDetailFields);
    }
}
