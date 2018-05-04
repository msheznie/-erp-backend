<?php

use Faker\Factory as Faker;
use App\Models\Address;
use App\Repositories\AddressRepository;

trait MakeAddressTrait
{
    /**
     * Create fake instance of Address and save it in database
     *
     * @param array $addressFields
     * @return Address
     */
    public function makeAddress($addressFields = [])
    {
        /** @var AddressRepository $addressRepo */
        $addressRepo = App::make(AddressRepository::class);
        $theme = $this->fakeAddressData($addressFields);
        return $addressRepo->create($theme);
    }

    /**
     * Get fake instance of Address
     *
     * @param array $addressFields
     * @return Address
     */
    public function fakeAddress($addressFields = [])
    {
        return new Address($this->fakeAddressData($addressFields));
    }

    /**
     * Get fake data of Address
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAddressData($addressFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'locationID' => $fake->randomDigitNotNull,
            'departmentID' => $fake->word,
            'addressTypeID' => $fake->randomDigitNotNull,
            'addressDescrption' => $fake->text,
            'contactPersonID' => $fake->word,
            'contactPersonTelephone' => $fake->word,
            'contactPersonFaxNo' => $fake->word,
            'contactPersonEmail' => $fake->word,
            'isDefault' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $addressFields);
    }
}
