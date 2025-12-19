<?php

use Faker\Factory as Faker;
use App\Models\ReportTemplate;
use App\Repositories\ReportTemplateRepository;

trait MakeReportTemplateTrait
{
    /**
     * Create fake instance of ReportTemplate and save it in database
     *
     * @param array $reportTemplateFields
     * @return ReportTemplate
     */
    public function makeReportTemplate($reportTemplateFields = [])
    {
        /** @var ReportTemplateRepository $reportTemplateRepo */
        $reportTemplateRepo = App::make(ReportTemplateRepository::class);
        $theme = $this->fakeReportTemplateData($reportTemplateFields);
        return $reportTemplateRepo->create($theme);
    }

    /**
     * Get fake instance of ReportTemplate
     *
     * @param array $reportTemplateFields
     * @return ReportTemplate
     */
    public function fakeReportTemplate($reportTemplateFields = [])
    {
        return new ReportTemplate($this->fakeReportTemplateData($reportTemplateFields));
    }

    /**
     * Get fake data of ReportTemplate
     *
     * @param array $postFields
     * @return array
     */
    public function fakeReportTemplateData($reportTemplateFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'description' => $fake->word,
            'reportID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'isActive' => $fake->randomDigitNotNull,
            'isMPREnabled' => $fake->randomDigitNotNull,
            'isAssignToGroup' => $fake->randomDigitNotNull,
            'createdPCID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedPCID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUserID' => $fake->word,
            'modifiedDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $reportTemplateFields);
    }
}
