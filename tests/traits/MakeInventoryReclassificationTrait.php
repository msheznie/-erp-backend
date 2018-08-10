<?php

use Faker\Factory as Faker;
use App\Models\InventoryReclassification;
use App\Repositories\InventoryReclassificationRepository;

trait MakeInventoryReclassificationTrait
{
    /**
     * Create fake instance of InventoryReclassification and save it in database
     *
     * @param array $inventoryReclassificationFields
     * @return InventoryReclassification
     */
    public function makeInventoryReclassification($inventoryReclassificationFields = [])
    {
        /** @var InventoryReclassificationRepository $inventoryReclassificationRepo */
        $inventoryReclassificationRepo = App::make(InventoryReclassificationRepository::class);
        $theme = $this->fakeInventoryReclassificationData($inventoryReclassificationFields);
        return $inventoryReclassificationRepo->create($theme);
    }

    /**
     * Get fake instance of InventoryReclassification
     *
     * @param array $inventoryReclassificationFields
     * @return InventoryReclassification
     */
    public function fakeInventoryReclassification($inventoryReclassificationFields = [])
    {
        return new InventoryReclassification($this->fakeInventoryReclassificationData($inventoryReclassificationFields));
    }

    /**
     * Get fake data of InventoryReclassification
     *
     * @param array $postFields
     * @return array
     */
    public function fakeInventoryReclassificationData($inventoryReclassificationFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'companyFinancePeriodID' => $fake->randomDigitNotNull,
            'FYBiggin' => $fake->date('Y-m-d H:i:s'),
            'FYEnd' => $fake->date('Y-m-d H:i:s'),
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'inventoryReclassificationDate' => $fake->date('Y-m-d H:i:s'),
            'narration' => $fake->text,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedByUserID' => $fake->word,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'postedDate' => $fake->date('Y-m-d H:i:s'),
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'rejectedYN' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserGroup' => $fake->word,
            'createdPCid' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $inventoryReclassificationFields);
    }
}
