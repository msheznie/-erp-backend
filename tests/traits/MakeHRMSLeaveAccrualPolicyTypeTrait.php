<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\HRMSLeaveAccrualPolicyType;
use App\Repositories\HRMSLeaveAccrualPolicyTypeRepository;

trait MakeHRMSLeaveAccrualPolicyTypeTrait
{
    /**
     * Create fake instance of HRMSLeaveAccrualPolicyType and save it in database
     *
     * @param array $hRMSLeaveAccrualPolicyTypeFields
     * @return HRMSLeaveAccrualPolicyType
     */
    public function makeHRMSLeaveAccrualPolicyType($hRMSLeaveAccrualPolicyTypeFields = [])
    {
        /** @var HRMSLeaveAccrualPolicyTypeRepository $hRMSLeaveAccrualPolicyTypeRepo */
        $hRMSLeaveAccrualPolicyTypeRepo = \App::make(HRMSLeaveAccrualPolicyTypeRepository::class);
        $theme = $this->fakeHRMSLeaveAccrualPolicyTypeData($hRMSLeaveAccrualPolicyTypeFields);
        return $hRMSLeaveAccrualPolicyTypeRepo->create($theme);
    }

    /**
     * Get fake instance of HRMSLeaveAccrualPolicyType
     *
     * @param array $hRMSLeaveAccrualPolicyTypeFields
     * @return HRMSLeaveAccrualPolicyType
     */
    public function fakeHRMSLeaveAccrualPolicyType($hRMSLeaveAccrualPolicyTypeFields = [])
    {
        return new HRMSLeaveAccrualPolicyType($this->fakeHRMSLeaveAccrualPolicyTypeData($hRMSLeaveAccrualPolicyTypeFields));
    }

    /**
     * Get fake data of HRMSLeaveAccrualPolicyType
     *
     * @param array $hRMSLeaveAccrualPolicyTypeFields
     * @return array
     */
    public function fakeHRMSLeaveAccrualPolicyTypeData($hRMSLeaveAccrualPolicyTypeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'description' => $fake->text,
            'isOnlyFemale' => $fake->randomDigitNotNull,
            'isOnlyMuslim' => $fake->randomDigitNotNull,
            'isExpat' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $hRMSLeaveAccrualPolicyTypeFields);
    }
}
