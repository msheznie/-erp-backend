<?php

use Faker\Factory as Faker;
use App\Models\SupplierMaster;
use App\Repositories\SupplierMasterRepository;

trait MakeSupplierMasterTrait
{
    /**
     * Create fake instance of SupplierMaster and save it in database
     *
     * @param array $supplierMasterFields
     * @return SupplierMaster
     */
    public function makeSupplierMaster($supplierMasterFields = [])
    {
        /** @var SupplierMasterRepository $supplierMasterRepo */
        $supplierMasterRepo = App::make(SupplierMasterRepository::class);
        $theme = $this->fakeSupplierMasterData($supplierMasterFields);
        return $supplierMasterRepo->create($theme);
    }

    /**
     * Get fake instance of SupplierMaster
     *
     * @param array $supplierMasterFields
     * @return SupplierMaster
     */
    public function fakeSupplierMaster($supplierMasterFields = [])
    {
        return new SupplierMaster($this->fakeSupplierMasterData($supplierMasterFields));
    }

    /**
     * Get fake data of SupplierMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSupplierMasterData($supplierMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'uniqueTextcode' => $fake->word,
            'primaryCompanySystemID' => $fake->randomDigitNotNull,
            'primaryCompanyID' => $fake->word,
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
            'approvedby' => $fake->word,
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedComment' => $fake->text,
            'isActive' => $fake->randomDigitNotNull,
            'isSupplierForiegn' => $fake->randomDigitNotNull,
            'supplierConfirmedYN' => $fake->randomDigitNotNull,
            'supplierConfirmedEmpID' => $fake->word,
            'supplierConfirmedEmpName' => $fake->word,
            'supplierConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'isCriticalYN' => $fake->randomDigitNotNull,
            'companyLinkedTo' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'isDirect' => $fake->randomDigitNotNull,
            'supplierImportanceID' => $fake->randomDigitNotNull,
            'supplierNatureID' => $fake->randomDigitNotNull,
            'supplierTypeID' => $fake->randomDigitNotNull,
            'WHTApplicable' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $supplierMasterFields);
    }
}
