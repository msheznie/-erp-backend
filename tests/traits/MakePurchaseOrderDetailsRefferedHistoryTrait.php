<?php

use Faker\Factory as Faker;
use App\Models\PurchaseOrderDetailsRefferedHistory;
use App\Repositories\PurchaseOrderDetailsRefferedHistoryRepository;

trait MakePurchaseOrderDetailsRefferedHistoryTrait
{
    /**
     * Create fake instance of PurchaseOrderDetailsRefferedHistory and save it in database
     *
     * @param array $purchaseOrderDetailsRefferedHistoryFields
     * @return PurchaseOrderDetailsRefferedHistory
     */
    public function makePurchaseOrderDetailsRefferedHistory($purchaseOrderDetailsRefferedHistoryFields = [])
    {
        /** @var PurchaseOrderDetailsRefferedHistoryRepository $purchaseOrderDetailsRefferedHistoryRepo */
        $purchaseOrderDetailsRefferedHistoryRepo = App::make(PurchaseOrderDetailsRefferedHistoryRepository::class);
        $theme = $this->fakePurchaseOrderDetailsRefferedHistoryData($purchaseOrderDetailsRefferedHistoryFields);
        return $purchaseOrderDetailsRefferedHistoryRepo->create($theme);
    }

    /**
     * Get fake instance of PurchaseOrderDetailsRefferedHistory
     *
     * @param array $purchaseOrderDetailsRefferedHistoryFields
     * @return PurchaseOrderDetailsRefferedHistory
     */
    public function fakePurchaseOrderDetailsRefferedHistory($purchaseOrderDetailsRefferedHistoryFields = [])
    {
        return new PurchaseOrderDetailsRefferedHistory($this->fakePurchaseOrderDetailsRefferedHistoryData($purchaseOrderDetailsRefferedHistoryFields));
    }

    /**
     * Get fake data of PurchaseOrderDetailsRefferedHistory
     *
     * @param array $postFields
     * @return array
     */
    public function fakePurchaseOrderDetailsRefferedHistoryData($purchaseOrderDetailsRefferedHistoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'purchaseOrderDetailsID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'departmentID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'purchaseOrderMasterID' => $fake->randomDigitNotNull,
            'purchaseProcessDetailID' => $fake->randomDigitNotNull,
            'POProcessMasterID' => $fake->randomDigitNotNull,
            'WO_purchaseOrderMasterID' => $fake->randomDigitNotNull,
            'WP_purchaseOrderDetailsID' => $fake->randomDigitNotNull,
            'purchaseRequestDetailsID' => $fake->randomDigitNotNull,
            'purchaseRequestID' => $fake->randomDigitNotNull,
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
            'requestedQty' => $fake->randomDigitNotNull,
            'noQty' => $fake->randomDigitNotNull,
            'balanceQty' => $fake->randomDigitNotNull,
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
            'receivedQty' => $fake->randomDigitNotNull,
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
            'manuallyClosed' => $fake->randomDigitNotNull,
            'manuallyClosedByEmpSystemID' => $fake->randomDigitNotNull,
            'manuallyClosedByEmpID' => $fake->word,
            'manuallyClosedByEmpName' => $fake->word,
            'manuallyClosedDate' => $fake->date('Y-m-d H:i:s'),
            'manuallyClosedComment' => $fake->text,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $purchaseOrderDetailsRefferedHistoryFields);
    }
}
