<?php

use Faker\Factory as Faker;
use App\Models\ItemIssueMasterRefferedBack;
use App\Repositories\ItemIssueMasterRefferedBackRepository;

trait MakeItemIssueMasterRefferedBackTrait
{
    /**
     * Create fake instance of ItemIssueMasterRefferedBack and save it in database
     *
     * @param array $itemIssueMasterRefferedBackFields
     * @return ItemIssueMasterRefferedBack
     */
    public function makeItemIssueMasterRefferedBack($itemIssueMasterRefferedBackFields = [])
    {
        /** @var ItemIssueMasterRefferedBackRepository $itemIssueMasterRefferedBackRepo */
        $itemIssueMasterRefferedBackRepo = App::make(ItemIssueMasterRefferedBackRepository::class);
        $theme = $this->fakeItemIssueMasterRefferedBackData($itemIssueMasterRefferedBackFields);
        return $itemIssueMasterRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of ItemIssueMasterRefferedBack
     *
     * @param array $itemIssueMasterRefferedBackFields
     * @return ItemIssueMasterRefferedBack
     */
    public function fakeItemIssueMasterRefferedBack($itemIssueMasterRefferedBackFields = [])
    {
        return new ItemIssueMasterRefferedBack($this->fakeItemIssueMasterRefferedBackData($itemIssueMasterRefferedBackFields));
    }

    /**
     * Get fake data of ItemIssueMasterRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeItemIssueMasterRefferedBackData($itemIssueMasterRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'itemIssueAutoID' => $fake->randomDigitNotNull,
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
            'itemIssueCode' => $fake->word,
            'issueType' => $fake->randomDigitNotNull,
            'issueDate' => $fake->date('Y-m-d H:i:s'),
            'wareHouseFrom' => $fake->randomDigitNotNull,
            'wareHouseFromCode' => $fake->word,
            'wareHouseFromDes' => $fake->word,
            'contractUIID' => $fake->randomDigitNotNull,
            'contractID' => $fake->word,
            'jobNo' => $fake->randomDigitNotNull,
            'workOrderNo' => $fake->word,
            'purchaseOrderNo' => $fake->word,
            'networkNo' => $fake->word,
            'itemDeliveredOnSiteDate' => $fake->date('Y-m-d H:i:s'),
            'customerSystemID' => $fake->randomDigitNotNull,
            'customerID' => $fake->word,
            'issueRefNo' => $fake->word,
            'reqDocID' => $fake->randomDigitNotNull,
            'reqByID' => $fake->word,
            'reqByName' => $fake->word,
            'reqDate' => $fake->date('Y-m-d H:i:s'),
            'reqComment' => $fake->word,
            'wellLocationFieldID' => $fake->randomDigitNotNull,
            'fieldShortCode' => $fake->word,
            'fieldName' => $fake->word,
            'wellNO' => $fake->word,
            'comment' => $fake->text,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedByUserID' => $fake->word,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'directReqByID' => $fake->word,
            'directReqByName' => $fake->word,
            'product' => $fake->word,
            'volume' => $fake->word,
            'strength' => $fake->word,
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
            'contRefNo' => $fake->word,
            'is_closed' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $itemIssueMasterRefferedBackFields);
    }
}
