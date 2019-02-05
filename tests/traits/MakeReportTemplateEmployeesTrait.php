<?php

use Faker\Factory as Faker;
use App\Models\ReportTemplateEmployees;
use App\Repositories\ReportTemplateEmployeesRepository;

trait MakeReportTemplateEmployeesTrait
{
    /**
     * Create fake instance of ReportTemplateEmployees and save it in database
     *
     * @param array $reportTemplateEmployeesFields
     * @return ReportTemplateEmployees
     */
    public function makeReportTemplateEmployees($reportTemplateEmployeesFields = [])
    {
        /** @var ReportTemplateEmployeesRepository $reportTemplateEmployeesRepo */
        $reportTemplateEmployeesRepo = App::make(ReportTemplateEmployeesRepository::class);
        $theme = $this->fakeReportTemplateEmployeesData($reportTemplateEmployeesFields);
        return $reportTemplateEmployeesRepo->create($theme);
    }

    /**
     * Get fake instance of ReportTemplateEmployees
     *
     * @param array $reportTemplateEmployeesFields
     * @return ReportTemplateEmployees
     */
    public function fakeReportTemplateEmployees($reportTemplateEmployeesFields = [])
    {
        return new ReportTemplateEmployees($this->fakeReportTemplateEmployeesData($reportTemplateEmployeesFields));
    }

    /**
     * Get fake data of ReportTemplateEmployees
     *
     * @param array $postFields
     * @return array
     */
    public function fakeReportTemplateEmployeesData($reportTemplateEmployeesFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companyReportTemplateID' => $fake->randomDigitNotNull,
            'userGroupID' => $fake->randomDigitNotNull,
            'employeeSystemID' => $fake->randomDigitNotNull,
            'createdPCID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedPCID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUserID' => $fake->word,
            'modifiedDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $reportTemplateEmployeesFields);
    }
}
