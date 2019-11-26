<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\employeeDepartmentDelegation;
use App\Repositories\employeeDepartmentDelegationRepository;

trait MakeemployeeDepartmentDelegationTrait
{
    /**
     * Create fake instance of employeeDepartmentDelegation and save it in database
     *
     * @param array $employeeDepartmentDelegationFields
     * @return employeeDepartmentDelegation
     */
    public function makeemployeeDepartmentDelegation($employeeDepartmentDelegationFields = [])
    {
        /** @var employeeDepartmentDelegationRepository $employeeDepartmentDelegationRepo */
        $employeeDepartmentDelegationRepo = \App::make(employeeDepartmentDelegationRepository::class);
        $theme = $this->fakeemployeeDepartmentDelegationData($employeeDepartmentDelegationFields);
        return $employeeDepartmentDelegationRepo->create($theme);
    }

    /**
     * Get fake instance of employeeDepartmentDelegation
     *
     * @param array $employeeDepartmentDelegationFields
     * @return employeeDepartmentDelegation
     */
    public function fakeemployeeDepartmentDelegation($employeeDepartmentDelegationFields = [])
    {
        return new employeeDepartmentDelegation($this->fakeemployeeDepartmentDelegationData($employeeDepartmentDelegationFields));
    }

    /**
     * Get fake data of employeeDepartmentDelegation
     *
     * @param array $employeeDepartmentDelegationFields
     * @return array
     */
    public function fakeemployeeDepartmentDelegationData($employeeDepartmentDelegationFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'departmentSystemID' => $fake->randomDigitNotNull,
            'departmentID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'empSystemID' => $fake->randomDigitNotNull,
            'empID' => $fake->word,
            'employeeName' => $fake->word,
            'empEmailID' => $fake->word,
            'sendEmailNotificationForPayment' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $employeeDepartmentDelegationFields);
    }
}
