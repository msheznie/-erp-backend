<?php

use Faker\Factory as Faker;
use App\Models\GposPaymentGlConfigDetail;
use App\Repositories\GposPaymentGlConfigDetailRepository;

trait MakeGposPaymentGlConfigDetailTrait
{
    /**
     * Create fake instance of GposPaymentGlConfigDetail and save it in database
     *
     * @param array $gposPaymentGlConfigDetailFields
     * @return GposPaymentGlConfigDetail
     */
    public function makeGposPaymentGlConfigDetail($gposPaymentGlConfigDetailFields = [])
    {
        /** @var GposPaymentGlConfigDetailRepository $gposPaymentGlConfigDetailRepo */
        $gposPaymentGlConfigDetailRepo = App::make(GposPaymentGlConfigDetailRepository::class);
        $theme = $this->fakeGposPaymentGlConfigDetailData($gposPaymentGlConfigDetailFields);
        return $gposPaymentGlConfigDetailRepo->create($theme);
    }

    /**
     * Get fake instance of GposPaymentGlConfigDetail
     *
     * @param array $gposPaymentGlConfigDetailFields
     * @return GposPaymentGlConfigDetail
     */
    public function fakeGposPaymentGlConfigDetail($gposPaymentGlConfigDetailFields = [])
    {
        return new GposPaymentGlConfigDetail($this->fakeGposPaymentGlConfigDetailData($gposPaymentGlConfigDetailFields));
    }

    /**
     * Get fake data of GposPaymentGlConfigDetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeGposPaymentGlConfigDetailData($gposPaymentGlConfigDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'paymentConfigMasterID' => $fake->randomDigitNotNull,
            'GLCode' => $fake->randomDigitNotNull,
            'companyID' => $fake->randomDigitNotNull,
            'companyCode' => $fake->word,
            'warehouseID' => $fake->randomDigitNotNull,
            'isAuthRequired' => $fake->word,
            'createdUserGroup' => $fake->randomDigitNotNull,
            'createdPCID' => $fake->word,
            'createdUserID' => $fake->word,
            'createdUserName' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedPCID' => $fake->word,
            'modifiedUserID' => $fake->word,
            'modifiedUserName' => $fake->word,
            'modifiedDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $gposPaymentGlConfigDetailFields);
    }
}
