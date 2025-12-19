<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\HRMSLeaveAccrualMaster;
use App\Repositories\HRMSLeaveAccrualMasterRepository;

trait MakeHRMSLeaveAccrualMasterTrait
{
    /**
     * Create fake instance of HRMSLeaveAccrualMaster and save it in database
     *
     * @param array $hRMSLeaveAccrualMasterFields
     * @return HRMSLeaveAccrualMaster
     */
    public function makeHRMSLeaveAccrualMaster($hRMSLeaveAccrualMasterFields = [])
    {
        /** @var HRMSLeaveAccrualMasterRepository $hRMSLeaveAccrualMasterRepo */
        $hRMSLeaveAccrualMasterRepo = \App::make(HRMSLeaveAccrualMasterRepository::class);
        $theme = $this->fakeHRMSLeaveAccrualMasterData($hRMSLeaveAccrualMasterFields);
        return $hRMSLeaveAccrualMasterRepo->create($theme);
    }

    /**
     * Get fake instance of HRMSLeaveAccrualMaster
     *
     * @param array $hRMSLeaveAccrualMasterFields
     * @return HRMSLeaveAccrualMaster
     */
    public function fakeHRMSLeaveAccrualMaster($hRMSLeaveAccrualMasterFields = [])
    {
        return new HRMSLeaveAccrualMaster($this->fakeHRMSLeaveAccrualMasterData($hRMSLeaveAccrualMasterFields));
    }

    /**
     * Get fake data of HRMSLeaveAccrualMaster
     *
     * @param array $hRMSLeaveAccrualMasterFields
     * @return array
     */
    public function fakeHRMSLeaveAccrualMasterData($hRMSLeaveAccrualMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companyID' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'leaveaccrualMasterCode' => $fake->word,
            'documentID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'serialNo' => $fake->randomDigitNotNull,
            'Description' => $fake->text,
            'Year' => $fake->randomDigitNotNull,
            'leavePeriod' => $fake->randomDigitNotNull,
            'leaveType' => $fake->randomDigitNotNull,
            'salaryProcessMasterID' => $fake->randomDigitNotNull,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedby' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedby' => $fake->word,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'manualAccuralYN' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createDate' => $fake->date('Y-m-d H:i:s'),
            'createdpc' => $fake->word,
            'modifieduser' => $fake->word,
            'modifiedpc' => $fake->word,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $hRMSLeaveAccrualMasterFields);
    }
}
