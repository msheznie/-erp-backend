<?php

use Faker\Factory as Faker;
use App\Models\CustomerMasterCategory;
use App\Repositories\CustomerMasterCategoryRepository;

trait MakeCustomerMasterCategoryTrait
{
    /**
     * Create fake instance of CustomerMasterCategory and save it in database
     *
     * @param array $customerMasterCategoryFields
     * @return CustomerMasterCategory
     */
    public function makeCustomerMasterCategory($customerMasterCategoryFields = [])
    {
        /** @var CustomerMasterCategoryRepository $customerMasterCategoryRepo */
        $customerMasterCategoryRepo = App::make(CustomerMasterCategoryRepository::class);
        $theme = $this->fakeCustomerMasterCategoryData($customerMasterCategoryFields);
        return $customerMasterCategoryRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerMasterCategory
     *
     * @param array $customerMasterCategoryFields
     * @return CustomerMasterCategory
     */
    public function fakeCustomerMasterCategory($customerMasterCategoryFields = [])
    {
        return new CustomerMasterCategory($this->fakeCustomerMasterCategoryData($customerMasterCategoryFields));
    }

    /**
     * Get fake data of CustomerMasterCategory
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCustomerMasterCategoryData($customerMasterCategoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'categoryDescription' => $fake->word,
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
            'TIMESTAMP' => $fake->date('Y-m-d H:i:s')
        ], $customerMasterCategoryFields);
    }
}
