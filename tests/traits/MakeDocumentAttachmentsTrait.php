<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\DocumentAttachments;
use App\Repositories\DocumentAttachmentsRepository;

trait MakeDocumentAttachmentsTrait
{
    /**
     * Create fake instance of DocumentAttachments and save it in database
     *
     * @param array $documentAttachmentsFields
     * @return DocumentAttachments
     */
    public function makeDocumentAttachments($documentAttachmentsFields = [])
    {
        /** @var DocumentAttachmentsRepository $documentAttachmentsRepo */
        $documentAttachmentsRepo = App::make(DocumentAttachmentsRepository::class);
        $theme = $this->fakeDocumentAttachmentsData($documentAttachmentsFields);
        return $documentAttachmentsRepo->create($theme);
    }

    /**
     * Get fake instance of DocumentAttachments
     *
     * @param array $documentAttachmentsFields
     * @return DocumentAttachments
     */
    public function fakeDocumentAttachments($documentAttachmentsFields = [])
    {
        return new DocumentAttachments($this->fakeDocumentAttachmentsData($documentAttachmentsFields));
    }

    /**
     * Get fake data of DocumentAttachments
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDocumentAttachmentsData($documentAttachmentsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'documentSystemCode' => $fake->randomDigitNotNull,
            'attachmentDescription' => $fake->text,
            'originalFileName' => $fake->word,
            'myFileName' => $fake->word,
            'docExpirtyDate' => $fake->date('Y-m-d H:i:s'),
            'attachmentType' => $fake->randomDigitNotNull,
            'sizeInKbs' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $documentAttachmentsFields);
    }
}
