<?php

use Faker\Factory as Faker;
use App\Models\HRMSJvDetails;
use App\Repositories\HRMSJvDetailsRepository;

trait MakeHRMSJvDetailsTrait
{
    /**
     * Create fake instance of HRMSJvDetails and save it in database
     *
     * @param array $hRMSJvDetailsFields
     * @return HRMSJvDetails
     */
    public function makeHRMSJvDetails($hRMSJvDetailsFields = [])
    {
        /** @var HRMSJvDetailsRepository $hRMSJvDetailsRepo */
        $hRMSJvDetailsRepo = App::make(HRMSJvDetailsRepository::class);
        $theme = $this->fakeHRMSJvDetailsData($hRMSJvDetailsFields);
        return $hRMSJvDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of HRMSJvDetails
     *
     * @param array $hRMSJvDetailsFields
     * @return HRMSJvDetails
     */
    public function fakeHRMSJvDetails($hRMSJvDetailsFields = [])
    {
        return new HRMSJvDetails($this->fakeHRMSJvDetailsData($hRMSJvDetailsFields));
    }

    /**
     * Get fake data of HRMSJvDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeHRMSJvDetailsData($hRMSJvDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'accMasterID' => $fake->randomDigitNotNull,
            'salaryProcessMasterID' => $fake->randomDigitNotNull,
            'accrualNarration' => $fake->word,
            'accrualDateAsOF' => $fake->date('Y-m-d H:i:s'),
            'companyID' => $fake->word,
            'serviceLine' => $fake->word,
            'departureDate' => $fake->date('Y-m-d H:i:s'),
            'callOfDate' => $fake->date('Y-m-d H:i:s'),
            'GlCode' => $fake->word,
            'accrualAmount' => $fake->randomDigitNotNull,
            'accrualCurrency' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'localCurrency' => $fake->randomDigitNotNull,
            'rptAmount' => $fake->randomDigitNotNull,
            'rptCurrency' => $fake->randomDigitNotNull,
            'jvMasterAutoID' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $hRMSJvDetailsFields);
    }
}
