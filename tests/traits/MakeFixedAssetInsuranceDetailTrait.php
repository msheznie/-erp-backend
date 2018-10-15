<?php

use Faker\Factory as Faker;
use App\Models\FixedAssetInsuranceDetail;
use App\Repositories\FixedAssetInsuranceDetailRepository;

trait MakeFixedAssetInsuranceDetailTrait
{
    /**
     * Create fake instance of FixedAssetInsuranceDetail and save it in database
     *
     * @param array $fixedAssetInsuranceDetailFields
     * @return FixedAssetInsuranceDetail
     */
    public function makeFixedAssetInsuranceDetail($fixedAssetInsuranceDetailFields = [])
    {
        /** @var FixedAssetInsuranceDetailRepository $fixedAssetInsuranceDetailRepo */
        $fixedAssetInsuranceDetailRepo = App::make(FixedAssetInsuranceDetailRepository::class);
        $theme = $this->fakeFixedAssetInsuranceDetailData($fixedAssetInsuranceDetailFields);
        return $fixedAssetInsuranceDetailRepo->create($theme);
    }

    /**
     * Get fake instance of FixedAssetInsuranceDetail
     *
     * @param array $fixedAssetInsuranceDetailFields
     * @return FixedAssetInsuranceDetail
     */
    public function fakeFixedAssetInsuranceDetail($fixedAssetInsuranceDetailFields = [])
    {
        return new FixedAssetInsuranceDetail($this->fakeFixedAssetInsuranceDetailData($fixedAssetInsuranceDetailFields));
    }

    /**
     * Get fake data of FixedAssetInsuranceDetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFixedAssetInsuranceDetailData($fixedAssetInsuranceDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companyID' => $fake->word,
            'faID' => $fake->randomDigitNotNull,
            'insuredYN' => $fake->randomDigitNotNull,
            'policy' => $fake->randomDigitNotNull,
            'policyNumber' => $fake->word,
            'dateOfInsurance' => $fake->date('Y-m-d H:i:s'),
            'dateOfExpiry' => $fake->date('Y-m-d H:i:s'),
            'insuredValue' => $fake->randomDigitNotNull,
            'insurerName' => $fake->word,
            'locationID' => $fake->randomDigitNotNull,
            'buildingNumber' => $fake->word,
            'openClosedArea' => $fake->randomDigitNotNull,
            'containerNumber' => $fake->word,
            'movingItem' => $fake->randomDigitNotNull,
            'createdByUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $fixedAssetInsuranceDetailFields);
    }
}
