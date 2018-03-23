<?php

use Faker\Factory as Faker;
use App\Models\ApprovalRole;
use App\Repositories\ApprovalRoleRepository;

trait MakeApprovalRoleTrait
{
    /**
     * Create fake instance of ApprovalRole and save it in database
     *
     * @param array $approvalRoleFields
     * @return ApprovalRole
     */
    public function makeApprovalRole($approvalRoleFields = [])
    {
        /** @var ApprovalRoleRepository $approvalRoleRepo */
        $approvalRoleRepo = App::make(ApprovalRoleRepository::class);
        $theme = $this->fakeApprovalRoleData($approvalRoleFields);
        return $approvalRoleRepo->create($theme);
    }

    /**
     * Get fake instance of ApprovalRole
     *
     * @param array $approvalRoleFields
     * @return ApprovalRole
     */
    public function fakeApprovalRole($approvalRoleFields = [])
    {
        return new ApprovalRole($this->fakeApprovalRoleData($approvalRoleFields));
    }

    /**
     * Get fake data of ApprovalRole
     *
     * @param array $postFields
     * @return array
     */
    public function fakeApprovalRoleData($approvalRoleFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'rollDescription' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'departmentSystemID' => $fake->randomDigitNotNull,
            'departmentID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineID' => $fake->word,
            'rollLevel' => $fake->randomDigitNotNull,
            'approvalLevelID' => $fake->randomDigitNotNull,
            'approvalGroupID' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $approvalRoleFields);
    }
}
