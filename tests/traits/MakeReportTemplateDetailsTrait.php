<?php

use Faker\Factory as Faker;
use App\Models\ReportTemplateDetails;
use App\Repositories\ReportTemplateDetailsRepository;

trait MakeReportTemplateDetailsTrait
{
    /**
     * Create fake instance of ReportTemplateDetails and save it in database
     *
     * @param array $reportTemplateDetailsFields
     * @return ReportTemplateDetails
     */
    public function makeReportTemplateDetails($reportTemplateDetailsFields = [])
    {
        /** @var ReportTemplateDetailsRepository $reportTemplateDetailsRepo */
        $reportTemplateDetailsRepo = App::make(ReportTemplateDetailsRepository::class);
        $theme = $this->fakeReportTemplateDetailsData($reportTemplateDetailsFields);
        return $reportTemplateDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of ReportTemplateDetails
     *
     * @param array $reportTemplateDetailsFields
     * @return ReportTemplateDetails
     */
    public function fakeReportTemplateDetails($reportTemplateDetailsFields = [])
    {
        return new ReportTemplateDetails($this->fakeReportTemplateDetailsData($reportTemplateDetailsFields));
    }

    /**
     * Get fake data of ReportTemplateDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeReportTemplateDetailsData($reportTemplateDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companyReportTemplateID' => $fake->randomDigitNotNull,
            'description' => $fake->word,
            'itemType' => $fake->randomDigitNotNull,
            'sortOrder' => $fake->randomDigitNotNull,
            'masterID' => $fake->randomDigitNotNull,
            'accountType' => $fake->word,
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
        ], $reportTemplateDetailsFields);
    }
}
