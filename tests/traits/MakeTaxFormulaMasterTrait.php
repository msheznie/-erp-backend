<?php

use Faker\Factory as Faker;
use App\Models\TaxFormulaMaster;
use App\Repositories\TaxFormulaMasterRepository;

trait MakeTaxFormulaMasterTrait
{
    /**
     * Create fake instance of TaxFormulaMaster and save it in database
     *
     * @param array $taxFormulaMasterFields
     * @return TaxFormulaMaster
     */
    public function makeTaxFormulaMaster($taxFormulaMasterFields = [])
    {
        /** @var TaxFormulaMasterRepository $taxFormulaMasterRepo */
        $taxFormulaMasterRepo = App::make(TaxFormulaMasterRepository::class);
        $theme = $this->fakeTaxFormulaMasterData($taxFormulaMasterFields);
        return $taxFormulaMasterRepo->create($theme);
    }

    /**
     * Get fake instance of TaxFormulaMaster
     *
     * @param array $taxFormulaMasterFields
     * @return TaxFormulaMaster
     */
    public function fakeTaxFormulaMaster($taxFormulaMasterFields = [])
    {
        return new TaxFormulaMaster($this->fakeTaxFormulaMasterData($taxFormulaMasterFields));
    }

    /**
     * Get fake data of TaxFormulaMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTaxFormulaMasterData($taxFormulaMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'Description' => $fake->word,
            'taxType' => $fake->randomDigitNotNull,
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
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $taxFormulaMasterFields);
    }
}
