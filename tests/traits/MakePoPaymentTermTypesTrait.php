<?php

use Faker\Factory as Faker;
use App\Models\PoPaymentTermTypes;
use App\Repositories\PoPaymentTermTypesRepository;

trait MakePoPaymentTermTypesTrait
{
    /**
     * Create fake instance of PoPaymentTermTypes and save it in database
     *
     * @param array $poPaymentTermTypesFields
     * @return PoPaymentTermTypes
     */
    public function makePoPaymentTermTypes($poPaymentTermTypesFields = [])
    {
        /** @var PoPaymentTermTypesRepository $poPaymentTermTypesRepo */
        $poPaymentTermTypesRepo = App::make(PoPaymentTermTypesRepository::class);
        $theme = $this->fakePoPaymentTermTypesData($poPaymentTermTypesFields);
        return $poPaymentTermTypesRepo->create($theme);
    }

    /**
     * Get fake instance of PoPaymentTermTypes
     *
     * @param array $poPaymentTermTypesFields
     * @return PoPaymentTermTypes
     */
    public function fakePoPaymentTermTypes($poPaymentTermTypesFields = [])
    {
        return new PoPaymentTermTypes($this->fakePoPaymentTermTypesData($poPaymentTermTypesFields));
    }

    /**
     * Get fake data of PoPaymentTermTypes
     *
     * @param array $postFields
     * @return array
     */
    public function fakePoPaymentTermTypesData($poPaymentTermTypesFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'categoryDescription' => $fake->word
        ], $poPaymentTermTypesFields);
    }
}
