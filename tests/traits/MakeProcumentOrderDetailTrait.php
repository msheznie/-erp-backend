<?php

use Faker\Factory as Faker;
use App\Models\ProcumentOrderDetail;
use App\Repositories\ProcumentOrderDetailRepository;

trait MakeProcumentOrderDetailTrait
{
    /**
     * Create fake instance of ProcumentOrderDetail and save it in database
     *
     * @param array $procumentOrderDetailFields
     * @return ProcumentOrderDetail
     */
    public function makeProcumentOrderDetail($procumentOrderDetailFields = [])
    {
        /** @var ProcumentOrderDetailRepository $procumentOrderDetailRepo */
        $procumentOrderDetailRepo = App::make(ProcumentOrderDetailRepository::class);
        $theme = $this->fakeProcumentOrderDetailData($procumentOrderDetailFields);
        return $procumentOrderDetailRepo->create($theme);
    }

    /**
     * Get fake instance of ProcumentOrderDetail
     *
     * @param array $procumentOrderDetailFields
     * @return ProcumentOrderDetail
     */
    public function fakeProcumentOrderDetail($procumentOrderDetailFields = [])
    {
        return new ProcumentOrderDetail($this->fakeProcumentOrderDetailData($procumentOrderDetailFields));
    }

    /**
     * Get fake data of ProcumentOrderDetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeProcumentOrderDetailData($procumentOrderDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companyID' => $fake->word,
            'departmentID' => $fake->word,
            'serviceLineCode' => $fake->word,
            'purchaseOrderMasterID' => $fake->randomDigitNotNull,
            'POProcessMasterID' => $fake->randomDigitNotNull,
            'WO_purchaseOrderMasterID' => $fake->randomDigitNotNull,
            'WP_purchaseOrderDetailsID' => $fake->randomDigitNotNull,
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
            'itemClientReferenceNumberMasterID' => $fake->randomDigitNotNull,
            'clientReferenceNumber' => $fake->word,
            'noQty' => $fake->randomDigitNotNull,
            'noOfDays' => $fake->randomDigitNotNull,
            'unitCost' => $fake->randomDigitNotNull,
            'discountPercentage' => $fake->randomDigitNotNull,
            'discountAmount' => $fake->randomDigitNotNull,
            'netAmount' => $fake->randomDigitNotNull,
            'budgetYear' => $fake->randomDigitNotNull,
            'prBelongsYear' => $fake->randomDigitNotNull,
            'isAccrued' => $fake->randomDigitNotNull,
            'budjetAmtLocal' => $fake->randomDigitNotNull,
            'budjetAmtRpt' => $fake->randomDigitNotNull,
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
            'addonPurchaseReturnCost' => $fake->randomDigitNotNull,
            'purchaseRetcostPerUnitLocalCur' => $fake->randomDigitNotNull,
            'purchaseRetcostPerUniSupDefaultCur' => $fake->randomDigitNotNull,
            'purchaseRetcostPerUnitTranCur' => $fake->randomDigitNotNull,
            'purchaseRetcostPerUnitRptCur' => $fake->randomDigitNotNull,
            'GRVSelectedYN' => $fake->randomDigitNotNull,
            'goodsRecievedYN' => $fake->randomDigitNotNull,
            'logisticSelectedYN' => $fake->randomDigitNotNull,
            'logisticRecievedYN' => $fake->randomDigitNotNull,
            'isAccruedYN' => $fake->randomDigitNotNull,
            'accrualJVID' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'totalWHTAmount' => $fake->randomDigitNotNull,
            'WHTBearedBySupplier' => $fake->randomDigitNotNull,
            'WHTBearedByCompany' => $fake->randomDigitNotNull,
            'VATPercentage' => $fake->randomDigitNotNull,
            'VATAmount' => $fake->randomDigitNotNull,
            'VATAmountLocal' => $fake->randomDigitNotNull,
            'VATAmountRpt' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $procumentOrderDetailFields);
    }
}
