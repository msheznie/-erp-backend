<?php

use Faker\Factory as Faker;
use App\Models\DepreciationMasterReferredHistory;
use App\Repositories\DepreciationMasterReferredHistoryRepository;

trait MakeDepreciationMasterReferredHistoryTrait
{
    /**
     * Create fake instance of DepreciationMasterReferredHistory and save it in database
     *
     * @param array $depreciationMasterReferredHistoryFields
     * @return DepreciationMasterReferredHistory
     */
    public function makeDepreciationMasterReferredHistory($depreciationMasterReferredHistoryFields = [])
    {
        /** @var DepreciationMasterReferredHistoryRepository $depreciationMasterReferredHistoryRepo */
        $depreciationMasterReferredHistoryRepo = App::make(DepreciationMasterReferredHistoryRepository::class);
        $theme = $this->fakeDepreciationMasterReferredHistoryData($depreciationMasterReferredHistoryFields);
        return $depreciationMasterReferredHistoryRepo->create($theme);
    }

    /**
     * Get fake instance of DepreciationMasterReferredHistory
     *
     * @param array $depreciationMasterReferredHistoryFields
     * @return DepreciationMasterReferredHistory
     */
    public function fakeDepreciationMasterReferredHistory($depreciationMasterReferredHistoryFields = [])
    {
        return new DepreciationMasterReferredHistory($this->fakeDepreciationMasterReferredHistoryData($depreciationMasterReferredHistoryFields));
    }

    /**
     * Get fake data of DepreciationMasterReferredHistory
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDepreciationMasterReferredHistoryData($depreciationMasterReferredHistoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'depMasterAutoID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'FYBiggin' => $fake->date('Y-m-d H:i:s'),
            'FYEnd' => $fake->date('Y-m-d H:i:s'),
            'companyFinancePeriodID' => $fake->randomDigitNotNull,
            'FYPeriodDateFrom' => $fake->date('Y-m-d H:i:s'),
            'FYPeriodDateTo' => $fake->date('Y-m-d H:i:s'),
            'depCode' => $fake->word,
            'depDate' => $fake->date('Y-m-d H:i:s'),
            'depMonthYear' => $fake->word,
            'depLocalCur' => $fake->randomDigitNotNull,
            'depAmountLocal' => $fake->randomDigitNotNull,
            'depRptCur' => $fake->randomDigitNotNull,
            'depAmountRpt' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->word,
            'RollLevForApp_curr' => $fake->word,
            'isDepProcessingYN' => $fake->word,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByEmpName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedByUserID' => $fake->word,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdPCID' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $depreciationMasterReferredHistoryFields);
    }
}
