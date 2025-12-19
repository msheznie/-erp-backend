<?php

use Faker\Factory as Faker;
use App\Models\MaterielRequest;
use App\Repositories\MaterielRequestRepository;

trait MakeMaterielRequestTrait
{
    /**
     * Create fake instance of MaterielRequest and save it in database
     *
     * @param array $materielRequestFields
     * @return MaterielRequest
     */
    public function makeMaterielRequest($materielRequestFields = [])
    {
        /** @var MaterielRequestRepository $materielRequestRepo */
        $materielRequestRepo = App::make(MaterielRequestRepository::class);
        $theme = $this->fakeMaterielRequestData($materielRequestFields);
        return $materielRequestRepo->create($theme);
    }

    /**
     * Get fake instance of MaterielRequest
     *
     * @param array $materielRequestFields
     * @return MaterielRequest
     */
    public function fakeMaterielRequest($materielRequestFields = [])
    {
        return new MaterielRequest($this->fakeMaterielRequestData($materielRequestFields));
    }

    /**
     * Get fake data of MaterielRequest
     *
     * @param array $postFields
     * @return array
     */
    public function fakeMaterielRequestData($materielRequestFields = [])
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
            'ConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'isActive' => $fake->randomDigitNotNull,
            'quantityOnOrder' => $fake->randomDigitNotNull,
            'quantityInHand' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'selectedForIssue' => $fake->randomDigitNotNull,
            'approved' => $fake->randomDigitNotNull,
            'ClosedYN' => $fake->randomDigitNotNull,
            'issueTrackID' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $materielRequestFields);
    }
}
