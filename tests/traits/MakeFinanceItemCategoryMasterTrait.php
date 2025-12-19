<?php

use Faker\Factory as Faker;
use App\Models\FinanceItemCategoryMaster;
use App\Repositories\FinanceItemCategoryMasterRepository;

trait MakeFinanceItemCategoryMasterTrait
{
    /**
     * Create fake instance of FinanceItemCategoryMaster and save it in database
     *
     * @param array $financeItemCategoryMasterFields
     * @return FinanceItemCategoryMaster
     */
    public function makeFinanceItemCategoryMaster($financeItemCategoryMasterFields = [])
    {
        /** @var FinanceItemCategoryMasterRepository $financeItemCategoryMasterRepo */
        $financeItemCategoryMasterRepo = App::make(FinanceItemCategoryMasterRepository::class);
        $theme = $this->fakeFinanceItemCategoryMasterData($financeItemCategoryMasterFields);
        return $financeItemCategoryMasterRepo->create($theme);
    }

    /**
     * Get fake instance of FinanceItemCategoryMaster
     *
     * @param array $financeItemCategoryMasterFields
     * @return FinanceItemCategoryMaster
     */
    public function fakeFinanceItemCategoryMaster($financeItemCategoryMasterFields = [])
    {
        return new FinanceItemCategoryMaster($this->fakeFinanceItemCategoryMasterData($financeItemCategoryMasterFields));
    }

    /**
     * Get fake data of FinanceItemCategoryMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFinanceItemCategoryMasterData($financeItemCategoryMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'categoryDescription' => $fake->word,
            'itemCodeDef' => $fake->word,
            'numberOfDigits' => $fake->randomDigitNotNull,
            'lastSerialOrder' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s'),
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s')
        ], $financeItemCategoryMasterFields);
    }
}
