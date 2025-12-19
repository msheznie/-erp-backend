<?php

use Faker\Factory as Faker;
use App\Models\ItemIssueMaster;
use App\Repositories\ItemIssueMasterRepository;

trait MakeItemIssueMasterTrait
{
    /**
     * Create fake instance of ItemIssueMaster and save it in database
     *
     * @param array $itemIssueMasterFields
     * @return ItemIssueMaster
     */
    public function makeItemIssueMaster($itemIssueMasterFields = [])
    {
        /** @var ItemIssueMasterRepository $itemIssueMasterRepo */
        $itemIssueMasterRepo = App::make(ItemIssueMasterRepository::class);
        $theme = $this->fakeItemIssueMasterData($itemIssueMasterFields);
        return $itemIssueMasterRepo->create($theme);
    }

    /**
     * Get fake instance of ItemIssueMaster
     *
     * @param array $itemIssueMasterFields
     * @return ItemIssueMaster
     */
    public function fakeItemIssueMaster($itemIssueMasterFields = [])
    {
        return new ItemIssueMaster($this->fakeItemIssueMasterData($itemIssueMasterFields));
    }

    /**
     * Get fake data of ItemIssueMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeItemIssueMasterData($itemIssueMasterFields = [])
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
            'itemIssueCode' => $fake->word,
            'issueType' => $fake->randomDigitNotNull,
            'issueDate' => $fake->date('Y-m-d H:i:s'),
            'wareHouseFrom' => $fake->randomDigitNotNull,
            'wareHouseFromCode' => $fake->word,
            'wareHouseFromDes' => $fake->word,
            'contractID' => $fake->word,
            'jobNo' => $fake->randomDigitNotNull,
            'workOrderNo' => $fake->word,
            'purchaseOrderNo' => $fake->word,
            'networkNo' => $fake->word,
            'itemDeliveredOnSiteDate' => $fake->date('Y-m-d H:i:s'),
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
            'onfirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'directReqByID' => $fake->word,
            'directReqByName' => $fake->word,
            'product' => $fake->word,
            'volume' => $fake->word,
            'strength' => $fake->word,
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
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $itemIssueMasterFields);
    }
}
