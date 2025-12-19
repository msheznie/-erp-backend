<?php

use Faker\Factory as Faker;
use App\Models\AssetDisposalReferred;
use App\Repositories\AssetDisposalReferredRepository;

trait MakeAssetDisposalReferredTrait
{
    /**
     * Create fake instance of AssetDisposalReferred and save it in database
     *
     * @param array $assetDisposalReferredFields
     * @return AssetDisposalReferred
     */
    public function makeAssetDisposalReferred($assetDisposalReferredFields = [])
    {
        /** @var AssetDisposalReferredRepository $assetDisposalReferredRepo */
        $assetDisposalReferredRepo = App::make(AssetDisposalReferredRepository::class);
        $theme = $this->fakeAssetDisposalReferredData($assetDisposalReferredFields);
        return $assetDisposalReferredRepo->create($theme);
    }

    /**
     * Get fake instance of AssetDisposalReferred
     *
     * @param array $assetDisposalReferredFields
     * @return AssetDisposalReferred
     */
    public function fakeAssetDisposalReferred($assetDisposalReferredFields = [])
    {
        return new AssetDisposalReferred($this->fakeAssetDisposalReferredData($assetDisposalReferredFields));
    }

    /**
     * Get fake data of AssetDisposalReferred
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAssetDisposalReferredData($assetDisposalReferredFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'assetdisposalMasterAutoID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'toCompanySystemID' => $fake->randomDigitNotNull,
            'toCompanyID' => $fake->word,
            'customerID' => $fake->randomDigitNotNull,
            'serialNo' => $fake->randomDigitNotNull,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'companyFinancePeriodID' => $fake->randomDigitNotNull,
            'FYBiggin' => $fake->date('Y-m-d H:i:s'),
            'FYEnd' => $fake->date('Y-m-d H:i:s'),
            'FYPeriodDateFrom' => $fake->date('Y-m-d H:i:s'),
            'FYPeriodDateTo' => $fake->date('Y-m-d H:i:s'),
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'disposalDocumentCode' => $fake->word,
            'disposalDocumentDate' => $fake->date('Y-m-d H:i:s'),
            'narration' => $fake->text,
            'revenuePercentage' => $fake->randomDigitNotNull,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confimedByEmpSystemID' => $fake->randomDigitNotNull,
            'confimedByEmpID' => $fake->word,
            'confirmedByEmpName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedByUserID' => $fake->word,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'disposalType' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->word,
            'RollLevForApp_curr' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $assetDisposalReferredFields);
    }
}
