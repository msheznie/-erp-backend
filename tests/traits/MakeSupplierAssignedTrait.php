<?php

use Faker\Factory as Faker;
use App\Models\SupplierAssigned;
use App\Repositories\SupplierAssignedRepository;

trait MakeSupplierAssignedTrait
{
    /**
     * Create fake instance of SupplierAssigned and save it in database
     *
     * @param array $supplierAssignedFields
     * @return SupplierAssigned
     */
    public function makeSupplierAssigned($supplierAssignedFields = [])
    {
        /** @var SupplierAssignedRepository $supplierAssignedRepo */
        $supplierAssignedRepo = App::make(SupplierAssignedRepository::class);
        $theme = $this->fakeSupplierAssignedData($supplierAssignedFields);
        return $supplierAssignedRepo->create($theme);
    }

    /**
     * Get fake instance of SupplierAssigned
     *
     * @param array $supplierAssignedFields
     * @return SupplierAssigned
     */
    public function fakeSupplierAssigned($supplierAssignedFields = [])
    {
        return new SupplierAssigned($this->fakeSupplierAssignedData($supplierAssignedFields));
    }

    /**
     * Get fake data of SupplierAssigned
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSupplierAssignedData($supplierAssignedFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'supplierCodeSytem' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'uniqueTextcode' => $fake->word,
            'primarySupplierCode' => $fake->word,
            'secondarySupplierCode' => $fake->word,
            'supplierName' => $fake->text,
            'liabilityAccountSysemID' => $fake->randomDigitNotNull,
            'liabilityAccount' => $fake->word,
            'UnbilledGRVAccountSystemID' => $fake->randomDigitNotNull,
            'UnbilledGRVAccount' => $fake->word,
            'address' => $fake->text,
            'countryID' => $fake->randomDigitNotNull,
            'supplierCountryID' => $fake->word,
            'telephone' => $fake->word,
            'fax' => $fake->word,
            'supEmail' => $fake->text,
            'webAddress' => $fake->text,
            'currency' => $fake->randomDigitNotNull,
            'nameOnPaymentCheque' => $fake->word,
            'creditLimit' => $fake->randomDigitNotNull,
            'creditPeriod' => $fake->randomDigitNotNull,
            'supCategoryMasterID' => $fake->randomDigitNotNull,
            'supCategorySubID' => $fake->randomDigitNotNull,
            'registrationNumber' => $fake->word,
            'registrationExprity' => $fake->word,
            'supplierImportanceID' => $fake->randomDigitNotNull,
            'supplierNatureID' => $fake->randomDigitNotNull,
            'supplierTypeID' => $fake->randomDigitNotNull,
            'WHTApplicable' => $fake->randomDigitNotNull,
            'isRelatedPartyYN' => $fake->randomDigitNotNull,
            'isCriticalYN' => $fake->randomDigitNotNull,
            'isActive' => $fake->randomDigitNotNull,
            'isAssigned' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $supplierAssignedFields);
    }
}
