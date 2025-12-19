<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\LeaveDataDetail;
use App\Repositories\LeaveDataDetailRepository;

trait MakeLeaveDataDetailTrait
{
    /**
     * Create fake instance of LeaveDataDetail and save it in database
     *
     * @param array $leaveDataDetailFields
     * @return LeaveDataDetail
     */
    public function makeLeaveDataDetail($leaveDataDetailFields = [])
    {
        /** @var LeaveDataDetailRepository $leaveDataDetailRepo */
        $leaveDataDetailRepo = \App::make(LeaveDataDetailRepository::class);
        $theme = $this->fakeLeaveDataDetailData($leaveDataDetailFields);
        return $leaveDataDetailRepo->create($theme);
    }

    /**
     * Get fake instance of LeaveDataDetail
     *
     * @param array $leaveDataDetailFields
     * @return LeaveDataDetail
     */
    public function fakeLeaveDataDetail($leaveDataDetailFields = [])
    {
        return new LeaveDataDetail($this->fakeLeaveDataDetailData($leaveDataDetailFields));
    }

    /**
     * Get fake data of LeaveDataDetail
     *
     * @param array $leaveDataDetailFields
     * @return array
     */
    public function fakeLeaveDataDetailData($leaveDataDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'leavedatamasterID' => $fake->randomDigitNotNull,
            'leavemasterID' => $fake->randomDigitNotNull,
            'startDate' => $fake->date('Y-m-d H:i:s'),
            'endDate' => $fake->date('Y-m-d H:i:s'),
            'noOfWorkingDays' => $fake->randomDigitNotNull,
            'noOfNonWorkingDays' => $fake->randomDigitNotNull,
            'totalDays' => $fake->randomDigitNotNull,
            'calculatedDays' => $fake->randomDigitNotNull,
            'startLastHitchDate' => $fake->date('Y-m-d H:i:s'),
            'endLastHitchDate' => $fake->date('Y-m-d H:i:s'),
            'startFollowingHitchDate' => $fake->date('Y-m-d H:i:s'),
            'endFollowingHitchDate' => $fake->date('Y-m-d H:i:s'),
            'comment' => $fake->text,
            'reportingMangerComment' => $fake->text,
            'modifieduser' => $fake->word,
            'modifiedpc' => $fake->word,
            'createduserGroup' => $fake->word,
            'createdpc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s'),
            'claimedDays' => $fake->randomDigitNotNull,
            'endFinalDate' => $fake->word
        ], $leaveDataDetailFields);
    }
}
