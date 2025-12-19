<?php

use Faker\Factory as Faker;
use App\Models\SupplierMasterRefferedBack;
use App\Repositories\SupplierMasterRefferedBackRepository;

trait MakeSupplierMasterRefferedBackTrait
{
    /**
     * Create fake instance of SupplierMasterRefferedBack and save it in database
     *
     * @param array $supplierMasterRefferedBackFields
     * @return SupplierMasterRefferedBack
     */
    public function makeSupplierMasterRefferedBack($supplierMasterRefferedBackFields = [])
    {
        /** @var SupplierMasterRefferedBackRepository $supplierMasterRefferedBackRepo */
        $supplierMasterRefferedBackRepo = App::make(SupplierMasterRefferedBackRepository::class);
        $theme = $this->fakeSupplierMasterRefferedBackData($supplierMasterRefferedBackFields);
        return $supplierMasterRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of SupplierMasterRefferedBack
     *
     * @param array $supplierMasterRefferedBackFields
     * @return SupplierMasterRefferedBack
     */
    public function fakeSupplierMasterRefferedBack($supplierMasterRefferedBackFields = [])
    {
        return new SupplierMasterRefferedBack($this->fakeSupplierMasterRefferedBackData($supplierMasterRefferedBackFields));
    }

    /**
     * Get fake data of SupplierMasterRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSupplierMasterRefferedBackData($supplierMasterRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'supplierCodeSystem' => $fake->randomDigitNotNull,
            'uniqueTextcode' => $fake->word,
            'primaryCompanySystemID' => $fake->randomDigitNotNull,
            'primaryCompanyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
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
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedEmpSystemID' => $fake->randomDigitNotNull,
            'approvedby' => $fake->word,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedComment' => $fake->text,
            'isActive' => $fake->randomDigitNotNull,
            'isSupplierForiegn' => $fake->randomDigitNotNull,
            'supplierConfirmedYN' => $fake->randomDigitNotNull,
            'supplierConfirmedEmpID' => $fake->word,
            'supplierConfirmedEmpSystemID' => $fake->randomDigitNotNull,
            'supplierConfirmedEmpName' => $fake->word,
            'supplierConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'isCriticalYN' => $fake->randomDigitNotNull,
            'companyLinkedToSystemID' => $fake->randomDigitNotNull,
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
            'vatEligible' => $fake->randomDigitNotNull,
            'vatNumber' => $fake->word,
            'vatPercentage' => $fake->randomDigitNotNull,
            'supCategoryICVMasterID' => $fake->randomDigitNotNull,
            'supCategorySubICVID' => $fake->randomDigitNotNull,
            'isLCCYN' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s'),
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUserSystemID' => $fake->randomDigitNotNull
        ], $supplierMasterRefferedBackFields);
    }
}
