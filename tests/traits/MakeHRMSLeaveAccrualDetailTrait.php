<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\HRMSLeaveAccrualDetail;
use App\Repositories\HRMSLeaveAccrualDetailRepository;

trait MakeHRMSLeaveAccrualDetailTrait
{
    /**
     * Create fake instance of HRMSLeaveAccrualDetail and save it in database
     *
     * @param array $hRMSLeaveAccrualDetailFields
     * @return HRMSLeaveAccrualDetail
     */
    public function makeHRMSLeaveAccrualDetail($hRMSLeaveAccrualDetailFields = [])
    {
        /** @var HRMSLeaveAccrualDetailRepository $hRMSLeaveAccrualDetailRepo */
        $hRMSLeaveAccrualDetailRepo = \App::make(HRMSLeaveAccrualDetailRepository::class);
        $theme = $this->fakeHRMSLeaveAccrualDetailData($hRMSLeaveAccrualDetailFields);
        return $hRMSLeaveAccrualDetailRepo->create($theme);
    }

    /**
     * Get fake instance of HRMSLeaveAccrualDetail
     *
     * @param array $hRMSLeaveAccrualDetailFields
     * @return HRMSLeaveAccrualDetail
     */
    public function fakeHRMSLeaveAccrualDetail($hRMSLeaveAccrualDetailFields = [])
    {
        return new HRMSLeaveAccrualDetail($this->fakeHRMSLeaveAccrualDetailData($hRMSLeaveAccrualDetailFields));
    }

    /**
     * Get fake data of HRMSLeaveAccrualDetail
     *
     * @param array $hRMSLeaveAccrualDetailFields
     * @return array
     */
    public function fakeHRMSLeaveAccrualDetailData($hRMSLeaveAccrualDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'leaveaccrualMasterID' => $fake->randomDigitNotNull,
            'empID' => $fake->word,
            'empSystemID' => $fake->randomDigitNotNull,
            'leavePeriod' => $fake->randomDigitNotNull,
            'schedulemasterID' => $fake->randomDigitNotNull,
            'leaveType' => $fake->randomDigitNotNull,
            'dateAssumed' => $fake->date('Y-m-d H:i:s'),
            'daysEntitled' => $fake->randomDigitNotNull,
            'description' => $fake->text,
            'startDate' => $fake->date('Y-m-d H:i:s'),
            'endDate' => $fake->date('Y-m-d H:i:s'),
            'manualAccuralYN' => $fake->randomDigitNotNull,
            'createDate' => $fake->date('Y-m-d H:i:s'),
            'createdUserGroup' => $fake->word,
            'createdPCid' => $fake->word,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $hRMSLeaveAccrualDetailFields);
    }
}
