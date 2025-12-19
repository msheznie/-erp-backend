<?php

use Faker\Factory as Faker;
use App\Models\AccruavalFromOPMaster;
use App\Repositories\AccruavalFromOPMasterRepository;

trait MakeAccruavalFromOPMasterTrait
{
    /**
     * Create fake instance of AccruavalFromOPMaster and save it in database
     *
     * @param array $accruavalFromOPMasterFields
     * @return AccruavalFromOPMaster
     */
    public function makeAccruavalFromOPMaster($accruavalFromOPMasterFields = [])
    {
        /** @var AccruavalFromOPMasterRepository $accruavalFromOPMasterRepo */
        $accruavalFromOPMasterRepo = App::make(AccruavalFromOPMasterRepository::class);
        $theme = $this->fakeAccruavalFromOPMasterData($accruavalFromOPMasterFields);
        return $accruavalFromOPMasterRepo->create($theme);
    }

    /**
     * Get fake instance of AccruavalFromOPMaster
     *
     * @param array $accruavalFromOPMasterFields
     * @return AccruavalFromOPMaster
     */
    public function fakeAccruavalFromOPMaster($accruavalFromOPMasterFields = [])
    {
        return new AccruavalFromOPMaster($this->fakeAccruavalFromOPMasterData($accruavalFromOPMasterFields));
    }

    /**
     * Get fake data of AccruavalFromOPMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAccruavalFromOPMasterData($accruavalFromOPMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'accruvalNarration' => $fake->word,
            'accrualDateAsOF' => $fake->date('Y-m-d H:i:s'),
            'serialNo' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'accmonth' => $fake->randomDigitNotNull,
            'accYear' => $fake->randomDigitNotNull,
            'accConfirmedYN' => $fake->randomDigitNotNull,
            'accConfirmedBy' => $fake->word,
            'accConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'jvMasterAutoID' => $fake->randomDigitNotNull,
            'accJVpostedYN' => $fake->randomDigitNotNull,
            'jvPostedBy' => $fake->word,
            'jvPostedDate' => $fake->date('Y-m-d H:i:s'),
            'createdby' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $accruavalFromOPMasterFields);
    }
}
