<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\LeaveApplicationType;
use App\Repositories\LeaveApplicationTypeRepository;

trait MakeLeaveApplicationTypeTrait
{
    /**
     * Create fake instance of LeaveApplicationType and save it in database
     *
     * @param array $leaveApplicationTypeFields
     * @return LeaveApplicationType
     */
    public function makeLeaveApplicationType($leaveApplicationTypeFields = [])
    {
        /** @var LeaveApplicationTypeRepository $leaveApplicationTypeRepo */
        $leaveApplicationTypeRepo = \App::make(LeaveApplicationTypeRepository::class);
        $theme = $this->fakeLeaveApplicationTypeData($leaveApplicationTypeFields);
        return $leaveApplicationTypeRepo->create($theme);
    }

    /**
     * Get fake instance of LeaveApplicationType
     *
     * @param array $leaveApplicationTypeFields
     * @return LeaveApplicationType
     */
    public function fakeLeaveApplicationType($leaveApplicationTypeFields = [])
    {
        return new LeaveApplicationType($this->fakeLeaveApplicationTypeData($leaveApplicationTypeFields));
    }

    /**
     * Get fake data of LeaveApplicationType
     *
     * @param array $leaveApplicationTypeFields
     * @return array
     */
    public function fakeLeaveApplicationTypeData($leaveApplicationTypeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'Type' => $fake->text,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $leaveApplicationTypeFields);
    }
}
