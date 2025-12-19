<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\Religion;
use App\Repositories\ReligionRepository;

trait MakeReligionTrait
{
    /**
     * Create fake instance of Religion and save it in database
     *
     * @param array $religionFields
     * @return Religion
     */
    public function makeReligion($religionFields = [])
    {
        /** @var ReligionRepository $religionRepo */
        $religionRepo = \App::make(ReligionRepository::class);
        $theme = $this->fakeReligionData($religionFields);
        return $religionRepo->create($theme);
    }

    /**
     * Get fake instance of Religion
     *
     * @param array $religionFields
     * @return Religion
     */
    public function fakeReligion($religionFields = [])
    {
        return new Religion($this->fakeReligionData($religionFields));
    }

    /**
     * Get fake data of Religion
     *
     * @param array $religionFields
     * @return array
     */
    public function fakeReligionData($religionFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'religionName' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $religionFields);
    }
}
