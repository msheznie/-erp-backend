<?php

use Faker\Factory as Faker;
use App\Models\AddressType;
use App\Repositories\AddressTypeRepository;

trait MakeAddressTypeTrait
{
    /**
     * Create fake instance of AddressType and save it in database
     *
     * @param array $addressTypeFields
     * @return AddressType
     */
    public function makeAddressType($addressTypeFields = [])
    {
        /** @var AddressTypeRepository $addressTypeRepo */
        $addressTypeRepo = App::make(AddressTypeRepository::class);
        $theme = $this->fakeAddressTypeData($addressTypeFields);
        return $addressTypeRepo->create($theme);
    }

    /**
     * Get fake instance of AddressType
     *
     * @param array $addressTypeFields
     * @return AddressType
     */
    public function fakeAddressType($addressTypeFields = [])
    {
        return new AddressType($this->fakeAddressTypeData($addressTypeFields));
    }

    /**
     * Get fake data of AddressType
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAddressTypeData($addressTypeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'addressTypeDescription' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $addressTypeFields);
    }
}
