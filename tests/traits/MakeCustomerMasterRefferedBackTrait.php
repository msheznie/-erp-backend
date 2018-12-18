<?php

use Faker\Factory as Faker;
use App\Models\CustomerMasterRefferedBack;
use App\Repositories\CustomerMasterRefferedBackRepository;

trait MakeCustomerMasterRefferedBackTrait
{
    /**
     * Create fake instance of CustomerMasterRefferedBack and save it in database
     *
     * @param array $customerMasterRefferedBackFields
     * @return CustomerMasterRefferedBack
     */
    public function makeCustomerMasterRefferedBack($customerMasterRefferedBackFields = [])
    {
        /** @var CustomerMasterRefferedBackRepository $customerMasterRefferedBackRepo */
        $customerMasterRefferedBackRepo = App::make(CustomerMasterRefferedBackRepository::class);
        $theme = $this->fakeCustomerMasterRefferedBackData($customerMasterRefferedBackFields);
        return $customerMasterRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerMasterRefferedBack
     *
     * @param array $customerMasterRefferedBackFields
     * @return CustomerMasterRefferedBack
     */
    public function fakeCustomerMasterRefferedBack($customerMasterRefferedBackFields = [])
    {
        return new CustomerMasterRefferedBack($this->fakeCustomerMasterRefferedBackData($customerMasterRefferedBackFields));
    }

    /**
     * Get fake data of CustomerMasterRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCustomerMasterRefferedBackData($customerMasterRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'customerCodeSystem' => $fake->randomDigitNotNull,
            'primaryCompanySystemID' => $fake->randomDigitNotNull,
            'primaryCompanyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'lastSerialOrder' => $fake->randomDigitNotNull,
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
            'customerLogo' => $fake->word,
            'companyLinkedToSystemID' => $fake->randomDigitNotNull,
            'companyLinkedTo' => $fake->word,
            'isCustomerActive' => $fake->randomDigitNotNull,
            'isAllowedQHSE' => $fake->randomDigitNotNull,
            'vatEligible' => $fake->randomDigitNotNull,
            'vatNumber' => $fake->word,
            'vatPercentage' => $fake->randomDigitNotNull,
            'isSupplierForiegn' => $fake->randomDigitNotNull,
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedEmpSystemID' => $fake->randomDigitNotNull,
            'approvedEmpID' => $fake->word,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedComment' => $fake->text,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedEmpID' => $fake->word,
            'confirmedEmpName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdPcID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $customerMasterRefferedBackFields);
    }
}
