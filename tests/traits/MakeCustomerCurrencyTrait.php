<?php

use Faker\Factory as Faker;
use App\Models\CustomerCurrency;
use App\Repositories\CustomerCurrencyRepository;

trait MakeCustomerCurrencyTrait
{
    /**
     * Create fake instance of CustomerCurrency and save it in database
     *
     * @param array $customerCurrencyFields
     * @return CustomerCurrency
     */
    public function makeCustomerCurrency($customerCurrencyFields = [])
    {
        /** @var CustomerCurrencyRepository $customerCurrencyRepo */
        $customerCurrencyRepo = App::make(CustomerCurrencyRepository::class);
        $theme = $this->fakeCustomerCurrencyData($customerCurrencyFields);
        return $customerCurrencyRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerCurrency
     *
     * @param array $customerCurrencyFields
     * @return CustomerCurrency
     */
    public function fakeCustomerCurrency($customerCurrencyFields = [])
    {
        return new CustomerCurrency($this->fakeCustomerCurrencyData($customerCurrencyFields));
    }

    /**
     * Get fake data of CustomerCurrency
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCustomerCurrencyData($customerCurrencyFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'customerCodeSystem' => $fake->randomDigitNotNull,
            'customerCode' => $fake->word,
            'currencyID' => $fake->randomDigitNotNull,
            'isDefault' => $fake->randomDigitNotNull,
            'isAssigned' => $fake->randomDigitNotNull,
            'createdBy' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $customerCurrencyFields);
    }
}
