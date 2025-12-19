<?php

use Faker\Factory as Faker;
use App\Models\ReportTemplateDocument;
use App\Repositories\ReportTemplateDocumentRepository;

trait MakeReportTemplateDocumentTrait
{
    /**
     * Create fake instance of ReportTemplateDocument and save it in database
     *
     * @param array $reportTemplateDocumentFields
     * @return ReportTemplateDocument
     */
    public function makeReportTemplateDocument($reportTemplateDocumentFields = [])
    {
        /** @var ReportTemplateDocumentRepository $reportTemplateDocumentRepo */
        $reportTemplateDocumentRepo = App::make(ReportTemplateDocumentRepository::class);
        $theme = $this->fakeReportTemplateDocumentData($reportTemplateDocumentFields);
        return $reportTemplateDocumentRepo->create($theme);
    }

    /**
     * Get fake instance of ReportTemplateDocument
     *
     * @param array $reportTemplateDocumentFields
     * @return ReportTemplateDocument
     */
    public function fakeReportTemplateDocument($reportTemplateDocumentFields = [])
    {
        return new ReportTemplateDocument($this->fakeReportTemplateDocumentData($reportTemplateDocumentFields));
    }

    /**
     * Get fake data of ReportTemplateDocument
     *
     * @param array $postFields
     * @return array
     */
    public function fakeReportTemplateDocumentData($reportTemplateDocumentFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'isActive' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $reportTemplateDocumentFields);
    }
}
