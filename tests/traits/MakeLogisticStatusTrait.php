<?php

use Faker\Factory as Faker;
use App\Models\LogisticStatus;
use App\Repositories\LogisticStatusRepository;

trait MakeLogisticStatusTrait
{
    /**
     * Create fake instance of LogisticStatus and save it in database
     *
     * @param array $logisticStatusFields
     * @return LogisticStatus
     */
    public function makeLogisticStatus($logisticStatusFields = [])
    {
        /** @var LogisticStatusRepository $logisticStatusRepo */
        $logisticStatusRepo = App::make(LogisticStatusRepository::class);
        $theme = $this->fakeLogisticStatusData($logisticStatusFields);
        return $logisticStatusRepo->create($theme);
    }

    /**
     * Get fake instance of LogisticStatus
     *
     * @param array $logisticStatusFields
     * @return LogisticStatus
     */
    public function fakeLogisticStatus($logisticStatusFields = [])
    {
        return new LogisticStatus($this->fakeLogisticStatusData($logisticStatusFields));
    }

    /**
     * Get fake data of LogisticStatus
     *
     * @param array $postFields
     * @return array
     */
    public function fakeLogisticStatusData($logisticStatusFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'statusDescriptions' => $fake->word,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdPCID' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $logisticStatusFields);
    }
}
