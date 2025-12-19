<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\ErpDocumentTemplate;
use App\Repositories\ErpDocumentTemplateRepository;

trait MakeErpDocumentTemplateTrait
{
    /**
     * Create fake instance of ErpDocumentTemplate and save it in database
     *
     * @param array $erpDocumentTemplateFields
     * @return ErpDocumentTemplate
     */
    public function makeErpDocumentTemplate($erpDocumentTemplateFields = [])
    {
        /** @var ErpDocumentTemplateRepository $erpDocumentTemplateRepo */
        $erpDocumentTemplateRepo = \App::make(ErpDocumentTemplateRepository::class);
        $theme = $this->fakeErpDocumentTemplateData($erpDocumentTemplateFields);
        return $erpDocumentTemplateRepo->create($theme);
    }

    /**
     * Get fake instance of ErpDocumentTemplate
     *
     * @param array $erpDocumentTemplateFields
     * @return ErpDocumentTemplate
     */
    public function fakeErpDocumentTemplate($erpDocumentTemplateFields = [])
    {
        return new ErpDocumentTemplate($this->fakeErpDocumentTemplateData($erpDocumentTemplateFields));
    }

    /**
     * Get fake data of ErpDocumentTemplate
     *
     * @param array $erpDocumentTemplateFields
     * @return array
     */
    public function fakeErpDocumentTemplateData($erpDocumentTemplateFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'documentID' => $fake->randomDigitNotNull,
            'companyID' => $fake->randomDigitNotNull,
            'printTemplateID' => $fake->randomDigitNotNull
        ], $erpDocumentTemplateFields);
    }
}
