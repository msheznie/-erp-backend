<?php

use Faker\Factory as Faker;
use App\Models\FinanceItemCategorySub;
use App\Repositories\FinanceItemCategorySubRepository;

trait MakeFinanceItemCategorySubTrait
{
    /**
     * Create fake instance of FinanceItemCategorySub and save it in database
     *
     * @param array $financeItemCategorySubFields
     * @return FinanceItemCategorySub
     */
    public function makeFinanceItemCategorySub($financeItemCategorySubFields = [])
    {
        /** @var FinanceItemCategorySubRepository $financeItemCategorySubRepo */
        $financeItemCategorySubRepo = App::make(FinanceItemCategorySubRepository::class);
        $theme = $this->fakeFinanceItemCategorySubData($financeItemCategorySubFields);
        return $financeItemCategorySubRepo->create($theme);
    }

    /**
     * Get fake instance of FinanceItemCategorySub
     *
     * @param array $financeItemCategorySubFields
     * @return FinanceItemCategorySub
     */
    public function fakeFinanceItemCategorySub($financeItemCategorySubFields = [])
    {
        return new FinanceItemCategorySub($this->fakeFinanceItemCategorySubData($financeItemCategorySubFields));
    }

    /**
     * Get fake data of FinanceItemCategorySub
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFinanceItemCategorySubData($financeItemCategorySubFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'categoryDescription' => $fake->word,
            'itemCategoryID' => $fake->randomDigitNotNull,
            'financeGLcodebBSSystemID' => $fake->randomDigitNotNull,
            'financeGLcodebBS' => $fake->word,
            'financeGLcodePLSystemID' => $fake->randomDigitNotNull,
            'financeGLcodePL' => $fake->word,
            'includePLForGRVYN' => $fake->randomDigitNotNull,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $financeItemCategorySubFields);
    }
}
