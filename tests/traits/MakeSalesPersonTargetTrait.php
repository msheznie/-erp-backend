<?php

use Faker\Factory as Faker;
use App\Models\SalesPersonTarget;
use App\Repositories\SalesPersonTargetRepository;

trait MakeSalesPersonTargetTrait
{
    /**
     * Create fake instance of SalesPersonTarget and save it in database
     *
     * @param array $salesPersonTargetFields
     * @return SalesPersonTarget
     */
    public function makeSalesPersonTarget($salesPersonTargetFields = [])
    {
        /** @var SalesPersonTargetRepository $salesPersonTargetRepo */
        $salesPersonTargetRepo = App::make(SalesPersonTargetRepository::class);
        $theme = $this->fakeSalesPersonTargetData($salesPersonTargetFields);
        return $salesPersonTargetRepo->create($theme);
    }

    /**
     * Get fake instance of SalesPersonTarget
     *
     * @param array $salesPersonTargetFields
     * @return SalesPersonTarget
     */
    public function fakeSalesPersonTarget($salesPersonTargetFields = [])
    {
        return new SalesPersonTarget($this->fakeSalesPersonTargetData($salesPersonTargetFields));
    }

    /**
     * Get fake data of SalesPersonTarget
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSalesPersonTargetData($salesPersonTargetFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'salesPersonID' => $fake->randomDigitNotNull,
            'datefrom' => $fake->word,
            'dateTo' => $fake->word,
            'currencyID' => $fake->randomDigitNotNull,
            'percentage' => $fake->word,
            'fromTargetAmount' => $fake->randomDigitNotNull,
            'toTargetAmount' => $fake->randomDigitNotNull,
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
        ], $salesPersonTargetFields);
    }
}
