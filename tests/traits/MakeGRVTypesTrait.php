<?php

use Faker\Factory as Faker;
use App\Models\GRVTypes;
use App\Repositories\GRVTypesRepository;

trait MakeGRVTypesTrait
{
    /**
     * Create fake instance of GRVTypes and save it in database
     *
     * @param array $gRVTypesFields
     * @return GRVTypes
     */
    public function makeGRVTypes($gRVTypesFields = [])
    {
        /** @var GRVTypesRepository $gRVTypesRepo */
        $gRVTypesRepo = App::make(GRVTypesRepository::class);
        $theme = $this->fakeGRVTypesData($gRVTypesFields);
        return $gRVTypesRepo->create($theme);
    }

    /**
     * Get fake instance of GRVTypes
     *
     * @param array $gRVTypesFields
     * @return GRVTypes
     */
    public function fakeGRVTypes($gRVTypesFields = [])
    {
        return new GRVTypes($this->fakeGRVTypesData($gRVTypesFields));
    }

    /**
     * Get fake data of GRVTypes
     *
     * @param array $postFields
     * @return array
     */
    public function fakeGRVTypesData($gRVTypesFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'idERP_GrvTpes' => $fake->word,
            'des' => $fake->word
        ], $gRVTypesFields);
    }
}
