<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\MaritialStatus;
use App\Repositories\MaritialStatusRepository;

trait MakeMaritialStatusTrait
{
    /**
     * Create fake instance of MaritialStatus and save it in database
     *
     * @param array $maritialStatusFields
     * @return MaritialStatus
     */
    public function makeMaritialStatus($maritialStatusFields = [])
    {
        /** @var MaritialStatusRepository $maritialStatusRepo */
        $maritialStatusRepo = \App::make(MaritialStatusRepository::class);
        $theme = $this->fakeMaritialStatusData($maritialStatusFields);
        return $maritialStatusRepo->create($theme);
    }

    /**
     * Get fake instance of MaritialStatus
     *
     * @param array $maritialStatusFields
     * @return MaritialStatus
     */
    public function fakeMaritialStatus($maritialStatusFields = [])
    {
        return new MaritialStatus($this->fakeMaritialStatusData($maritialStatusFields));
    }

    /**
     * Get fake data of MaritialStatus
     *
     * @param array $maritialStatusFields
     * @return array
     */
    public function fakeMaritialStatusData($maritialStatusFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'code' => $fake->word,
            'description' => $fake->word,
            'description_O' => $fake->word,
            'noOfkids' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $maritialStatusFields);
    }
}
