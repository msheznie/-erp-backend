<?php

use Faker\Factory as Faker;
use App\Models\HRMSJvMaster;
use App\Repositories\HRMSJvMasterRepository;

trait MakeHRMSJvMasterTrait
{
    /**
     * Create fake instance of HRMSJvMaster and save it in database
     *
     * @param array $hRMSJvMasterFields
     * @return HRMSJvMaster
     */
    public function makeHRMSJvMaster($hRMSJvMasterFields = [])
    {
        /** @var HRMSJvMasterRepository $hRMSJvMasterRepo */
        $hRMSJvMasterRepo = App::make(HRMSJvMasterRepository::class);
        $theme = $this->fakeHRMSJvMasterData($hRMSJvMasterFields);
        return $hRMSJvMasterRepo->create($theme);
    }

    /**
     * Get fake instance of HRMSJvMaster
     *
     * @param array $hRMSJvMasterFields
     * @return HRMSJvMaster
     */
    public function fakeHRMSJvMaster($hRMSJvMasterFields = [])
    {
        return new HRMSJvMaster($this->fakeHRMSJvMasterData($hRMSJvMasterFields));
    }

    /**
     * Get fake data of HRMSJvMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeHRMSJvMasterData($hRMSJvMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'salaryProcessMasterID' => $fake->randomDigitNotNull,
            'accruvalNarration' => $fake->word,
            'accrualDateAsOF' => $fake->date('Y-m-d H:i:s'),
            'documentID' => $fake->word,
            'JVCode' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'accmonth' => $fake->randomDigitNotNull,
            'accYear' => $fake->randomDigitNotNull,
            'accConfirmedYN' => $fake->randomDigitNotNull,
            'accConfirmedBy' => $fake->word,
            'accConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'jvMasterAutoID' => $fake->randomDigitNotNull,
            'accJVSelectedYN' => $fake->randomDigitNotNull,
            'accJVpostedYN' => $fake->randomDigitNotNull,
            'jvPostedBy' => $fake->word,
            'jvPostedDate' => $fake->date('Y-m-d H:i:s'),
            'createdby' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $hRMSJvMasterFields);
    }
}
