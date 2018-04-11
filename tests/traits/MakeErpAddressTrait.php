<?php

use Faker\Factory as Faker;
use App\Models\ErpAddress;
use App\Repositories\ErpAddressRepository;

trait MakeErpAddressTrait
{
    /**
     * Create fake instance of ErpAddress and save it in database
     *
     * @param array $erpAddressFields
     * @return ErpAddress
     */
    public function makeErpAddress($erpAddressFields = [])
    {
        /** @var ErpAddressRepository $erpAddressRepo */
        $erpAddressRepo = App::make(ErpAddressRepository::class);
        $theme = $this->fakeErpAddressData($erpAddressFields);
        return $erpAddressRepo->create($theme);
    }

    /**
     * Get fake instance of ErpAddress
     *
     * @param array $erpAddressFields
     * @return ErpAddress
     */
    public function fakeErpAddress($erpAddressFields = [])
    {
        return new ErpAddress($this->fakeErpAddressData($erpAddressFields));
    }

    /**
     * Get fake data of ErpAddress
     *
     * @param array $postFields
     * @return array
     */
    public function fakeErpAddressData($erpAddressFields = [])
    {
        $fake = Faker::create();

        return array_merge([
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
        ], $erpAddressFields);
    }
}
