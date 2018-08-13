<?php

use Faker\Factory as Faker;
use App\Models\ItemClientReferenceNumberMaster;
use App\Repositories\ItemClientReferenceNumberMasterRepository;

trait MakeItemClientReferenceNumberMasterTrait
{
    /**
     * Create fake instance of ItemClientReferenceNumberMaster and save it in database
     *
     * @param array $itemClientReferenceNumberMasterFields
     * @return ItemClientReferenceNumberMaster
     */
    public function makeItemClientReferenceNumberMaster($itemClientReferenceNumberMasterFields = [])
    {
        /** @var ItemClientReferenceNumberMasterRepository $itemClientReferenceNumberMasterRepo */
        $itemClientReferenceNumberMasterRepo = App::make(ItemClientReferenceNumberMasterRepository::class);
        $theme = $this->fakeItemClientReferenceNumberMasterData($itemClientReferenceNumberMasterFields);
        return $itemClientReferenceNumberMasterRepo->create($theme);
    }

    /**
     * Get fake instance of ItemClientReferenceNumberMaster
     *
     * @param array $itemClientReferenceNumberMasterFields
     * @return ItemClientReferenceNumberMaster
     */
    public function fakeItemClientReferenceNumberMaster($itemClientReferenceNumberMasterFields = [])
    {
        return new ItemClientReferenceNumberMaster($this->fakeItemClientReferenceNumberMasterData($itemClientReferenceNumberMasterFields));
    }

    /**
     * Get fake data of ItemClientReferenceNumberMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeItemClientReferenceNumberMasterData($itemClientReferenceNumberMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'idItemAssigned' => $fake->randomDigitNotNull,
            'itemSystemCode' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->text,
            'unitOfMeasure' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'customerID' => $fake->randomDigitNotNull,
            'contractUIID' => $fake->randomDigitNotNull,
            'contractID' => $fake->word,
            'clientReferenceNumber' => $fake->word,
            'createdByUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedByUserID' => $fake->word,
            'modifiedDateTime' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $itemClientReferenceNumberMasterFields);
    }
}
