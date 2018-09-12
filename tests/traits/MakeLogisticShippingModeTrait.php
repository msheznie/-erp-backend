<?php

use Faker\Factory as Faker;
use App\Models\LogisticShippingMode;
use App\Repositories\LogisticShippingModeRepository;

trait MakeLogisticShippingModeTrait
{
    /**
     * Create fake instance of LogisticShippingMode and save it in database
     *
     * @param array $logisticShippingModeFields
     * @return LogisticShippingMode
     */
    public function makeLogisticShippingMode($logisticShippingModeFields = [])
    {
        /** @var LogisticShippingModeRepository $logisticShippingModeRepo */
        $logisticShippingModeRepo = App::make(LogisticShippingModeRepository::class);
        $theme = $this->fakeLogisticShippingModeData($logisticShippingModeFields);
        return $logisticShippingModeRepo->create($theme);
    }

    /**
     * Get fake instance of LogisticShippingMode
     *
     * @param array $logisticShippingModeFields
     * @return LogisticShippingMode
     */
    public function fakeLogisticShippingMode($logisticShippingModeFields = [])
    {
        return new LogisticShippingMode($this->fakeLogisticShippingModeData($logisticShippingModeFields));
    }

    /**
     * Get fake data of LogisticShippingMode
     *
     * @param array $postFields
     * @return array
     */
    public function fakeLogisticShippingModeData($logisticShippingModeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'modeShippingDescription' => $fake->word,
            'createdUserID' => $fake->word,
            'createdPCID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->word
        ], $logisticShippingModeFields);
    }
}
