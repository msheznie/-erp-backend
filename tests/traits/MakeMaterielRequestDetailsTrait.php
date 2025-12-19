<?php

use Faker\Factory as Faker;
use App\Models\MaterielRequestDetails;
use App\Repositories\MaterielRequestDetailsRepository;

trait MakeMaterielRequestDetailsTrait
{
    /**
     * Create fake instance of MaterielRequestDetails and save it in database
     *
     * @param array $materielRequestDetailsFields
     * @return MaterielRequestDetails
     */
    public function makeMaterielRequestDetails($materielRequestDetailsFields = [])
    {
        /** @var MaterielRequestDetailsRepository $materielRequestDetailsRepo */
        $materielRequestDetailsRepo = App::make(MaterielRequestDetailsRepository::class);
        $theme = $this->fakeMaterielRequestDetailsData($materielRequestDetailsFields);
        return $materielRequestDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of MaterielRequestDetails
     *
     * @param array $materielRequestDetailsFields
     * @return MaterielRequestDetails
     */
    public function fakeMaterielRequestDetails($materielRequestDetailsFields = [])
    {
        return new MaterielRequestDetails($this->fakeMaterielRequestDetailsData($materielRequestDetailsFields));
    }

    /**
     * Get fake data of MaterielRequestDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeMaterielRequestDetailsData($materielRequestDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'RequestID' => $fake->randomDigitNotNull,
            'itemCode' => $fake->randomDigitNotNull,
            'itemDescription' => $fake->word,
            'itemFinanceCategoryID' => $fake->randomDigitNotNull,
            'itemFinanceCategorySubID' => $fake->randomDigitNotNull,
            'financeGLcodebBS' => $fake->word,
            'financeGLcodePL' => $fake->word,
            'includePLForGRVYN' => $fake->randomDigitNotNull,
            'partNumber' => $fake->word,
            'unitOfMeasure' => $fake->randomDigitNotNull,
            'unitOfMeasureIssued' => $fake->randomDigitNotNull,
            'quantityRequested' => $fake->randomDigitNotNull,
            'qtyIssuedDefaultMeasure' => $fake->randomDigitNotNull,
            'convertionMeasureVal' => $fake->randomDigitNotNull,
            'comments' => $fake->word,
            'quantityOnOrder' => $fake->randomDigitNotNull,
            'quantityInHand' => $fake->randomDigitNotNull,
            'estimatedCost' => $fake->randomDigitNotNull,
            'minQty' => $fake->randomDigitNotNull,
            'maxQty' => $fake->randomDigitNotNull,
            'selectedForIssue' => $fake->randomDigitNotNull,
            'ClosedYN' => $fake->randomDigitNotNull,
            'allowCreatePR' => $fake->randomDigitNotNull,
            'selectedToCreatePR' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $materielRequestDetailsFields);
    }
}
