<?php

use Faker\Factory as Faker;
use App\Models\ItemMasterRefferedBack;
use App\Repositories\ItemMasterRefferedBackRepository;

trait MakeItemMasterRefferedBackTrait
{
    /**
     * Create fake instance of ItemMasterRefferedBack and save it in database
     *
     * @param array $itemMasterRefferedBackFields
     * @return ItemMasterRefferedBack
     */
    public function makeItemMasterRefferedBack($itemMasterRefferedBackFields = [])
    {
        /** @var ItemMasterRefferedBackRepository $itemMasterRefferedBackRepo */
        $itemMasterRefferedBackRepo = App::make(ItemMasterRefferedBackRepository::class);
        $theme = $this->fakeItemMasterRefferedBackData($itemMasterRefferedBackFields);
        return $itemMasterRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of ItemMasterRefferedBack
     *
     * @param array $itemMasterRefferedBackFields
     * @return ItemMasterRefferedBack
     */
    public function fakeItemMasterRefferedBack($itemMasterRefferedBackFields = [])
    {
        return new ItemMasterRefferedBack($this->fakeItemMasterRefferedBackData($itemMasterRefferedBackFields));
    }

    /**
     * Get fake data of ItemMasterRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeItemMasterRefferedBackData($itemMasterRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'itemCodeSystem' => $fake->randomDigitNotNull,
            'primaryItemCode' => $fake->word,
            'runningSerialOrder' => $fake->randomDigitNotNull,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'primaryCompanySystemID' => $fake->randomDigitNotNull,
            'primaryCompanyID' => $fake->word,
            'primaryCode' => $fake->word,
            'secondaryItemCode' => $fake->word,
            'barcode' => $fake->word,
            'itemDescription' => $fake->text,
            'itemShortDescription' => $fake->text,
            'itemUrl' => $fake->text,
            'unit' => $fake->randomDigitNotNull,
            'financeCategoryMaster' => $fake->randomDigitNotNull,
            'financeCategorySub' => $fake->randomDigitNotNull,
            'itemPicture' => $fake->word,
            'selectedForAssign' => $fake->randomDigitNotNull,
            'isActive' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'sentConfirmationEmail' => $fake->randomDigitNotNull,
            'confirmationEmailSentByEmpID' => $fake->word,
            'confirmationEmailSentByEmpName' => $fake->word,
            'itemConfirmedYN' => $fake->randomDigitNotNull,
            'itemConfirmedByEMPSystemID' => $fake->randomDigitNotNull,
            'itemConfirmedByEMPID' => $fake->word,
            'itemConfirmedByEMPName' => $fake->word,
            'itemConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'itemApprovedBySystemID' => $fake->randomDigitNotNull,
            'itemApprovedBy' => $fake->word,
            'itemApprovedYN' => $fake->randomDigitNotNull,
            'itemApprovedDate' => $fake->date('Y-m-d H:i:s'),
            'itemApprovedComment' => $fake->text,
            'timesReferred' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s'),
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUserSystemID' => $fake->randomDigitNotNull
        ], $itemMasterRefferedBackFields);
    }
}
