<?php

use Faker\Factory as Faker;
use App\Models\CustomerContactDetails;
use App\Repositories\CustomerContactDetailsRepository;

trait MakeCustomerContactDetailsTrait
{
    /**
     * Create fake instance of CustomerContactDetails and save it in database
     *
     * @param array $customerContactDetailsFields
     * @return CustomerContactDetails
     */
    public function makeCustomerContactDetails($customerContactDetailsFields = [])
    {
        /** @var CustomerContactDetailsRepository $customerContactDetailsRepo */
        $customerContactDetailsRepo = App::make(CustomerContactDetailsRepository::class);
        $theme = $this->fakeCustomerContactDetailsData($customerContactDetailsFields);
        return $customerContactDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerContactDetails
     *
     * @param array $customerContactDetailsFields
     * @return CustomerContactDetails
     */
    public function fakeCustomerContactDetails($customerContactDetailsFields = [])
    {
        return new CustomerContactDetails($this->fakeCustomerContactDetailsData($customerContactDetailsFields));
    }

    /**
     * Get fake data of CustomerContactDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCustomerContactDetailsData($customerContactDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'customerID' => $fake->randomDigitNotNull,
            'contactTypeID' => $fake->randomDigitNotNull,
            'contactPersonName' => $fake->word,
            'contactPersonTelephone' => $fake->word,
            'contactPersonFax' => $fake->word,
            'contactPersonEmail' => $fake->word,
            'isDefault' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $customerContactDetailsFields);
    }
}
