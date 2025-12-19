<?php

use Faker\Factory as Faker;
use App\Models\CompanyDocumentAttachment;
use App\Repositories\CompanyDocumentAttachmentRepository;

trait MakeCompanyDocumentAttachmentTrait
{
    /**
     * Create fake instance of CompanyDocumentAttachment and save it in database
     *
     * @param array $companyDocumentAttachmentFields
     * @return CompanyDocumentAttachment
     */
    public function makeCompanyDocumentAttachment($companyDocumentAttachmentFields = [])
    {
        /** @var CompanyDocumentAttachmentRepository $companyDocumentAttachmentRepo */
        $companyDocumentAttachmentRepo = App::make(CompanyDocumentAttachmentRepository::class);
        $theme = $this->fakeCompanyDocumentAttachmentData($companyDocumentAttachmentFields);
        return $companyDocumentAttachmentRepo->create($theme);
    }

    /**
     * Get fake instance of CompanyDocumentAttachment
     *
     * @param array $companyDocumentAttachmentFields
     * @return CompanyDocumentAttachment
     */
    public function fakeCompanyDocumentAttachment($companyDocumentAttachmentFields = [])
    {
        return new CompanyDocumentAttachment($this->fakeCompanyDocumentAttachmentData($companyDocumentAttachmentFields));
    }

    /**
     * Get fake data of CompanyDocumentAttachment
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCompanyDocumentAttachmentData($companyDocumentAttachmentFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'docRefNumber' => $fake->word,
            'isAttachmentYN' => $fake->randomDigitNotNull,
            'sendEmailYN' => $fake->randomDigitNotNull,
            'codeGeneratorFormat' => $fake->word,
            'isAmountApproval' => $fake->randomDigitNotNull,
            'isServiceLineApproval' => $fake->randomDigitNotNull,
            'blockYN' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $companyDocumentAttachmentFields);
    }
}
