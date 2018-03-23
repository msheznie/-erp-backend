<?php

use Faker\Factory as Faker;
use App\Models\FinanceItemcategorySubAssigned;
use App\Repositories\FinanceItemcategorySubAssignedRepository;

trait MakeFinanceItemcategorySubAssignedTrait
{
    /**
     * Create fake instance of FinanceItemcategorySubAssigned and save it in database
     *
     * @param array $financeItemcategorySubAssignedFields
     * @return FinanceItemcategorySubAssigned
     */
    public function makeFinanceItemcategorySubAssigned($financeItemcategorySubAssignedFields = [])
    {
        /** @var FinanceItemcategorySubAssignedRepository $financeItemcategorySubAssignedRepo */
        $financeItemcategorySubAssignedRepo = App::make(FinanceItemcategorySubAssignedRepository::class);
        $theme = $this->fakeFinanceItemcategorySubAssignedData($financeItemcategorySubAssignedFields);
        return $financeItemcategorySubAssignedRepo->create($theme);
    }

    /**
     * Get fake instance of FinanceItemcategorySubAssigned
     *
     * @param array $financeItemcategorySubAssignedFields
     * @return FinanceItemcategorySubAssigned
     */
    public function fakeFinanceItemcategorySubAssigned($financeItemcategorySubAssignedFields = [])
    {
        return new FinanceItemcategorySubAssigned($this->fakeFinanceItemcategorySubAssignedData($financeItemcategorySubAssignedFields));
    }

    /**
     * Get fake data of FinanceItemcategorySubAssigned
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFinanceItemcategorySubAssignedData($financeItemcategorySubAssignedFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'mainItemCategoryID' => $fake->randomDigitNotNull,
            'itemCategorySubID' => $fake->randomDigitNotNull,
            'categoryDescription' => $fake->word,
            'financeGLcodebBSSystemID' => $fake->randomDigitNotNull,
            'financeGLcodebBS' => $fake->word,
            'financeGLcodePLSystemID' => $fake->randomDigitNotNull,
            'financeGLcodePL' => $fake->word,
            'includePLForGRVYN' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'isActive' => $fake->randomDigitNotNull,
            'isAssigned' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $financeItemcategorySubAssignedFields);
    }
}
