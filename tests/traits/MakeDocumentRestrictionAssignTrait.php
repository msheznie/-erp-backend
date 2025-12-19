<?php

use Faker\Factory as Faker;
use App\Models\DocumentRestrictionAssign;
use App\Repositories\DocumentRestrictionAssignRepository;

trait MakeDocumentRestrictionAssignTrait
{
    /**
     * Create fake instance of DocumentRestrictionAssign and save it in database
     *
     * @param array $documentRestrictionAssignFields
     * @return DocumentRestrictionAssign
     */
    public function makeDocumentRestrictionAssign($documentRestrictionAssignFields = [])
    {
        /** @var DocumentRestrictionAssignRepository $documentRestrictionAssignRepo */
        $documentRestrictionAssignRepo = App::make(DocumentRestrictionAssignRepository::class);
        $theme = $this->fakeDocumentRestrictionAssignData($documentRestrictionAssignFields);
        return $documentRestrictionAssignRepo->create($theme);
    }

    /**
     * Get fake instance of DocumentRestrictionAssign
     *
     * @param array $documentRestrictionAssignFields
     * @return DocumentRestrictionAssign
     */
    public function fakeDocumentRestrictionAssign($documentRestrictionAssignFields = [])
    {
        return new DocumentRestrictionAssign($this->fakeDocumentRestrictionAssignData($documentRestrictionAssignFields));
    }

    /**
     * Get fake data of DocumentRestrictionAssign
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDocumentRestrictionAssignData($documentRestrictionAssignFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'documentRestrictionPolicyID' => $fake->randomDigitNotNull,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'userGroupID' => $fake->randomDigitNotNull
        ], $documentRestrictionAssignFields);
    }
}
