<?php

use Faker\Factory as Faker;
use App\Models\ItemMaster;
use App\Repositories\ItemMasterRepository;

trait MakeItemMasterTrait
{
    /**
     * Create fake instance of ItemMaster and save it in database
     *
     * @param array $itemMasterFields
     * @return ItemMaster
     */
    public function makeItemMaster($itemMasterFields = [])
    {
        /** @var ItemMasterRepository $itemMasterRepo */
        $itemMasterRepo = App::make(ItemMasterRepository::class);
        $theme = $this->fakeItemMasterData($itemMasterFields);
        return $itemMasterRepo->create($theme);
    }

    /**
     * Get fake instance of ItemMaster
     *
     * @param array $itemMasterFields
     * @return ItemMaster
     */
    public function fakeItemMaster($itemMasterFields = [])
    {
        return new ItemMaster($this->fakeItemMasterData($itemMasterFields));
    }

    /**
     * Get fake data of ItemMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeItemMasterData($itemMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
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
            'sentConfirmationEmail' => $fake->randomDigitNotNull,
            'confirmationEmailSentByEmpID' => $fake->word,
            'confirmationEmailSentByEmpName' => $fake->word,
            'itemConfirmedYN' => $fake->randomDigitNotNull,
            'itemConfirmedByEMPID' => $fake->word,
            'itemConfirmedByEMPName' => $fake->word,
            'itemConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'itemApprovedBy' => $fake->word,
            'itemApprovedYN' => $fake->randomDigitNotNull,
            'itemApprovedDate' => $fake->date('Y-m-d H:i:s'),
            'itemApprovedComment' => $fake->text,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $itemMasterFields);
    }
}
