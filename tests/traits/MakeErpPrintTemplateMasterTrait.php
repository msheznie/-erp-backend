<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\ErpPrintTemplateMaster;
use App\Repositories\ErpPrintTemplateMasterRepository;

trait MakeErpPrintTemplateMasterTrait
{
    /**
     * Create fake instance of ErpPrintTemplateMaster and save it in database
     *
     * @param array $erpPrintTemplateMasterFields
     * @return ErpPrintTemplateMaster
     */
    public function makeErpPrintTemplateMaster($erpPrintTemplateMasterFields = [])
    {
        /** @var ErpPrintTemplateMasterRepository $erpPrintTemplateMasterRepo */
        $erpPrintTemplateMasterRepo = \App::make(ErpPrintTemplateMasterRepository::class);
        $theme = $this->fakeErpPrintTemplateMasterData($erpPrintTemplateMasterFields);
        return $erpPrintTemplateMasterRepo->create($theme);
    }

    /**
     * Get fake instance of ErpPrintTemplateMaster
     *
     * @param array $erpPrintTemplateMasterFields
     * @return ErpPrintTemplateMaster
     */
    public function fakeErpPrintTemplateMaster($erpPrintTemplateMasterFields = [])
    {
        return new ErpPrintTemplateMaster($this->fakeErpPrintTemplateMasterData($erpPrintTemplateMasterFields));
    }

    /**
     * Get fake data of ErpPrintTemplateMaster
     *
     * @param array $erpPrintTemplateMasterFields
     * @return array
     */
    public function fakeErpPrintTemplateMasterData($erpPrintTemplateMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'printTemplateName' => $fake->word,
            'printTemplateBlade' => $fake->word,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s')
        ], $erpPrintTemplateMasterFields);
    }
}
