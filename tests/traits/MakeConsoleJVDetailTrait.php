<?php

use Faker\Factory as Faker;
use App\Models\ConsoleJVDetail;
use App\Repositories\ConsoleJVDetailRepository;

trait MakeConsoleJVDetailTrait
{
    /**
     * Create fake instance of ConsoleJVDetail and save it in database
     *
     * @param array $consoleJVDetailFields
     * @return ConsoleJVDetail
     */
    public function makeConsoleJVDetail($consoleJVDetailFields = [])
    {
        /** @var ConsoleJVDetailRepository $consoleJVDetailRepo */
        $consoleJVDetailRepo = App::make(ConsoleJVDetailRepository::class);
        $theme = $this->fakeConsoleJVDetailData($consoleJVDetailFields);
        return $consoleJVDetailRepo->create($theme);
    }

    /**
     * Get fake instance of ConsoleJVDetail
     *
     * @param array $consoleJVDetailFields
     * @return ConsoleJVDetail
     */
    public function fakeConsoleJVDetail($consoleJVDetailFields = [])
    {
        return new ConsoleJVDetail($this->fakeConsoleJVDetailData($consoleJVDetailFields));
    }

    /**
     * Get fake data of ConsoleJVDetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeConsoleJVDetailData($consoleJVDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'consoleJvMasterAutoId' => $fake->randomDigitNotNull,
            'jvDetailAutoID' => $fake->randomDigitNotNull,
            'jvMasterAutoId' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'documentCode' => $fake->word,
            'glDate' => $fake->date('Y-m-d H:i:s'),
            'glAccountSystemID' => $fake->randomDigitNotNull,
            'glAccount' => $fake->word,
            'glAccountDescription' => $fake->word,
            'comments' => $fake->text,
            'currencyID' => $fake->randomDigitNotNull,
            'currencyER' => $fake->randomDigitNotNull,
            'debitAmount' => $fake->randomDigitNotNull,
            'creditAmount' => $fake->randomDigitNotNull,
            'localDebitAmount' => $fake->randomDigitNotNull,
            'rptDebitAmount' => $fake->randomDigitNotNull,
            'localCreditAmount' => $fake->randomDigitNotNull,
            'rptCreditAmount' => $fake->randomDigitNotNull,
            'consoleType' => $fake->randomDigitNotNull,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $consoleJVDetailFields);
    }
}
