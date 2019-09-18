<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\HrmsDocumentAttachments;
use App\Repositories\HrmsDocumentAttachmentsRepository;

trait MakeHrmsDocumentAttachmentsTrait
{
    /**
     * Create fake instance of HrmsDocumentAttachments and save it in database
     *
     * @param array $hrmsDocumentAttachmentsFields
     * @return HrmsDocumentAttachments
     */
    public function makeHrmsDocumentAttachments($hrmsDocumentAttachmentsFields = [])
    {
        /** @var HrmsDocumentAttachmentsRepository $hrmsDocumentAttachmentsRepo */
        $hrmsDocumentAttachmentsRepo = \App::make(HrmsDocumentAttachmentsRepository::class);
        $theme = $this->fakeHrmsDocumentAttachmentsData($hrmsDocumentAttachmentsFields);
        return $hrmsDocumentAttachmentsRepo->create($theme);
    }

    /**
     * Get fake instance of HrmsDocumentAttachments
     *
     * @param array $hrmsDocumentAttachmentsFields
     * @return HrmsDocumentAttachments
     */
    public function fakeHrmsDocumentAttachments($hrmsDocumentAttachmentsFields = [])
    {
        return new HrmsDocumentAttachments($this->fakeHrmsDocumentAttachmentsData($hrmsDocumentAttachmentsFields));
    }

    /**
     * Get fake data of HrmsDocumentAttachments
     *
     * @param array $hrmsDocumentAttachmentsFields
     * @return array
     */
    public function fakeHrmsDocumentAttachmentsData($hrmsDocumentAttachmentsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companyID' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentSystemCode' => $fake->randomDigitNotNull,
            'attachmentDescription' => $fake->text,
            'myFileName' => $fake->word,
            'docExpirtyDate' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $hrmsDocumentAttachmentsFields);
    }
}
