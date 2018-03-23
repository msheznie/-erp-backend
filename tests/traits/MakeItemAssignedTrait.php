<?php

use Faker\Factory as Faker;
use App\Models\ItemAssigned;
use App\Repositories\ItemAssignedRepository;

trait MakeItemAssignedTrait
{
    /**
     * Create fake instance of ItemAssigned and save it in database
     *
     * @param array $itemAssignedFields
     * @return ItemAssigned
     */
    public function makeItemAssigned($itemAssignedFields = [])
    {
        /** @var ItemAssignedRepository $itemAssignedRepo */
        $itemAssignedRepo = App::make(ItemAssignedRepository::class);
        $theme = $this->fakeItemAssignedData($itemAssignedFields);
        return $itemAssignedRepo->create($theme);
    }

    /**
     * Get fake instance of ItemAssigned
     *
     * @param array $itemAssignedFields
     * @return ItemAssigned
     */
    public function fakeItemAssigned($itemAssignedFields = [])
    {
        return new ItemAssigned($this->fakeItemAssignedData($itemAssignedFields));
    }

    /**
     * Get fake data of ItemAssigned
     *
     * @param array $postFields
     * @return array
     */
    public function fakeItemAssignedData($itemAssignedFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'itemCodeSystem' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'secondaryItemCode' => $fake->word,
            'barcode' => $fake->word,
            'itemDescription' => $fake->text,
            'itemUnitOfMeasure' => $fake->randomDigitNotNull,
            'itemUrl' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'maximunQty' => $fake->randomDigitNotNull,
            'minimumQty' => $fake->randomDigitNotNull,
            'rolQuantity' => $fake->randomDigitNotNull,
            'wacValueLocalCurrencyID' => $fake->randomDigitNotNull,
            'wacValueLocal' => $fake->randomDigitNotNull,
            'wacValueReportingCurrencyID' => $fake->randomDigitNotNull,
            'wacValueReporting' => $fake->randomDigitNotNull,
            'totalQty' => $fake->randomDigitNotNull,
            'totalValueLocal' => $fake->randomDigitNotNull,
            'totalValueRpt' => $fake->randomDigitNotNull,
            'financeCategoryMaster' => $fake->randomDigitNotNull,
            'financeCategorySub' => $fake->randomDigitNotNull,
            'categorySub1' => $fake->randomDigitNotNull,
            'categorySub2' => $fake->randomDigitNotNull,
            'categorySub3' => $fake->randomDigitNotNull,
            'categorySub4' => $fake->randomDigitNotNull,
            'categorySub5' => $fake->randomDigitNotNull,
            'isActive' => $fake->randomDigitNotNull,
            'isAssigned' => $fake->randomDigitNotNull,
            'selectedForWarehouse' => $fake->randomDigitNotNull,
            'itemMovementCategory' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $itemAssignedFields);
    }
}
