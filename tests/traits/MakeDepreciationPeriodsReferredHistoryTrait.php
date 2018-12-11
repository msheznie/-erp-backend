<?php

use Faker\Factory as Faker;
use App\Models\DepreciationPeriodsReferredHistory;
use App\Repositories\DepreciationPeriodsReferredHistoryRepository;

trait MakeDepreciationPeriodsReferredHistoryTrait
{
    /**
     * Create fake instance of DepreciationPeriodsReferredHistory and save it in database
     *
     * @param array $depreciationPeriodsReferredHistoryFields
     * @return DepreciationPeriodsReferredHistory
     */
    public function makeDepreciationPeriodsReferredHistory($depreciationPeriodsReferredHistoryFields = [])
    {
        /** @var DepreciationPeriodsReferredHistoryRepository $depreciationPeriodsReferredHistoryRepo */
        $depreciationPeriodsReferredHistoryRepo = App::make(DepreciationPeriodsReferredHistoryRepository::class);
        $theme = $this->fakeDepreciationPeriodsReferredHistoryData($depreciationPeriodsReferredHistoryFields);
        return $depreciationPeriodsReferredHistoryRepo->create($theme);
    }

    /**
     * Get fake instance of DepreciationPeriodsReferredHistory
     *
     * @param array $depreciationPeriodsReferredHistoryFields
     * @return DepreciationPeriodsReferredHistory
     */
    public function fakeDepreciationPeriodsReferredHistory($depreciationPeriodsReferredHistoryFields = [])
    {
        return new DepreciationPeriodsReferredHistory($this->fakeDepreciationPeriodsReferredHistoryData($depreciationPeriodsReferredHistoryFields));
    }

    /**
     * Get fake data of DepreciationPeriodsReferredHistory
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDepreciationPeriodsReferredHistoryData($depreciationPeriodsReferredHistoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'DepreciationPeriodsID' => $fake->randomDigitNotNull,
            'depMasterAutoID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'faFinanceCatID' => $fake->randomDigitNotNull,
            'faMainCategory' => $fake->randomDigitNotNull,
            'faSubCategory' => $fake->randomDigitNotNull,
            'faID' => $fake->randomDigitNotNull,
            'faCode' => $fake->word,
            'assetDescription' => $fake->text,
            'depMonth' => $fake->randomDigitNotNull,
            'depPercent' => $fake->randomDigitNotNull,
            'COSTUNIT' => $fake->randomDigitNotNull,
            'costUnitRpt' => $fake->randomDigitNotNull,
            'FYID' => $fake->randomDigitNotNull,
            'depForFYStartDate' => $fake->date('Y-m-d H:i:s'),
            'depForFYEndDate' => $fake->date('Y-m-d H:i:s'),
            'FYperiodID' => $fake->randomDigitNotNull,
            'depForFYperiodStartDate' => $fake->date('Y-m-d H:i:s'),
            'depForFYperiodEndDate' => $fake->date('Y-m-d H:i:s'),
            'depMonthYear' => $fake->word,
            'depAmountLocalCurr' => $fake->randomDigitNotNull,
            'depAmountLocal' => $fake->randomDigitNotNull,
            'depAmountRptCurr' => $fake->randomDigitNotNull,
            'depAmountRpt' => $fake->randomDigitNotNull,
            'depDoneYN' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdBy' => $fake->word,
            'createdPCid' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $depreciationPeriodsReferredHistoryFields);
    }
}
