<?php

use Faker\Factory as Faker;
use App\Models\SalesPersonMaster;
use App\Repositories\SalesPersonMasterRepository;

trait MakeSalesPersonMasterTrait
{
    /**
     * Create fake instance of SalesPersonMaster and save it in database
     *
     * @param array $salesPersonMasterFields
     * @return SalesPersonMaster
     */
    public function makeSalesPersonMaster($salesPersonMasterFields = [])
    {
        /** @var SalesPersonMasterRepository $salesPersonMasterRepo */
        $salesPersonMasterRepo = App::make(SalesPersonMasterRepository::class);
        $theme = $this->fakeSalesPersonMasterData($salesPersonMasterFields);
        return $salesPersonMasterRepo->create($theme);
    }

    /**
     * Get fake instance of SalesPersonMaster
     *
     * @param array $salesPersonMasterFields
     * @return SalesPersonMaster
     */
    public function fakeSalesPersonMaster($salesPersonMasterFields = [])
    {
        return new SalesPersonMaster($this->fakeSalesPersonMasterData($salesPersonMasterFields));
    }

    /**
     * Get fake data of SalesPersonMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSalesPersonMasterData($salesPersonMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'empSystemID' => $fake->randomDigitNotNull,
            'SalesPersonCode' => $fake->word,
            'SalesPersonName' => $fake->word,
            'salesPersonImage' => $fake->word,
            'wareHouseAutoID' => $fake->randomDigitNotNull,
            'wareHouseCode' => $fake->word,
            'wareHouseDescription' => $fake->word,
            'wareHouseLocation' => $fake->word,
            'SalesPersonEmail' => $fake->word,
            'SecondaryCode' => $fake->word,
            'contactNumber' => $fake->word,
            'salesPersonTargetType' => $fake->randomDigitNotNull,
            'salesPersonTarget' => $fake->randomDigitNotNull,
            'SalesPersonAddress' => $fake->word,
            'receivableAutoID' => $fake->randomDigitNotNull,
            'receivableSystemGLCode' => $fake->word,
            'receivableGLAccount' => $fake->word,
            'receivableDescription' => $fake->word,
            'receivableType' => $fake->word,
            'expenseAutoID' => $fake->randomDigitNotNull,
            'expenseSystemGLCode' => $fake->word,
            'expenseGLAccount' => $fake->word,
            'expenseDescription' => $fake->word,
            'expenseType' => $fake->word,
            'salesPersonCurrencyID' => $fake->randomDigitNotNull,
            'salesPersonCurrency' => $fake->word,
            'salesPersonCurrencyDecimalPlaces' => $fake->randomDigitNotNull,
            'segmentID' => $fake->randomDigitNotNull,
            'segmentCode' => $fake->word,
            'isActive' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'createdUserGroup' => $fake->randomDigitNotNull,
            'createdPCID' => $fake->word,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserName' => $fake->word,
            'modifiedPCID' => $fake->word,
            'modifiedUserID' => $fake->word,
            'modifiedDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedUserName' => $fake->word,
            'TIMESTAMP' => $fake->date('Y-m-d H:i:s')
        ], $salesPersonMasterFields);
    }
}
