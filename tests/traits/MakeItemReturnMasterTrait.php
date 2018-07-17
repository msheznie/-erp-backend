<?php

use Faker\Factory as Faker;
use App\Models\ItemReturnMaster;
use App\Repositories\ItemReturnMasterRepository;

trait MakeItemReturnMasterTrait
{
    /**
     * Create fake instance of ItemReturnMaster and save it in database
     *
     * @param array $itemReturnMasterFields
     * @return ItemReturnMaster
     */
    public function makeItemReturnMaster($itemReturnMasterFields = [])
    {
        /** @var ItemReturnMasterRepository $itemReturnMasterRepo */
        $itemReturnMasterRepo = App::make(ItemReturnMasterRepository::class);
        $theme = $this->fakeItemReturnMasterData($itemReturnMasterFields);
        return $itemReturnMasterRepo->create($theme);
    }

    /**
     * Get fake instance of ItemReturnMaster
     *
     * @param array $itemReturnMasterFields
     * @return ItemReturnMaster
     */
    public function fakeItemReturnMaster($itemReturnMasterFields = [])
    {
        return new ItemReturnMaster($this->fakeItemReturnMasterData($itemReturnMasterFields));
    }

    /**
     * Get fake data of ItemReturnMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeItemReturnMasterData($itemReturnMasterFields = [])
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
            'serialNo' => $fake->randomDigitNotNull,
            'itemReturnCode' => $fake->word,
            'ReturnType' => $fake->randomDigitNotNull,
            'ReturnDate' => $fake->date('Y-m-d H:i:s'),
            'ReturnedBy' => $fake->word,
            'jobNo' => $fake->randomDigitNotNull,
            'customerID' => $fake->word,
            'wareHouseLocation' => $fake->randomDigitNotNull,
            'ReturnRefNo' => $fake->word,
            'comment' => $fake->word,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'postedDate' => $fake->date('Y-m-d H:i:s'),
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserGroup' => $fake->word,
            'createdPCid' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $itemReturnMasterFields);
    }
}
