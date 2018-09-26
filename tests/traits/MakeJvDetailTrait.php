<?php

use Faker\Factory as Faker;
use App\Models\JvDetail;
use App\Repositories\JvDetailRepository;

trait MakeJvDetailTrait
{
    /**
     * Create fake instance of JvDetail and save it in database
     *
     * @param array $jvDetailFields
     * @return JvDetail
     */
    public function makeJvDetail($jvDetailFields = [])
    {
        /** @var JvDetailRepository $jvDetailRepo */
        $jvDetailRepo = App::make(JvDetailRepository::class);
        $theme = $this->fakeJvDetailData($jvDetailFields);
        return $jvDetailRepo->create($theme);
    }

    /**
     * Get fake instance of JvDetail
     *
     * @param array $jvDetailFields
     * @return JvDetail
     */
    public function fakeJvDetail($jvDetailFields = [])
    {
        return new JvDetail($this->fakeJvDetailData($jvDetailFields));
    }

    /**
     * Get fake data of JvDetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeJvDetailData($jvDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'jvMasterAutoId' => $fake->randomDigitNotNull,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'recurringjvMasterAutoId' => $fake->randomDigitNotNull,
            'recurringjvDetailAutoID' => $fake->randomDigitNotNull,
            'recurringMonth' => $fake->randomDigitNotNull,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'chartOfAccountSystemID' => $fake->randomDigitNotNull,
            'glAccount' => $fake->word,
            'glAccountDescription' => $fake->text,
            'referenceGLCode' => $fake->word,
            'referenceGLDescription' => $fake->text,
            'comments' => $fake->text,
            'clientContractID' => $fake->word,
            'currencyID' => $fake->randomDigitNotNull,
            'currencyER' => $fake->randomDigitNotNull,
            'debitAmount' => $fake->randomDigitNotNull,
            'creditAmount' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'companyIDForConsole' => $fake->word,
            'selectedForConsole' => $fake->randomDigitNotNull,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $jvDetailFields);
    }
}
