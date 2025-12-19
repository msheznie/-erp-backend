<?php

use Faker\Factory as Faker;
use App\Models\DocumentRestrictionPolicy;
use App\Repositories\DocumentRestrictionPolicyRepository;

trait MakeDocumentRestrictionPolicyTrait
{
    /**
     * Create fake instance of DocumentRestrictionPolicy and save it in database
     *
     * @param array $documentRestrictionPolicyFields
     * @return DocumentRestrictionPolicy
     */
    public function makeDocumentRestrictionPolicy($documentRestrictionPolicyFields = [])
    {
        /** @var DocumentRestrictionPolicyRepository $documentRestrictionPolicyRepo */
        $documentRestrictionPolicyRepo = App::make(DocumentRestrictionPolicyRepository::class);
        $theme = $this->fakeDocumentRestrictionPolicyData($documentRestrictionPolicyFields);
        return $documentRestrictionPolicyRepo->create($theme);
    }

    /**
     * Get fake instance of DocumentRestrictionPolicy
     *
     * @param array $documentRestrictionPolicyFields
     * @return DocumentRestrictionPolicy
     */
    public function fakeDocumentRestrictionPolicy($documentRestrictionPolicyFields = [])
    {
        return new DocumentRestrictionPolicy($this->fakeDocumentRestrictionPolicyData($documentRestrictionPolicyFields));
    }

    /**
     * Get fake data of DocumentRestrictionPolicy
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDocumentRestrictionPolicyData($documentRestrictionPolicyFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'policyDescription' => $fake->word
        ], $documentRestrictionPolicyFields);
    }
}
