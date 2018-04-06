<?php

use Faker\Factory as Faker;
use App\Models\DocumentAttachmentType;
use App\Repositories\DocumentAttachmentTypeRepository;

trait MakeDocumentAttachmentTypeTrait
{
    /**
     * Create fake instance of DocumentAttachmentType and save it in database
     *
     * @param array $documentAttachmentTypeFields
     * @return DocumentAttachmentType
     */
    public function makeDocumentAttachmentType($documentAttachmentTypeFields = [])
    {
        /** @var DocumentAttachmentTypeRepository $documentAttachmentTypeRepo */
        $documentAttachmentTypeRepo = App::make(DocumentAttachmentTypeRepository::class);
        $theme = $this->fakeDocumentAttachmentTypeData($documentAttachmentTypeFields);
        return $documentAttachmentTypeRepo->create($theme);
    }

    /**
     * Get fake instance of DocumentAttachmentType
     *
     * @param array $documentAttachmentTypeFields
     * @return DocumentAttachmentType
     */
    public function fakeDocumentAttachmentType($documentAttachmentTypeFields = [])
    {
        return new DocumentAttachmentType($this->fakeDocumentAttachmentTypeData($documentAttachmentTypeFields));
    }

    /**
     * Get fake data of DocumentAttachmentType
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDocumentAttachmentTypeData($documentAttachmentTypeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'documentID' => $fake->word,
            'description' => $fake->word,
            'timestamp' => $fake->word
        ], $documentAttachmentTypeFields);
    }
}
