<?php

use Faker\Factory as Faker;
use App\Models\MonthlyAdditionsMaster;
use App\Repositories\MonthlyAdditionsMasterRepository;

trait MakeMonthlyAdditionsMasterTrait
{
    /**
     * Create fake instance of MonthlyAdditionsMaster and save it in database
     *
     * @param array $monthlyAdditionsMasterFields
     * @return MonthlyAdditionsMaster
     */
    public function makeMonthlyAdditionsMaster($monthlyAdditionsMasterFields = [])
    {
        /** @var MonthlyAdditionsMasterRepository $monthlyAdditionsMasterRepo */
        $monthlyAdditionsMasterRepo = App::make(MonthlyAdditionsMasterRepository::class);
        $theme = $this->fakeMonthlyAdditionsMasterData($monthlyAdditionsMasterFields);
        return $monthlyAdditionsMasterRepo->create($theme);
    }

    /**
     * Get fake instance of MonthlyAdditionsMaster
     *
     * @param array $monthlyAdditionsMasterFields
     * @return MonthlyAdditionsMaster
     */
    public function fakeMonthlyAdditionsMaster($monthlyAdditionsMasterFields = [])
    {
        return new MonthlyAdditionsMaster($this->fakeMonthlyAdditionsMasterData($monthlyAdditionsMasterFields));
    }

    /**
     * Get fake data of MonthlyAdditionsMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeMonthlyAdditionsMasterData($monthlyAdditionsMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'monthlyAdditionsCode' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'CompanyID' => $fake->word,
            'description' => $fake->word,
            'currency' => $fake->randomDigitNotNull,
            'processPeriod' => $fake->randomDigitNotNull,
            'dateMA' => $fake->date('Y-m-d H:i:s'),
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedby' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'approvedby' => $fake->word,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyExchangeRate' => $fake->randomDigitNotNull,
            'rptCurrencyID' => $fake->randomDigitNotNull,
            'rptCurrencyExchangeRate' => $fake->randomDigitNotNull,
            'expenseClaimAdditionYN' => $fake->randomDigitNotNull,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifieduser' => $fake->word,
            'modifiedpc' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createduserGroup' => $fake->word,
            'createdpc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $monthlyAdditionsMasterFields);
    }
}
