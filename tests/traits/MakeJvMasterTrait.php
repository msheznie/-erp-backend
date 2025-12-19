<?php

use Faker\Factory as Faker;
use App\Models\JvMaster;
use App\Repositories\JvMasterRepository;

trait MakeJvMasterTrait
{
    /**
     * Create fake instance of JvMaster and save it in database
     *
     * @param array $jvMasterFields
     * @return JvMaster
     */
    public function makeJvMaster($jvMasterFields = [])
    {
        /** @var JvMasterRepository $jvMasterRepo */
        $jvMasterRepo = App::make(JvMasterRepository::class);
        $theme = $this->fakeJvMasterData($jvMasterFields);
        return $jvMasterRepo->create($theme);
    }

    /**
     * Get fake instance of JvMaster
     *
     * @param array $jvMasterFields
     * @return JvMaster
     */
    public function fakeJvMaster($jvMasterFields = [])
    {
        return new JvMaster($this->fakeJvMasterData($jvMasterFields));
    }

    /**
     * Get fake data of JvMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeJvMasterData($jvMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
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
            'postedDate' => $fake->date('Y-m-d H:i:s'),
            'jvType' => $fake->randomDigitNotNull,
            'isReverseAccYN' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
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
        ], $jvMasterFields);
    }
}
