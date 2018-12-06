<?php

use Faker\Factory as Faker;
use App\Models\ItemReturnMasterRefferedBack;
use App\Repositories\ItemReturnMasterRefferedBackRepository;

trait MakeItemReturnMasterRefferedBackTrait
{
    /**
     * Create fake instance of ItemReturnMasterRefferedBack and save it in database
     *
     * @param array $itemReturnMasterRefferedBackFields
     * @return ItemReturnMasterRefferedBack
     */
    public function makeItemReturnMasterRefferedBack($itemReturnMasterRefferedBackFields = [])
    {
        /** @var ItemReturnMasterRefferedBackRepository $itemReturnMasterRefferedBackRepo */
        $itemReturnMasterRefferedBackRepo = App::make(ItemReturnMasterRefferedBackRepository::class);
        $theme = $this->fakeItemReturnMasterRefferedBackData($itemReturnMasterRefferedBackFields);
        return $itemReturnMasterRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of ItemReturnMasterRefferedBack
     *
     * @param array $itemReturnMasterRefferedBackFields
     * @return ItemReturnMasterRefferedBack
     */
    public function fakeItemReturnMasterRefferedBack($itemReturnMasterRefferedBackFields = [])
    {
        return new ItemReturnMasterRefferedBack($this->fakeItemReturnMasterRefferedBackData($itemReturnMasterRefferedBackFields));
    }

    /**
     * Get fake data of ItemReturnMasterRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeItemReturnMasterRefferedBackData($itemReturnMasterRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'itemReturnAutoID' => $fake->randomDigitNotNull,
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
            'approvedByUserID' => $fake->word,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'postedDate' => $fake->date('Y-m-d H:i:s'),
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->randomDigitNotNull,
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
        ], $itemReturnMasterRefferedBackFields);
    }
}
