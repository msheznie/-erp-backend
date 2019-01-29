<?php

use Faker\Factory as Faker;
use App\Models\ReportTemplateNumbers;
use App\Repositories\ReportTemplateNumbersRepository;

trait MakeReportTemplateNumbersTrait
{
    /**
     * Create fake instance of ReportTemplateNumbers and save it in database
     *
     * @param array $reportTemplateNumbersFields
     * @return ReportTemplateNumbers
     */
    public function makeReportTemplateNumbers($reportTemplateNumbersFields = [])
    {
        /** @var ReportTemplateNumbersRepository $reportTemplateNumbersRepo */
        $reportTemplateNumbersRepo = App::make(ReportTemplateNumbersRepository::class);
        $theme = $this->fakeReportTemplateNumbersData($reportTemplateNumbersFields);
        return $reportTemplateNumbersRepo->create($theme);
    }

    /**
     * Get fake instance of ReportTemplateNumbers
     *
     * @param array $reportTemplateNumbersFields
     * @return ReportTemplateNumbers
     */
    public function fakeReportTemplateNumbers($reportTemplateNumbersFields = [])
    {
        return new ReportTemplateNumbers($this->fakeReportTemplateNumbersData($reportTemplateNumbersFields));
    }

    /**
     * Get fake data of ReportTemplateNumbers
     *
     * @param array $postFields
     * @return array
     */
    public function fakeReportTemplateNumbersData($reportTemplateNumbersFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'value' => $fake->randomDigitNotNull,
            'timesStamp' => $fake->date('Y-m-d H:i:s')
        ], $reportTemplateNumbersFields);
    }
}
