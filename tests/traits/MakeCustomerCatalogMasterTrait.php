<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\CustomerCatalogMaster;
use App\Repositories\CustomerCatalogMasterRepository;

trait MakeCustomerCatalogMasterTrait
{
    /**
     * Create fake instance of CustomerCatalogMaster and save it in database
     *
     * @param array $customerCatalogMasterFields
     * @return CustomerCatalogMaster
     */
    public function makeCustomerCatalogMaster($customerCatalogMasterFields = [])
    {
        /** @var CustomerCatalogMasterRepository $customerCatalogMasterRepo */
        $customerCatalogMasterRepo = \App::make(CustomerCatalogMasterRepository::class);
        $theme = $this->fakeCustomerCatalogMasterData($customerCatalogMasterFields);
        return $customerCatalogMasterRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerCatalogMaster
     *
     * @param array $customerCatalogMasterFields
     * @return CustomerCatalogMaster
     */
    public function fakeCustomerCatalogMaster($customerCatalogMasterFields = [])
    {
        return new CustomerCatalogMaster($this->fakeCustomerCatalogMasterData($customerCatalogMasterFields));
    }

    /**
     * Get fake data of CustomerCatalogMaster
     *
     * @param array $customerCatalogMasterFields
     * @return array
     */
    public function fakeCustomerCatalogMasterData($customerCatalogMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'catalogID' => $fake->word,
            'catalogName' => $fake->word,
            'fromDate' => $fake->date('Y-m-d H:i:s'),
            'toDate' => $fake->date('Y-m-d H:i:s'),
            'customerID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'documentSystemID' => $fake->randomDigitNotNull,
            'createdBy' => $fake->randomDigitNotNull,
            'createdDate' => $fake->date('Y-m-d H:i:s'),
            'modifiedBy' => $fake->word,
            'modifiedDate' => $fake->date('Y-m-d H:i:s'),
            'isDelete' => $fake->randomDigitNotNull,
            'isActive' => $fake->randomDigitNotNull
        ], $customerCatalogMasterFields);
    }
}
