<?php

use Faker\Factory as Faker;
use App\Models\Year;
use App\Repositories\YearRepository;

trait MakeYearTrait
{
    /**
     * Create fake instance of Year and save it in database
     *
     * @param array $yearFields
     * @return Year
     */
    public function makeYear($yearFields = [])
    {
        /** @var YearRepository $yearRepo */
        $yearRepo = App::make(YearRepository::class);
        $theme = $this->fakeYearData($yearFields);
        return $yearRepo->create($theme);
    }

    /**
     * Get fake instance of Year
     *
     * @param array $yearFields
     * @return Year
     */
    public function fakeYear($yearFields = [])
    {
        return new Year($this->fakeYearData($yearFields));
    }

    /**
     * Get fake data of Year
     *
     * @param array $postFields
     * @return array
     */
    public function fakeYearData($yearFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'year' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $yearFields);
    }
}
