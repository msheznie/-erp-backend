<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\LeaveDataMaster;
use App\Repositories\LeaveDataMasterRepository;

trait MakeLeaveDataMasterTrait
{
    /**
     * Create fake instance of LeaveDataMaster and save it in database
     *
     * @param array $leaveDataMasterFields
     * @return LeaveDataMaster
     */
    public function makeLeaveDataMaster($leaveDataMasterFields = [])
    {
        /** @var LeaveDataMasterRepository $leaveDataMasterRepo */
        $leaveDataMasterRepo = \App::make(LeaveDataMasterRepository::class);
        $theme = $this->fakeLeaveDataMasterData($leaveDataMasterFields);
        return $leaveDataMasterRepo->create($theme);
    }

    /**
     * Get fake instance of LeaveDataMaster
     *
     * @param array $leaveDataMasterFields
     * @return LeaveDataMaster
     */
    public function fakeLeaveDataMaster($leaveDataMasterFields = [])
    {
        return new LeaveDataMaster($this->fakeLeaveDataMasterData($leaveDataMasterFields));
    }

    /**
     * Get fake data of LeaveDataMaster
     *
     * @param array $leaveDataMasterFields
     * @return array
     */
    public function fakeLeaveDataMasterData($leaveDataMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'empID' => $fake->word,
            'EntryType' => $fake->randomDigitNotNull,
            'managerAttached' => $fake->word,
            'SeniorManager' => $fake->word,
            'designatiomID' => $fake->randomDigitNotNull,
            'location' => $fake->randomDigitNotNull,
            'leaveType' => $fake->randomDigitNotNull,
            'scheduleMasterID' => $fake->randomDigitNotNull,
            'leaveDataMasterCode' => $fake->word,
            'documentID' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'createDate' => $fake->date('Y-m-d H:i:s'),
            'CompanyID' => $fake->word,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedby' => $fake->word,
            'confirmedDate' => $fake->word,
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedby' => $fake->word,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'leaveAvailable' => $fake->randomDigitNotNull,
            'policytype' => $fake->randomDigitNotNull,
            'isPicked' => $fake->randomDigitNotNull,
            'modifieduser' => $fake->word,
            'modifiedpc' => $fake->word,
            'createduserGroup' => $fake->word,
            'createdpc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s'),
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'hrapprovalYN' => $fake->randomDigitNotNull,
            'hrapprovedby' => $fake->word,
            'hrapprovedDate' => $fake->word,
            'claimedYN' => $fake->randomDigitNotNull,
            'claimedLeavedatamasterID' => $fake->randomDigitNotNull
        ], $leaveDataMasterFields);
    }
}
