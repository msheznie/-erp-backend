<?php

use Faker\Factory as Faker;
use App\Models\GposPaymentGlConfigMaster;
use App\Repositories\GposPaymentGlConfigMasterRepository;

trait MakeGposPaymentGlConfigMasterTrait
{
    /**
     * Create fake instance of GposPaymentGlConfigMaster and save it in database
     *
     * @param array $gposPaymentGlConfigMasterFields
     * @return GposPaymentGlConfigMaster
     */
    public function makeGposPaymentGlConfigMaster($gposPaymentGlConfigMasterFields = [])
    {
        /** @var GposPaymentGlConfigMasterRepository $gposPaymentGlConfigMasterRepo */
        $gposPaymentGlConfigMasterRepo = App::make(GposPaymentGlConfigMasterRepository::class);
        $theme = $this->fakeGposPaymentGlConfigMasterData($gposPaymentGlConfigMasterFields);
        return $gposPaymentGlConfigMasterRepo->create($theme);
    }

    /**
     * Get fake instance of GposPaymentGlConfigMaster
     *
     * @param array $gposPaymentGlConfigMasterFields
     * @return GposPaymentGlConfigMaster
     */
    public function fakeGposPaymentGlConfigMaster($gposPaymentGlConfigMasterFields = [])
    {
        return new GposPaymentGlConfigMaster($this->fakeGposPaymentGlConfigMasterData($gposPaymentGlConfigMasterFields));
    }

    /**
     * Get fake data of GposPaymentGlConfigMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeGposPaymentGlConfigMasterData($gposPaymentGlConfigMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'description' => $fake->word,
            'glAccountType' => $fake->randomDigitNotNull,
            'queryString' => $fake->text,
            'image' => $fake->word,
            'isActive' => $fake->randomDigitNotNull,
            'sortOrder' => $fake->randomDigitNotNull,
            'selectBoxName' => $fake->word,
            'timesstamp' => $fake->date('Y-m-d H:i:s')
        ], $gposPaymentGlConfigMasterFields);
    }
}
