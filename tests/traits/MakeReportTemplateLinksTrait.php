<?php

use Faker\Factory as Faker;
use App\Models\ReportTemplateLinks;
use App\Repositories\ReportTemplateLinksRepository;

trait MakeReportTemplateLinksTrait
{
    /**
     * Create fake instance of ReportTemplateLinks and save it in database
     *
     * @param array $reportTemplateLinksFields
     * @return ReportTemplateLinks
     */
    public function makeReportTemplateLinks($reportTemplateLinksFields = [])
    {
        /** @var ReportTemplateLinksRepository $reportTemplateLinksRepo */
        $reportTemplateLinksRepo = App::make(ReportTemplateLinksRepository::class);
        $theme = $this->fakeReportTemplateLinksData($reportTemplateLinksFields);
        return $reportTemplateLinksRepo->create($theme);
    }

    /**
     * Get fake instance of ReportTemplateLinks
     *
     * @param array $reportTemplateLinksFields
     * @return ReportTemplateLinks
     */
    public function fakeReportTemplateLinks($reportTemplateLinksFields = [])
    {
        return new ReportTemplateLinks($this->fakeReportTemplateLinksData($reportTemplateLinksFields));
    }

    /**
     * Get fake data of ReportTemplateLinks
     *
     * @param array $postFields
     * @return array
     */
    public function fakeReportTemplateLinksData($reportTemplateLinksFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'templateMasterID' => $fake->randomDigitNotNull,
            'templateDetailID' => $fake->randomDigitNotNull,
            'sortOrder' => $fake->randomDigitNotNull,
            'glAutoID' => $fake->randomDigitNotNull,
            'subCategory' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'createdPCID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedPCID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUserID' => $fake->word,
            'modifiedDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $reportTemplateLinksFields);
    }
}
