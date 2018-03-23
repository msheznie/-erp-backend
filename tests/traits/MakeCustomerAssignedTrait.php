<?php

use Faker\Factory as Faker;
use App\Models\CustomerAssigned;
use App\Repositories\CustomerAssignedRepository;

trait MakeCustomerAssignedTrait
{
    /**
     * Create fake instance of CustomerAssigned and save it in database
     *
     * @param array $customerAssignedFields
     * @return CustomerAssigned
     */
    public function makeCustomerAssigned($customerAssignedFields = [])
    {
        /** @var CustomerAssignedRepository $customerAssignedRepo */
        $customerAssignedRepo = App::make(CustomerAssignedRepository::class);
        $theme = $this->fakeCustomerAssignedData($customerAssignedFields);
        return $customerAssignedRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerAssigned
     *
     * @param array $customerAssignedFields
     * @return CustomerAssigned
     */
    public function fakeCustomerAssigned($customerAssignedFields = [])
    {
        return new CustomerAssigned($this->fakeCustomerAssignedData($customerAssignedFields));
    }

    /**
     * Get fake data of CustomerAssigned
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCustomerAssignedData($customerAssignedFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'customerCodeSystem' => $fake->randomDigitNotNull,
            'CutomerCode' => $fake->word,
            'customerShortCode' => $fake->word,
            'custGLAccountSystemID' => $fake->randomDigitNotNull,
            'custGLaccount' => $fake->word,
            'CustomerName' => $fake->text,
            'ReportTitle' => $fake->text,
            'customerAddress1' => $fake->text,
            'customerAddress2' => $fake->text,
            'customerCity' => $fake->word,
            'customerCountry' => $fake->word,
            'CustWebsite' => $fake->word,
            'creditLimit' => $fake->randomDigitNotNull,
            'creditDays' => $fake->randomDigitNotNull,
            'isRelatedPartyYN' => $fake->randomDigitNotNull,
            'isActive' => $fake->randomDigitNotNull,
            'isAssigned' => $fake->randomDigitNotNull,
            'vatEligible' => $fake->randomDigitNotNull,
            'vatNumber' => $fake->word,
            'vatPercentage' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $customerAssignedFields);
    }
}
