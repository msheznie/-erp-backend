<?php

use Faker\Factory as Faker;
use App\Models\DocumentReferedHistory;
use App\Repositories\DocumentReferedHistoryRepository;

trait MakeDocumentReferedHistoryTrait
{
    /**
     * Create fake instance of DocumentReferedHistory and save it in database
     *
     * @param array $documentReferedHistoryFields
     * @return DocumentReferedHistory
     */
    public function makeDocumentReferedHistory($documentReferedHistoryFields = [])
    {
        /** @var DocumentReferedHistoryRepository $documentReferedHistoryRepo */
        $documentReferedHistoryRepo = App::make(DocumentReferedHistoryRepository::class);
        $theme = $this->fakeDocumentReferedHistoryData($documentReferedHistoryFields);
        return $documentReferedHistoryRepo->create($theme);
    }

    /**
     * Get fake instance of DocumentReferedHistory
     *
     * @param array $documentReferedHistoryFields
     * @return DocumentReferedHistory
     */
    public function fakeDocumentReferedHistory($documentReferedHistoryFields = [])
    {
        return new DocumentReferedHistory($this->fakeDocumentReferedHistoryData($documentReferedHistoryFields));
    }

    /**
     * Get fake data of DocumentReferedHistory
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDocumentReferedHistoryData($documentReferedHistoryFields = [])
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
            'employeeSystemID' => $fake->randomDigitNotNull,
            'employeeID' => $fake->word,
            'docConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'docConfirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'docConfirmedByEmpID' => $fake->word,
            'preRollApprovedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedComments' => $fake->word,
            'rejectedYN' => $fake->word,
            'rejectedDate' => $fake->date('Y-m-d H:i:s'),
            'rejectedComments' => $fake->word,
            'approvedPCID' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s'),
            'refTimes' => $fake->randomDigitNotNull
        ], $documentReferedHistoryFields);
    }
}
