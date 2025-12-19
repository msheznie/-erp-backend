<?php

use Faker\Factory as Faker;
use App\Models\CustomerMaster;
use App\Repositories\CustomerMasterRepository;

trait MakeCustomerMasterTrait
{
    /**
     * Create fake instance of CustomerMaster and save it in database
     *
     * @param array $customerMasterFields
     * @return CustomerMaster
     */
    public function makeCustomerMaster($customerMasterFields = [])
    {
        /** @var CustomerMasterRepository $customerMasterRepo */
        $customerMasterRepo = App::make(CustomerMasterRepository::class);
        $theme = $this->fakeCustomerMasterData($customerMasterFields);
        return $customerMasterRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerMaster
     *
     * @param array $customerMasterFields
     * @return CustomerMaster
     */
    public function fakeCustomerMaster($customerMasterFields = [])
    {
        return new CustomerMaster($this->fakeCustomerMasterData($customerMasterFields));
    }

    /**
     * Get fake data of CustomerMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCustomerMasterData($customerMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'primaryCompanySystemID' => $fake->randomDigitNotNull,
            'primaryCompanyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'lastSerialOrder' => $fake->randomDigitNotNull,
            'CutomerCode' => $fake->word,
            'customerShortCode' => $fake->word,
            'custGLAccountSystemID' => $fake->randomDigitNotNull,
            'custGLaccount' => $fake->word,
            'CustomerName' => $fake->word,
            'ReportTitle' => $fake->word,
            'customerAddress1' => $fake->text,
            'customerAddress2' => $fake->text,
            'customerCity' => $fake->word,
            'customerCountry' => $fake->word,
            'CustWebsite' => $fake->word,
            'creditLimit' => $fake->randomDigitNotNull,
            'creditDays' => $fake->randomDigitNotNull,
            'customerLogo' => $fake->word,
            'companyLinkedTo' => $fake->word,
            'isCustomerActive' => $fake->randomDigitNotNull,
            'isAllowedQHSE' => $fake->randomDigitNotNull,
            'vatEligible' => $fake->randomDigitNotNull,
            'vatNumber' => $fake->word,
            'vatPercentage' => $fake->randomDigitNotNull,
            'isSupplierForiegn' => $fake->randomDigitNotNull,
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedComment' => $fake->text,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedEmpID' => $fake->word,
            'confirmedEmpName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'createdUserGroup' => $fake->word,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdPcID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $customerMasterFields);
    }
}
