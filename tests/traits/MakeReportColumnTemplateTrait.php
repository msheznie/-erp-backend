<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\ReportColumnTemplate;
use App\Repositories\ReportColumnTemplateRepository;

trait MakeReportColumnTemplateTrait
{
    /**
     * Create fake instance of ReportColumnTemplate and save it in database
     *
     * @param array $reportColumnTemplateFields
     * @return ReportColumnTemplate
     */
    public function makeReportColumnTemplate($reportColumnTemplateFields = [])
    {
        /** @var ReportColumnTemplateRepository $reportColumnTemplateRepo */
        $reportColumnTemplateRepo = \App::make(ReportColumnTemplateRepository::class);
        $theme = $this->fakeReportColumnTemplateData($reportColumnTemplateFields);
        return $reportColumnTemplateRepo->create($theme);
    }

    /**
     * Get fake instance of ReportColumnTemplate
     *
     * @param array $reportColumnTemplateFields
     * @return ReportColumnTemplate
     */
    public function fakeReportColumnTemplate($reportColumnTemplateFields = [])
    {
        return new ReportColumnTemplate($this->fakeReportColumnTemplateData($reportColumnTemplateFields));
    }

    /**
     * Get fake data of ReportColumnTemplate
     *
     * @param array $reportColumnTemplateFields
     * @return array
     */
    public function fakeReportColumnTemplateData($reportColumnTemplateFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'templateName' => $fake->word,
            'templateImage' => $fake->word
        ], $reportColumnTemplateFields);
    }
}
