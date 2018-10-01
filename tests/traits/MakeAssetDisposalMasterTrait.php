<?php

use Faker\Factory as Faker;
use App\Models\AssetDisposalMaster;
use App\Repositories\AssetDisposalMasterRepository;

trait MakeAssetDisposalMasterTrait
{
    /**
     * Create fake instance of AssetDisposalMaster and save it in database
     *
     * @param array $assetDisposalMasterFields
     * @return AssetDisposalMaster
     */
    public function makeAssetDisposalMaster($assetDisposalMasterFields = [])
    {
        /** @var AssetDisposalMasterRepository $assetDisposalMasterRepo */
        $assetDisposalMasterRepo = App::make(AssetDisposalMasterRepository::class);
        $theme = $this->fakeAssetDisposalMasterData($assetDisposalMasterFields);
        return $assetDisposalMasterRepo->create($theme);
    }

    /**
     * Get fake instance of AssetDisposalMaster
     *
     * @param array $assetDisposalMasterFields
     * @return AssetDisposalMaster
     */
    public function fakeAssetDisposalMaster($assetDisposalMasterFields = [])
    {
        return new AssetDisposalMaster($this->fakeAssetDisposalMasterData($assetDisposalMasterFields));
    }

    /**
     * Get fake data of AssetDisposalMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAssetDisposalMasterData($assetDisposalMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'toCompanySystemID' => $fake->randomDigitNotNull,
            'toCompanyID' => $fake->word,
            'customerID' => $fake->randomDigitNotNull,
            'serialNo' => $fake->randomDigitNotNull,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'FYBiggin' => $fake->date('Y-m-d H:i:s'),
            'FYEnd' => $fake->date('Y-m-d H:i:s'),
            'FYPeriodDateFrom' => $fake->date('Y-m-d H:i:s'),
            'FYPeriodDateTo' => $fake->date('Y-m-d H:i:s'),
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'disposalDocumentCode' => $fake->word,
            'disposalDocumentDate' => $fake->date('Y-m-d H:i:s'),
            'narration' => $fake->text,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confimedByEmpSystemID' => $fake->randomDigitNotNull,
            'confimedByEmpID' => $fake->word,
            'confirmedByEmpName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'disposalType' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $assetDisposalMasterFields);
    }
}
