<?php

use Faker\Factory as Faker;
use App\Models\PoAddonsRefferedBack;
use App\Repositories\PoAddonsRefferedBackRepository;

trait MakePoAddonsRefferedBackTrait
{
    /**
     * Create fake instance of PoAddonsRefferedBack and save it in database
     *
     * @param array $poAddonsRefferedBackFields
     * @return PoAddonsRefferedBack
     */
    public function makePoAddonsRefferedBack($poAddonsRefferedBackFields = [])
    {
        /** @var PoAddonsRefferedBackRepository $poAddonsRefferedBackRepo */
        $poAddonsRefferedBackRepo = App::make(PoAddonsRefferedBackRepository::class);
        $theme = $this->fakePoAddonsRefferedBackData($poAddonsRefferedBackFields);
        return $poAddonsRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of PoAddonsRefferedBack
     *
     * @param array $poAddonsRefferedBackFields
     * @return PoAddonsRefferedBack
     */
    public function fakePoAddonsRefferedBack($poAddonsRefferedBackFields = [])
    {
        return new PoAddonsRefferedBack($this->fakePoAddonsRefferedBackData($poAddonsRefferedBackFields));
    }

    /**
     * Get fake data of PoAddonsRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakePoAddonsRefferedBackData($poAddonsRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'idpoAddons' => $fake->randomDigitNotNull,
            'poId' => $fake->randomDigitNotNull,
            'idaddOnCostCategories' => $fake->randomDigitNotNull,
            'supplierID' => $fake->randomDigitNotNull,
            'currencyID' => $fake->randomDigitNotNull,
            'amount' => $fake->randomDigitNotNull,
            'glCode' => $fake->word,
            'timesReferred' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $poAddonsRefferedBackFields);
    }
}
