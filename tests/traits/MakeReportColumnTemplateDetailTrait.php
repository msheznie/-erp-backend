<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\ReportColumnTemplateDetail;
use App\Repositories\ReportColumnTemplateDetailRepository;

trait MakeReportColumnTemplateDetailTrait
{
    /**
     * Create fake instance of ReportColumnTemplateDetail and save it in database
     *
     * @param array $reportColumnTemplateDetailFields
     * @return ReportColumnTemplateDetail
     */
    public function makeReportColumnTemplateDetail($reportColumnTemplateDetailFields = [])
    {
        /** @var ReportColumnTemplateDetailRepository $reportColumnTemplateDetailRepo */
        $reportColumnTemplateDetailRepo = \App::make(ReportColumnTemplateDetailRepository::class);
        $theme = $this->fakeReportColumnTemplateDetailData($reportColumnTemplateDetailFields);
        return $reportColumnTemplateDetailRepo->create($theme);
    }

    /**
     * Get fake instance of ReportColumnTemplateDetail
     *
     * @param array $reportColumnTemplateDetailFields
     * @return ReportColumnTemplateDetail
     */
    public function fakeReportColumnTemplateDetail($reportColumnTemplateDetailFields = [])
    {
        return new ReportColumnTemplateDetail($this->fakeReportColumnTemplateDetailData($reportColumnTemplateDetailFields));
    }

    /**
     * Get fake data of ReportColumnTemplateDetail
     *
     * @param array $reportColumnTemplateDetailFields
     * @return array
     */
    public function fakeReportColumnTemplateDetailData($reportColumnTemplateDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'reportColumnTemplateID' => $fake->randomDigitNotNull,
            'columnID' => $fake->randomDigitNotNull
        ], $reportColumnTemplateDetailFields);
    }
}
