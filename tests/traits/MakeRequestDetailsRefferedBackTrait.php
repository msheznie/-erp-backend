<?php

use Faker\Factory as Faker;
use App\Models\RequestDetailsRefferedBack;
use App\Repositories\RequestDetailsRefferedBackRepository;

trait MakeRequestDetailsRefferedBackTrait
{
    /**
     * Create fake instance of RequestDetailsRefferedBack and save it in database
     *
     * @param array $requestDetailsRefferedBackFields
     * @return RequestDetailsRefferedBack
     */
    public function makeRequestDetailsRefferedBack($requestDetailsRefferedBackFields = [])
    {
        /** @var RequestDetailsRefferedBackRepository $requestDetailsRefferedBackRepo */
        $requestDetailsRefferedBackRepo = App::make(RequestDetailsRefferedBackRepository::class);
        $theme = $this->fakeRequestDetailsRefferedBackData($requestDetailsRefferedBackFields);
        return $requestDetailsRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of RequestDetailsRefferedBack
     *
     * @param array $requestDetailsRefferedBackFields
     * @return RequestDetailsRefferedBack
     */
    public function fakeRequestDetailsRefferedBack($requestDetailsRefferedBackFields = [])
    {
        return new RequestDetailsRefferedBack($this->fakeRequestDetailsRefferedBackData($requestDetailsRefferedBackFields));
    }

    /**
     * Get fake data of RequestDetailsRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeRequestDetailsRefferedBackData($requestDetailsRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'RequestDetailsID' => $fake->randomDigitNotNull,
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
            'timesReferred' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $requestDetailsRefferedBackFields);
    }
}
