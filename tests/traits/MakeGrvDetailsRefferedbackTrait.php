<?php

use Faker\Factory as Faker;
use App\Models\GrvDetailsRefferedback;
use App\Repositories\GrvDetailsRefferedbackRepository;

trait MakeGrvDetailsRefferedbackTrait
{
    /**
     * Create fake instance of GrvDetailsRefferedback and save it in database
     *
     * @param array $grvDetailsRefferedbackFields
     * @return GrvDetailsRefferedback
     */
    public function makeGrvDetailsRefferedback($grvDetailsRefferedbackFields = [])
    {
        /** @var GrvDetailsRefferedbackRepository $grvDetailsRefferedbackRepo */
        $grvDetailsRefferedbackRepo = App::make(GrvDetailsRefferedbackRepository::class);
        $theme = $this->fakeGrvDetailsRefferedbackData($grvDetailsRefferedbackFields);
        return $grvDetailsRefferedbackRepo->create($theme);
    }

    /**
     * Get fake instance of GrvDetailsRefferedback
     *
     * @param array $grvDetailsRefferedbackFields
     * @return GrvDetailsRefferedback
     */
    public function fakeGrvDetailsRefferedback($grvDetailsRefferedbackFields = [])
    {
        return new GrvDetailsRefferedback($this->fakeGrvDetailsRefferedbackData($grvDetailsRefferedbackFields));
    }

    /**
     * Get fake data of GrvDetailsRefferedback
     *
     * @param array $postFields
     * @return array
     */
    public function fakeGrvDetailsRefferedbackData($grvDetailsRefferedbackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'grvDetailsID' => $fake->randomDigitNotNull,
            'grvAutoID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineCode' => $fake->word,
            'purchaseOrderMastertID' => $fake->randomDigitNotNull,
            'purchaseOrderDetailsID' => $fake->randomDigitNotNull,
            'itemCode' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->text,
            'itemFinanceCategoryID' => $fake->randomDigitNotNull,
            'itemFinanceCategorySubID' => $fake->randomDigitNotNull,
            'financeGLcodebBSSystemID' => $fake->randomDigitNotNull,
            'financeGLcodebBS' => $fake->word,
            'financeGLcodePLSystemID' => $fake->randomDigitNotNull,
            'financeGLcodePL' => $fake->word,
            'includePLForGRVYN' => $fake->randomDigitNotNull,
            'supplierPartNumber' => $fake->word,
            'unitOfMeasure' => $fake->randomDigitNotNull,
            'noQty' => $fake->randomDigitNotNull,
            'prvRecievedQty' => $fake->randomDigitNotNull,
            'poQty' => $fake->randomDigitNotNull,
            'unitCost' => $fake->randomDigitNotNull,
            'discountPercentage' => $fake->randomDigitNotNull,
            'discountAmount' => $fake->randomDigitNotNull,
            'netAmount' => $fake->randomDigitNotNull,
            'comment' => $fake->text,
            'supplierDefaultCurrencyID' => $fake->randomDigitNotNull,
            'supplierDefaultER' => $fake->randomDigitNotNull,
            'supplierItemCurrencyID' => $fake->randomDigitNotNull,
            'foreignToLocalER' => $fake->randomDigitNotNull,
            'companyReportingCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingER' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'addonDistCost' => $fake->randomDigitNotNull,
            'GRVcostPerUnitLocalCur' => $fake->randomDigitNotNull,
            'GRVcostPerUnitSupDefaultCur' => $fake->randomDigitNotNull,
            'GRVcostPerUnitSupTransCur' => $fake->randomDigitNotNull,
            'GRVcostPerUnitComRptCur' => $fake->randomDigitNotNull,
            'landingCost_TransCur' => $fake->randomDigitNotNull,
            'landingCost_LocalCur' => $fake->randomDigitNotNull,
            'landingCost_RptCur' => $fake->randomDigitNotNull,
            'logisticsCharges_TransCur' => $fake->randomDigitNotNull,
            'logisticsCharges_LocalCur' => $fake->randomDigitNotNull,
            'logisticsChargest_RptCur' => $fake->randomDigitNotNull,
            'assetAllocationDoneYN' => $fake->randomDigitNotNull,
            'isContract' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'totalWHTAmount' => $fake->randomDigitNotNull,
            'WHTBearedBySupplier' => $fake->randomDigitNotNull,
            'WHTBearedByCompany' => $fake->randomDigitNotNull,
            'extraComment' => $fake->text,
            'vatRegisteredYN' => $fake->randomDigitNotNull,
            'supplierVATEligible' => $fake->randomDigitNotNull,
            'VATPercentage' => $fake->randomDigitNotNull,
            'VATAmount' => $fake->randomDigitNotNull,
            'VATAmountLocal' => $fake->randomDigitNotNull,
            'VATAmountRpt' => $fake->randomDigitNotNull,
            'logisticsAvailable' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $grvDetailsRefferedbackFields);
    }
}
