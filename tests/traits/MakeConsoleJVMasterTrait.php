<?php

use Faker\Factory as Faker;
use App\Models\ConsoleJVMaster;
use App\Repositories\ConsoleJVMasterRepository;

trait MakeConsoleJVMasterTrait
{
    /**
     * Create fake instance of ConsoleJVMaster and save it in database
     *
     * @param array $consoleJVMasterFields
     * @return ConsoleJVMaster
     */
    public function makeConsoleJVMaster($consoleJVMasterFields = [])
    {
        /** @var ConsoleJVMasterRepository $consoleJVMasterRepo */
        $consoleJVMasterRepo = App::make(ConsoleJVMasterRepository::class);
        $theme = $this->fakeConsoleJVMasterData($consoleJVMasterFields);
        return $consoleJVMasterRepo->create($theme);
    }

    /**
     * Get fake instance of ConsoleJVMaster
     *
     * @param array $consoleJVMasterFields
     * @return ConsoleJVMaster
     */
    public function fakeConsoleJVMaster($consoleJVMasterFields = [])
    {
        return new ConsoleJVMaster($this->fakeConsoleJVMasterData($consoleJVMasterFields));
    }

    /**
     * Get fake data of ConsoleJVMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeConsoleJVMasterData($consoleJVMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'serialNo' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'consoleJVcode' => $fake->word,
            'consoleJVdate' => $fake->date('Y-m-d H:i:s'),
            'consoleJVNarration' => $fake->word,
            'currencyID' => $fake->randomDigitNotNull,
            'currencyER' => $fake->randomDigitNotNull,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'rptCurrencyID' => $fake->randomDigitNotNull,
            'rptCurrencyER' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $consoleJVMasterFields);
    }
}
