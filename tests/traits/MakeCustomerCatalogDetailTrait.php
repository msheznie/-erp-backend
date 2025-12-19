<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\CustomerCatalogDetail;
use App\Repositories\CustomerCatalogDetailRepository;

trait MakeCustomerCatalogDetailTrait
{
    /**
     * Create fake instance of CustomerCatalogDetail and save it in database
     *
     * @param array $customerCatalogDetailFields
     * @return CustomerCatalogDetail
     */
    public function makeCustomerCatalogDetail($customerCatalogDetailFields = [])
    {
        /** @var CustomerCatalogDetailRepository $customerCatalogDetailRepo */
        $customerCatalogDetailRepo = \App::make(CustomerCatalogDetailRepository::class);
        $theme = $this->fakeCustomerCatalogDetailData($customerCatalogDetailFields);
        return $customerCatalogDetailRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerCatalogDetail
     *
     * @param array $customerCatalogDetailFields
     * @return CustomerCatalogDetail
     */
    public function fakeCustomerCatalogDetail($customerCatalogDetailFields = [])
    {
        return new CustomerCatalogDetail($this->fakeCustomerCatalogDetailData($customerCatalogDetailFields));
    }

    /**
     * Get fake data of CustomerCatalogDetail
     *
     * @param array $customerCatalogDetailFields
     * @return array
     */
    public function fakeCustomerCatalogDetailData($customerCatalogDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'customerCatalogMasterID' => $fake->randomDigitNotNull,
            'itemCodeSystem' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->text,
            'itemUnitOfMeasure' => $fake->randomDigitNotNull,
            'partNo' => $fake->word,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localPrice' => $fake->word,
            'reportingCurrencyID' => $fake->randomDigitNotNull,
            'reportingPrice' => $fake->word,
            'leadTime' => $fake->randomDigitNotNull,
            'isDelete' => $fake->randomDigitNotNull,
            'timstamp' => $fake->date('Y-m-d H:i:s')
        ], $customerCatalogDetailFields);
    }
}
