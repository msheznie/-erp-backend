<?php

use Faker\Factory as Faker;
use App\Models\LogisticModeOfImport;
use App\Repositories\LogisticModeOfImportRepository;

trait MakeLogisticModeOfImportTrait
{
    /**
     * Create fake instance of LogisticModeOfImport and save it in database
     *
     * @param array $logisticModeOfImportFields
     * @return LogisticModeOfImport
     */
    public function makeLogisticModeOfImport($logisticModeOfImportFields = [])
    {
        /** @var LogisticModeOfImportRepository $logisticModeOfImportRepo */
        $logisticModeOfImportRepo = App::make(LogisticModeOfImportRepository::class);
        $theme = $this->fakeLogisticModeOfImportData($logisticModeOfImportFields);
        return $logisticModeOfImportRepo->create($theme);
    }

    /**
     * Get fake instance of LogisticModeOfImport
     *
     * @param array $logisticModeOfImportFields
     * @return LogisticModeOfImport
     */
    public function fakeLogisticModeOfImport($logisticModeOfImportFields = [])
    {
        return new LogisticModeOfImport($this->fakeLogisticModeOfImportData($logisticModeOfImportFields));
    }

    /**
     * Get fake data of LogisticModeOfImport
     *
     * @param array $postFields
     * @return array
     */
    public function fakeLogisticModeOfImportData($logisticModeOfImportFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'modeImportDescription' => $fake->word,
            'createdUserID' => $fake->word,
            'createdPCID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $logisticModeOfImportFields);
    }
}
