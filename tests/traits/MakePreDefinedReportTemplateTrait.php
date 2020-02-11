<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\PreDefinedReportTemplate;
use App\Repositories\PreDefinedReportTemplateRepository;

trait MakePreDefinedReportTemplateTrait
{
    /**
     * Create fake instance of PreDefinedReportTemplate and save it in database
     *
     * @param array $preDefinedReportTemplateFields
     * @return PreDefinedReportTemplate
     */
    public function makePreDefinedReportTemplate($preDefinedReportTemplateFields = [])
    {
        /** @var PreDefinedReportTemplateRepository $preDefinedReportTemplateRepo */
        $preDefinedReportTemplateRepo = \App::make(PreDefinedReportTemplateRepository::class);
        $theme = $this->fakePreDefinedReportTemplateData($preDefinedReportTemplateFields);
        return $preDefinedReportTemplateRepo->create($theme);
    }

    /**
     * Get fake instance of PreDefinedReportTemplate
     *
     * @param array $preDefinedReportTemplateFields
     * @return PreDefinedReportTemplate
     */
    public function fakePreDefinedReportTemplate($preDefinedReportTemplateFields = [])
    {
        return new PreDefinedReportTemplate($this->fakePreDefinedReportTemplateData($preDefinedReportTemplateFields));
    }

    /**
     * Get fake data of PreDefinedReportTemplate
     *
     * @param array $preDefinedReportTemplateFields
     * @return array
     */
    public function fakePreDefinedReportTemplateData($preDefinedReportTemplateFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'preDefinedReportTemplateCode' => $fake->word,
            'templateName' => $fake->word
        ], $preDefinedReportTemplateFields);
    }
}
