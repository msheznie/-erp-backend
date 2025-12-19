<?php

use Faker\Factory as Faker;
use App\Models\CustomerInvoiceCollectionDetail;
use App\Repositories\CustomerInvoiceCollectionDetailRepository;

trait MakeCustomerInvoiceCollectionDetailTrait
{
    /**
     * Create fake instance of CustomerInvoiceCollectionDetail and save it in database
     *
     * @param array $customerInvoiceCollectionDetailFields
     * @return CustomerInvoiceCollectionDetail
     */
    public function makeCustomerInvoiceCollectionDetail($customerInvoiceCollectionDetailFields = [])
    {
        /** @var CustomerInvoiceCollectionDetailRepository $customerInvoiceCollectionDetailRepo */
        $customerInvoiceCollectionDetailRepo = App::make(CustomerInvoiceCollectionDetailRepository::class);
        $theme = $this->fakeCustomerInvoiceCollectionDetailData($customerInvoiceCollectionDetailFields);
        return $customerInvoiceCollectionDetailRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerInvoiceCollectionDetail
     *
     * @param array $customerInvoiceCollectionDetailFields
     * @return CustomerInvoiceCollectionDetail
     */
    public function fakeCustomerInvoiceCollectionDetail($customerInvoiceCollectionDetailFields = [])
    {
        return new CustomerInvoiceCollectionDetail($this->fakeCustomerInvoiceCollectionDetailData($customerInvoiceCollectionDetailFields));
    }

    /**
     * Get fake data of CustomerInvoiceCollectionDetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCustomerInvoiceCollectionDetailData($customerInvoiceCollectionDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'customerInvoiceID' => $fake->randomDigitNotNull,
            'invoiceStatusTypeID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'collectionDate' => $fake->date('Y-m-d H:i:s'),
            'comments' => $fake->text,
            'actionRequired' => $fake->text,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserGroup' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $customerInvoiceCollectionDetailFields);
    }
}
