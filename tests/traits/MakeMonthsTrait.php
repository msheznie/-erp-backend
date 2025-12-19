<?php

use Faker\Factory as Faker;
use App\Models\Months;
use App\Repositories\MonthsRepository;

trait MakeMonthsTrait
{
    /**
     * Create fake instance of Months and save it in database
     *
     * @param array $monthsFields
     * @return Months
     */
    public function makeMonths($monthsFields = [])
    {
        /** @var MonthsRepository $monthsRepo */
        $monthsRepo = App::make(MonthsRepository::class);
        $theme = $this->fakeMonthsData($monthsFields);
        return $monthsRepo->create($theme);
    }

    /**
     * Get fake instance of Months
     *
     * @param array $monthsFields
     * @return Months
     */
    public function fakeMonths($monthsFields = [])
    {
        return new Months($this->fakeMonthsData($monthsFields));
    }

    /**
     * Get fake data of Months
     *
     * @param array $postFields
     * @return array
     */
    public function fakeMonthsData($monthsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'monthDes' => $fake->word
        ], $monthsFields);
    }
}
