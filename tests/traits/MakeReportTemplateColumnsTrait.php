<?php

use Faker\Factory as Faker;
use App\Models\ReportTemplateColumns;
use App\Repositories\ReportTemplateColumnsRepository;

trait MakeReportTemplateColumnsTrait
{
    /**
     * Create fake instance of ReportTemplateColumns and save it in database
     *
     * @param array $reportTemplateColumnsFields
     * @return ReportTemplateColumns
     */
    public function makeReportTemplateColumns($reportTemplateColumnsFields = [])
    {
        /** @var ReportTemplateColumnsRepository $reportTemplateColumnsRepo */
        $reportTemplateColumnsRepo = App::make(ReportTemplateColumnsRepository::class);
        $theme = $this->fakeReportTemplateColumnsData($reportTemplateColumnsFields);
        return $reportTemplateColumnsRepo->create($theme);
    }

    /**
     * Get fake instance of ReportTemplateColumns
     *
     * @param array $reportTemplateColumnsFields
     * @return ReportTemplateColumns
     */
    public function fakeReportTemplateColumns($reportTemplateColumnsFields = [])
    {
        return new ReportTemplateColumns($this->fakeReportTemplateColumnsData($reportTemplateColumnsFields));
    }

    /**
     * Get fake data of ReportTemplateColumns
     *
     * @param array $postFields
     * @return array
     */
    public function fakeReportTemplateColumnsData($reportTemplateColumnsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'description' => $fake->word,
            'shortCode' => $fake->word,
            'type' => $fake->word,
            'createdPCID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedPCID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUserID' => $fake->word,
            'modifiedDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $reportTemplateColumnsFields);
    }
}
