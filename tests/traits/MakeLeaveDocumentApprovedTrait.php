<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\LeaveDocumentApproved;
use App\Repositories\LeaveDocumentApprovedRepository;

trait MakeLeaveDocumentApprovedTrait
{
    /**
     * Create fake instance of LeaveDocumentApproved and save it in database
     *
     * @param array $leaveDocumentApprovedFields
     * @return LeaveDocumentApproved
     */
    public function makeLeaveDocumentApproved($leaveDocumentApprovedFields = [])
    {
        /** @var LeaveDocumentApprovedRepository $leaveDocumentApprovedRepo */
        $leaveDocumentApprovedRepo = \App::make(LeaveDocumentApprovedRepository::class);
        $theme = $this->fakeLeaveDocumentApprovedData($leaveDocumentApprovedFields);
        return $leaveDocumentApprovedRepo->create($theme);
    }

    /**
     * Get fake instance of LeaveDocumentApproved
     *
     * @param array $leaveDocumentApprovedFields
     * @return LeaveDocumentApproved
     */
    public function fakeLeaveDocumentApproved($leaveDocumentApprovedFields = [])
    {
        return new LeaveDocumentApproved($this->fakeLeaveDocumentApprovedData($leaveDocumentApprovedFields));
    }

    /**
     * Get fake data of LeaveDocumentApproved
     *
     * @param array $leaveDocumentApprovedFields
     * @return array
     */
    public function fakeLeaveDocumentApprovedData($leaveDocumentApprovedFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companyID' => $fake->word,
            'departmentID' => $fake->word,
            'serviceLineCode' => $fake->word,
            'documentID' => $fake->word,
            'documentSystemCode' => $fake->randomDigitNotNull,
            'documentCode' => $fake->word,
            'approvalLevelID' => $fake->randomDigitNotNull,
            'rollID' => $fake->randomDigitNotNull,
            'rollLevelOrder' => $fake->randomDigitNotNull,
            'employeeID' => $fake->word,
            'Approver' => $fake->word,
            'docConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'docConfirmedByEmpID' => $fake->word,
            'preRollApprovedDate' => $fake->date('Y-m-d H:i:s'),
            'requesterID' => $fake->word,
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedComments' => $fake->text,
            'rejectedYN' => $fake->randomDigitNotNull,
            'rejectedDate' => $fake->date('Y-m-d H:i:s'),
            'rejectedComments' => $fake->text,
            'myApproveFlag' => $fake->randomDigitNotNull,
            'isDeligationApproval' => $fake->randomDigitNotNull,
            'approvedForEmpID' => $fake->word,
            'isApprovedFromPC' => $fake->randomDigitNotNull,
            'approvedPCID' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s'),
            'approvalGroupID' => $fake->randomDigitNotNull,
            'hrApproval' => $fake->randomDigitNotNull
        ], $leaveDocumentApprovedFields);
    }
}
