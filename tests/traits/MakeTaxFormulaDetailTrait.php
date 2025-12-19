<?php

use Faker\Factory as Faker;
use App\Models\TaxFormulaDetail;
use App\Repositories\TaxFormulaDetailRepository;

trait MakeTaxFormulaDetailTrait
{
    /**
     * Create fake instance of TaxFormulaDetail and save it in database
     *
     * @param array $taxFormulaDetailFields
     * @return TaxFormulaDetail
     */
    public function makeTaxFormulaDetail($taxFormulaDetailFields = [])
    {
        /** @var TaxFormulaDetailRepository $taxFormulaDetailRepo */
        $taxFormulaDetailRepo = App::make(TaxFormulaDetailRepository::class);
        $theme = $this->fakeTaxFormulaDetailData($taxFormulaDetailFields);
        return $taxFormulaDetailRepo->create($theme);
    }

    /**
     * Get fake instance of TaxFormulaDetail
     *
     * @param array $taxFormulaDetailFields
     * @return TaxFormulaDetail
     */
    public function fakeTaxFormulaDetail($taxFormulaDetailFields = [])
    {
        return new TaxFormulaDetail($this->fakeTaxFormulaDetailData($taxFormulaDetailFields));
    }

    /**
     * Get fake data of TaxFormulaDetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTaxFormulaDetailData($taxFormulaDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'taxCalculationformulaID' => $fake->randomDigitNotNull,
            'taxMasterAutoID' => $fake->randomDigitNotNull,
            'description' => $fake->word,
            'taxMasters' => $fake->text,
            'sortOrder' => $fake->randomDigitNotNull,
            'formula' => $fake->text,
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
        ], $taxFormulaDetailFields);
    }
}
