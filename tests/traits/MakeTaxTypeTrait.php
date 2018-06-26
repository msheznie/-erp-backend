<?php

use Faker\Factory as Faker;
use App\Models\TaxType;
use App\Repositories\TaxTypeRepository;

trait MakeTaxTypeTrait
{
    /**
     * Create fake instance of TaxType and save it in database
     *
     * @param array $taxTypeFields
     * @return TaxType
     */
    public function makeTaxType($taxTypeFields = [])
    {
        /** @var TaxTypeRepository $taxTypeRepo */
        $taxTypeRepo = App::make(TaxTypeRepository::class);
        $theme = $this->fakeTaxTypeData($taxTypeFields);
        return $taxTypeRepo->create($theme);
    }

    /**
     * Get fake instance of TaxType
     *
     * @param array $taxTypeFields
     * @return TaxType
     */
    public function fakeTaxType($taxTypeFields = [])
    {
        return new TaxType($this->fakeTaxTypeData($taxTypeFields));
    }

    /**
     * Get fake data of TaxType
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTaxTypeData($taxTypeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'typeDescription' => $fake->word
        ], $taxTypeFields);
    }
}
