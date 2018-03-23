<?php

use Faker\Factory as Faker;
use App\Models\ApprovalGroups;
use App\Repositories\ApprovalGroupsRepository;

trait MakeApprovalGroupsTrait
{
    /**
     * Create fake instance of ApprovalGroups and save it in database
     *
     * @param array $approvalGroupsFields
     * @return ApprovalGroups
     */
    public function makeApprovalGroups($approvalGroupsFields = [])
    {
        /** @var ApprovalGroupsRepository $approvalGroupsRepo */
        $approvalGroupsRepo = App::make(ApprovalGroupsRepository::class);
        $theme = $this->fakeApprovalGroupsData($approvalGroupsFields);
        return $approvalGroupsRepo->create($theme);
    }

    /**
     * Get fake instance of ApprovalGroups
     *
     * @param array $approvalGroupsFields
     * @return ApprovalGroups
     */
    public function fakeApprovalGroups($approvalGroupsFields = [])
    {
        return new ApprovalGroups($this->fakeApprovalGroupsData($approvalGroupsFields));
    }

    /**
     * Get fake data of ApprovalGroups
     *
     * @param array $postFields
     * @return array
     */
    public function fakeApprovalGroupsData($approvalGroupsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'rightsGroupDes' => $fake->word,
            'isFormsAssigned' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'departmentID' => $fake->word,
            'condition' => $fake->word,
            'sortOrder' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $approvalGroupsFields);
    }
}
