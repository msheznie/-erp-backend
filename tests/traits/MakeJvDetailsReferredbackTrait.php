<?php

use Faker\Factory as Faker;
use App\Models\JvDetailsReferredback;
use App\Repositories\JvDetailsReferredbackRepository;

trait MakeJvDetailsReferredbackTrait
{
    /**
     * Create fake instance of JvDetailsReferredback and save it in database
     *
     * @param array $jvDetailsReferredbackFields
     * @return JvDetailsReferredback
     */
    public function makeJvDetailsReferredback($jvDetailsReferredbackFields = [])
    {
        /** @var JvDetailsReferredbackRepository $jvDetailsReferredbackRepo */
        $jvDetailsReferredbackRepo = App::make(JvDetailsReferredbackRepository::class);
        $theme = $this->fakeJvDetailsReferredbackData($jvDetailsReferredbackFields);
        return $jvDetailsReferredbackRepo->create($theme);
    }

    /**
     * Get fake instance of JvDetailsReferredback
     *
     * @param array $jvDetailsReferredbackFields
     * @return JvDetailsReferredback
     */
    public function fakeJvDetailsReferredback($jvDetailsReferredbackFields = [])
    {
        return new JvDetailsReferredback($this->fakeJvDetailsReferredbackData($jvDetailsReferredbackFields));
    }

    /**
     * Get fake data of JvDetailsReferredback
     *
     * @param array $postFields
     * @return array
     */
    public function fakeJvDetailsReferredbackData($jvDetailsReferredbackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'jvDetailAutoID' => $fake->randomDigitNotNull,
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
        ], $jvDetailsReferredbackFields);
    }
}
