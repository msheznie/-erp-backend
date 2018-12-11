<?php

use Faker\Factory as Faker;
use App\Models\RequestRefferedBack;
use App\Repositories\RequestRefferedBackRepository;

trait MakeRequestRefferedBackTrait
{
    /**
     * Create fake instance of RequestRefferedBack and save it in database
     *
     * @param array $requestRefferedBackFields
     * @return RequestRefferedBack
     */
    public function makeRequestRefferedBack($requestRefferedBackFields = [])
    {
        /** @var RequestRefferedBackRepository $requestRefferedBackRepo */
        $requestRefferedBackRepo = App::make(RequestRefferedBackRepository::class);
        $theme = $this->fakeRequestRefferedBackData($requestRefferedBackFields);
        return $requestRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of RequestRefferedBack
     *
     * @param array $requestRefferedBackFields
     * @return RequestRefferedBack
     */
    public function fakeRequestRefferedBack($requestRefferedBackFields = [])
    {
        return new RequestRefferedBack($this->fakeRequestRefferedBackData($requestRefferedBackFields));
    }

    /**
     * Get fake data of RequestRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeRequestRefferedBackData($requestRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'RequestID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'departmentSystemID' => $fake->randomDigitNotNull,
            'departmentID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'companyJobID' => $fake->randomDigitNotNull,
            'jobDescription' => $fake->word,
            'serialNumber' => $fake->randomDigitNotNull,
            'RequestCode' => $fake->word,
            'comments' => $fake->word,
            'location' => $fake->randomDigitNotNull,
            'priority' => $fake->randomDigitNotNull,
            'deliveryLocation' => $fake->randomDigitNotNull,
            'RequestedDate' => $fake->date('Y-m-d H:i:s'),
            'ConfirmedYN' => $fake->randomDigitNotNull,
            'ConfirmedBySystemID' => $fake->randomDigitNotNull,
            'ConfirmedBy' => $fake->word,
            'confirmedEmpName' => $fake->word,
            'ConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'isActive' => $fake->randomDigitNotNull,
            'quantityOnOrder' => $fake->randomDigitNotNull,
            'quantityInHand' => $fake->randomDigitNotNull,
            'selectedForIssue' => $fake->randomDigitNotNull,
            'approved' => $fake->randomDigitNotNull,
            'ClosedYN' => $fake->randomDigitNotNull,
            'issueTrackID' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s'),
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s')
        ], $requestRefferedBackFields);
    }
}
