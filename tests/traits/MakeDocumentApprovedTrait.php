<?php

use Faker\Factory as Faker;
use App\Models\DocumentApproved;
use App\Repositories\DocumentApprovedRepository;

trait MakeDocumentApprovedTrait
{
    /**
     * Create fake instance of DocumentApproved and save it in database
     *
     * @param array $documentApprovedFields
     * @return DocumentApproved
     */
    public function makeDocumentApproved($documentApprovedFields = [])
    {
        /** @var DocumentApprovedRepository $documentApprovedRepo */
        $documentApprovedRepo = App::make(DocumentApprovedRepository::class);
        $theme = $this->fakeDocumentApprovedData($documentApprovedFields);
        return $documentApprovedRepo->create($theme);
    }

    /**
     * Get fake instance of DocumentApproved
     *
     * @param array $documentApprovedFields
     * @return DocumentApproved
     */
    public function fakeDocumentApproved($documentApprovedFields = [])
    {
        return new DocumentApproved($this->fakeDocumentApprovedData($documentApprovedFields));
    }

    /**
     * Get fake data of DocumentApproved
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDocumentApprovedData($documentApprovedFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'departmentSystemID' => $fake->randomDigitNotNull,
            'departmentID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'documentSystemCode' => $fake->randomDigitNotNull,
            'documentCode' => $fake->word,
            'documentDate' => $fake->date('Y-m-d H:i:s'),
            'approvalLevelID' => $fake->randomDigitNotNull,
            'rollID' => $fake->randomDigitNotNull,
            'approvalGroupID' => $fake->randomDigitNotNull,
            'rollLevelOrder' => $fake->randomDigitNotNull,
            'employeeID' => $fake->word,
            'docConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'docConfirmedByEmpID' => $fake->word,
            'preRollApprovedDate' => $fake->date('Y-m-d H:i:s'),
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
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $documentApprovedFields);
    }
}
