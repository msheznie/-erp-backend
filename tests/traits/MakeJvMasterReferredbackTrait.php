<?php

use Faker\Factory as Faker;
use App\Models\JvMasterReferredback;
use App\Repositories\JvMasterReferredbackRepository;

trait MakeJvMasterReferredbackTrait
{
    /**
     * Create fake instance of JvMasterReferredback and save it in database
     *
     * @param array $jvMasterReferredbackFields
     * @return JvMasterReferredback
     */
    public function makeJvMasterReferredback($jvMasterReferredbackFields = [])
    {
        /** @var JvMasterReferredbackRepository $jvMasterReferredbackRepo */
        $jvMasterReferredbackRepo = App::make(JvMasterReferredbackRepository::class);
        $theme = $this->fakeJvMasterReferredbackData($jvMasterReferredbackFields);
        return $jvMasterReferredbackRepo->create($theme);
    }

    /**
     * Get fake instance of JvMasterReferredback
     *
     * @param array $jvMasterReferredbackFields
     * @return JvMasterReferredback
     */
    public function fakeJvMasterReferredback($jvMasterReferredbackFields = [])
    {
        return new JvMasterReferredback($this->fakeJvMasterReferredbackData($jvMasterReferredbackFields));
    }

    /**
     * Get fake data of JvMasterReferredback
     *
     * @param array $postFields
     * @return array
     */
    public function fakeJvMasterReferredbackData($jvMasterReferredbackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'jvMasterAutoId' => $fake->randomDigitNotNull,
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
            'JVcode' => $fake->word,
            'JVdate' => $fake->date('Y-m-d H:i:s'),
            'recurringjvMasterAutoId' => $fake->randomDigitNotNull,
            'recurringMonth' => $fake->randomDigitNotNull,
            'recurringYear' => $fake->randomDigitNotNull,
            'JVNarration' => $fake->text,
            'currencyID' => $fake->randomDigitNotNull,
            'currencyER' => $fake->randomDigitNotNull,
            'rptCurrencyID' => $fake->randomDigitNotNull,
            'rptCurrencyER' => $fake->randomDigitNotNull,
            'empID' => $fake->word,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedByUserID' => $fake->word,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'postedDate' => $fake->date('Y-m-d H:i:s'),
            'jvType' => $fake->randomDigitNotNull,
            'isReverseAccYN' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'isRelatedPartyYN' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $jvMasterReferredbackFields);
    }
}
