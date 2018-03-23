<?php

use Faker\Factory as Faker;
use App\Models\SupplierContactDetails;
use App\Repositories\SupplierContactDetailsRepository;

trait MakeSupplierContactDetailsTrait
{
    /**
     * Create fake instance of SupplierContactDetails and save it in database
     *
     * @param array $supplierContactDetailsFields
     * @return SupplierContactDetails
     */
    public function makeSupplierContactDetails($supplierContactDetailsFields = [])
    {
        /** @var SupplierContactDetailsRepository $supplierContactDetailsRepo */
        $supplierContactDetailsRepo = App::make(SupplierContactDetailsRepository::class);
        $theme = $this->fakeSupplierContactDetailsData($supplierContactDetailsFields);
        return $supplierContactDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of SupplierContactDetails
     *
     * @param array $supplierContactDetailsFields
     * @return SupplierContactDetails
     */
    public function fakeSupplierContactDetails($supplierContactDetailsFields = [])
    {
        return new SupplierContactDetails($this->fakeSupplierContactDetailsData($supplierContactDetailsFields));
    }

    /**
     * Get fake data of SupplierContactDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSupplierContactDetailsData($supplierContactDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'supplierID' => $fake->randomDigitNotNull,
            'contactTypeID' => $fake->randomDigitNotNull,
            'contactPersonName' => $fake->word,
            'contactPersonTelephone' => $fake->word,
            'contactPersonFax' => $fake->word,
            'contactPersonEmail' => $fake->word,
            'isDefault' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $supplierContactDetailsFields);
    }
}
