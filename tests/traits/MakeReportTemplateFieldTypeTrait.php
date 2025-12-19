<?php

use Faker\Factory as Faker;
use App\Models\ReportTemplateFieldType;
use App\Repositories\ReportTemplateFieldTypeRepository;

trait MakeReportTemplateFieldTypeTrait
{
    /**
     * Create fake instance of ReportTemplateFieldType and save it in database
     *
     * @param array $reportTemplateFieldTypeFields
     * @return ReportTemplateFieldType
     */
    public function makeReportTemplateFieldType($reportTemplateFieldTypeFields = [])
    {
        /** @var ReportTemplateFieldTypeRepository $reportTemplateFieldTypeRepo */
        $reportTemplateFieldTypeRepo = App::make(ReportTemplateFieldTypeRepository::class);
        $theme = $this->fakeReportTemplateFieldTypeData($reportTemplateFieldTypeFields);
        return $reportTemplateFieldTypeRepo->create($theme);
    }

    /**
     * Get fake instance of ReportTemplateFieldType
     *
     * @param array $reportTemplateFieldTypeFields
     * @return ReportTemplateFieldType
     */
    public function fakeReportTemplateFieldType($reportTemplateFieldTypeFields = [])
    {
        return new ReportTemplateFieldType($this->fakeReportTemplateFieldTypeData($reportTemplateFieldTypeFields));
    }

    /**
     * Get fake data of ReportTemplateFieldType
     *
     * @param array $postFields
     * @return array
     */
    public function fakeReportTemplateFieldTypeData($reportTemplateFieldTypeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'fieldType' => $fake->word
        ], $reportTemplateFieldTypeFields);
    }
}
