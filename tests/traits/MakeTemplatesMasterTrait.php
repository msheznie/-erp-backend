<?php

use Faker\Factory as Faker;
use App\Models\TemplatesMaster;
use App\Repositories\TemplatesMasterRepository;

trait MakeTemplatesMasterTrait
{
    /**
     * Create fake instance of TemplatesMaster and save it in database
     *
     * @param array $templatesMasterFields
     * @return TemplatesMaster
     */
    public function makeTemplatesMaster($templatesMasterFields = [])
    {
        /** @var TemplatesMasterRepository $templatesMasterRepo */
        $templatesMasterRepo = App::make(TemplatesMasterRepository::class);
        $theme = $this->fakeTemplatesMasterData($templatesMasterFields);
        return $templatesMasterRepo->create($theme);
    }

    /**
     * Get fake instance of TemplatesMaster
     *
     * @param array $templatesMasterFields
     * @return TemplatesMaster
     */
    public function fakeTemplatesMaster($templatesMasterFields = [])
    {
        return new TemplatesMaster($this->fakeTemplatesMasterData($templatesMasterFields));
    }

    /**
     * Get fake data of TemplatesMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTemplatesMasterData($templatesMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'templateDescription' => $fake->word,
            'templateType' => $fake->word,
            'templateReportName' => $fake->word,
            'isActive' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $templatesMasterFields);
    }
}
