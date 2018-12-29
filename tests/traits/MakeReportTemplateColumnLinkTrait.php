<?php

use Faker\Factory as Faker;
use App\Models\ReportTemplateColumnLink;
use App\Repositories\ReportTemplateColumnLinkRepository;

trait MakeReportTemplateColumnLinkTrait
{
    /**
     * Create fake instance of ReportTemplateColumnLink and save it in database
     *
     * @param array $reportTemplateColumnLinkFields
     * @return ReportTemplateColumnLink
     */
    public function makeReportTemplateColumnLink($reportTemplateColumnLinkFields = [])
    {
        /** @var ReportTemplateColumnLinkRepository $reportTemplateColumnLinkRepo */
        $reportTemplateColumnLinkRepo = App::make(ReportTemplateColumnLinkRepository::class);
        $theme = $this->fakeReportTemplateColumnLinkData($reportTemplateColumnLinkFields);
        return $reportTemplateColumnLinkRepo->create($theme);
    }

    /**
     * Get fake instance of ReportTemplateColumnLink
     *
     * @param array $reportTemplateColumnLinkFields
     * @return ReportTemplateColumnLink
     */
    public function fakeReportTemplateColumnLink($reportTemplateColumnLinkFields = [])
    {
        return new ReportTemplateColumnLink($this->fakeReportTemplateColumnLinkData($reportTemplateColumnLinkFields));
    }

    /**
     * Get fake data of ReportTemplateColumnLink
     *
     * @param array $postFields
     * @return array
     */
    public function fakeReportTemplateColumnLinkData($reportTemplateColumnLinkFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'columnID' => $fake->randomDigitNotNull,
            'templateID' => $fake->randomDigitNotNull,
            'description' => $fake->word,
            'shortCode' => $fake->word,
            'type' => $fake->word,
            'sortOrder' => $fake->randomDigitNotNull,
            'createdPCID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedPCID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUserID' => $fake->word,
            'modifiedDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $reportTemplateColumnLinkFields);
    }
}
