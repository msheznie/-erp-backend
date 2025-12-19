<?php

use Faker\Factory as Faker;
use App\Models\LogisticShippingStatus;
use App\Repositories\LogisticShippingStatusRepository;

trait MakeLogisticShippingStatusTrait
{
    /**
     * Create fake instance of LogisticShippingStatus and save it in database
     *
     * @param array $logisticShippingStatusFields
     * @return LogisticShippingStatus
     */
    public function makeLogisticShippingStatus($logisticShippingStatusFields = [])
    {
        /** @var LogisticShippingStatusRepository $logisticShippingStatusRepo */
        $logisticShippingStatusRepo = App::make(LogisticShippingStatusRepository::class);
        $theme = $this->fakeLogisticShippingStatusData($logisticShippingStatusFields);
        return $logisticShippingStatusRepo->create($theme);
    }

    /**
     * Get fake instance of LogisticShippingStatus
     *
     * @param array $logisticShippingStatusFields
     * @return LogisticShippingStatus
     */
    public function fakeLogisticShippingStatus($logisticShippingStatusFields = [])
    {
        return new LogisticShippingStatus($this->fakeLogisticShippingStatusData($logisticShippingStatusFields));
    }

    /**
     * Get fake data of LogisticShippingStatus
     *
     * @param array $postFields
     * @return array
     */
    public function fakeLogisticShippingStatusData($logisticShippingStatusFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'logisticMasterID' => $fake->randomDigitNotNull,
            'shippingStatusID' => $fake->randomDigitNotNull,
            'statusDate' => $fake->date('Y-m-d H:i:s'),
            'statusComment' => $fake->word,
            'createdUserID' => $fake->word,
            'createdPCID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $logisticShippingStatusFields);
    }
}
