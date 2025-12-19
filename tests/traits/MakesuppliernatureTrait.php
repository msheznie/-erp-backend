<?php

use Faker\Factory as Faker;
use App\Models\suppliernature;
use App\Repositories\suppliernatureRepository;

trait MakesuppliernatureTrait
{
    /**
     * Create fake instance of suppliernature and save it in database
     *
     * @param array $suppliernatureFields
     * @return suppliernature
     */
    public function makesuppliernature($suppliernatureFields = [])
    {
        /** @var suppliernatureRepository $suppliernatureRepo */
        $suppliernatureRepo = App::make(suppliernatureRepository::class);
        $theme = $this->fakesuppliernatureData($suppliernatureFields);
        return $suppliernatureRepo->create($theme);
    }

    /**
     * Get fake instance of suppliernature
     *
     * @param array $suppliernatureFields
     * @return suppliernature
     */
    public function fakesuppliernature($suppliernatureFields = [])
    {
        return new suppliernature($this->fakesuppliernatureData($suppliernatureFields));
    }

    /**
     * Get fake data of suppliernature
     *
     * @param array $postFields
     * @return array
     */
    public function fakesuppliernatureData($suppliernatureFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'natureDescription' => $fake->word
        ], $suppliernatureFields);
    }
}
