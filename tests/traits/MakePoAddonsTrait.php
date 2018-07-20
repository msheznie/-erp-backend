<?php

use Faker\Factory as Faker;
use App\Models\PoAddons;
use App\Repositories\PoAddonsRepository;

trait MakePoAddonsTrait
{
    /**
     * Create fake instance of PoAddons and save it in database
     *
     * @param array $poAddonsFields
     * @return PoAddons
     */
    public function makePoAddons($poAddonsFields = [])
    {
        /** @var PoAddonsRepository $poAddonsRepo */
        $poAddonsRepo = App::make(PoAddonsRepository::class);
        $theme = $this->fakePoAddonsData($poAddonsFields);
        return $poAddonsRepo->create($theme);
    }

    /**
     * Get fake instance of PoAddons
     *
     * @param array $poAddonsFields
     * @return PoAddons
     */
    public function fakePoAddons($poAddonsFields = [])
    {
        return new PoAddons($this->fakePoAddonsData($poAddonsFields));
    }

    /**
     * Get fake data of PoAddons
     *
     * @param array $postFields
     * @return array
     */
    public function fakePoAddonsData($poAddonsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'poId' => $fake->randomDigitNotNull,
            'idaddOnCostCategories' => $fake->randomDigitNotNull,
            'supplierID' => $fake->randomDigitNotNull,
            'currencyID' => $fake->randomDigitNotNull,
            'amount' => $fake->randomDigitNotNull,
            'glCode' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $poAddonsFields);
    }
}
