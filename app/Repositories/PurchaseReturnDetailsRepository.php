<?php

namespace App\Repositories;

use App\Models\PurchaseReturnDetails;
use App\Models\GRVDetails;
use App\Models\PoAdvancePayment;
use App\Models\PurchaseReturnLogistic;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PurchaseReturnDetailsRepository
 * @package App\Repositories
 * @version July 31, 2018, 6:20 am UTC
 *
 * @method PurchaseReturnDetails findWithoutFail($id, $columns = ['*'])
 * @method PurchaseReturnDetails find($id, $columns = ['*'])
 * @method PurchaseReturnDetails first($columns = ['*'])
*/
class PurchaseReturnDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'purhaseReturnAutoID',
        'companyID',
        'grvAutoID',
        'grvDetailsID',
        'itemCode',
        'itemPrimaryCode',
        'itemDescription',
        'supplierPartNumber',
        'unitOfMeasure',
        'GRVQty',
        'comment',
        'noQty',
        'supplierDefaultCurrencyID',
        'supplierDefaultER',
        'supplierTransactionCurrencyID',
        'supplierTransactionER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'GRVcostPerUnitLocalCur',
        'GRVcostPerUnitSupDefaultCur',
        'GRVcostPerUnitSupTransCur',
        'GRVcostPerUnitComRptCur',
        'netAmount',
        'netAmountLocal',
        'netAmountRpt',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PurchaseReturnDetails::class;
    }

    public function savePrnLogistics($purhasereturnDetailID)
    {
        $purchaseReturnDetailData = PurchaseReturnDetails::with(['grv_detail_master'])->find($purhasereturnDetailID);

        if (!$purchaseReturnDetailData) {
            return ['status' => false, 'message' => "Purchase Return Detail not found"];
        }

        //getting transaction amount
        $grvTotalSupplierTransactionCurrency = GRVDetails::selectRaw('COALESCE(SUM(GRVcostPerUnitSupTransCur * noQty),0) as transactionTotalSum, COALESCE(SUM(GRVcostPerUnitComRptCur * noQty),0) as reportingTotalSum, COALESCE(SUM(GRVcostPerUnitLocalCur * noQty),0) as localTotalSum, COALESCE(SUM(GRVcostPerUnitSupDefaultCur * noQty),0) as defaultTotalSum')
                                                        ->where('grvAutoID', $purchaseReturnDetailData->grvAutoID)
                                                        ->first();


        //getting logistic amount
        $grvTotalLogisticAmount = PoAdvancePayment::where('grvAutoID', $purchaseReturnDetailData->grvAutoID)
                                                  ->get();


        $deleteRes = PurchaseReturnLogistic::where('purchaseReturnDetailID', $purchaseReturnDetailData->purhasereturnDetailID)->delete();


        foreach ($grvTotalLogisticAmount as $key => $value) {
            $logisticTrans = $this->calculateLogisticAmount($value->reqAmountInPOTransCur, ($purchaseReturnDetailData->GRVQty * $purchaseReturnDetailData->GRVcostPerUnitSupTransCur), $grvTotalSupplierTransactionCurrency->transactionTotalSum, $purchaseReturnDetailData->GRVQty, $purchaseReturnDetailData->noQty);

            $logisticRpt = $this->calculateLogisticAmount($value->reqAmountInPORptCur, ($purchaseReturnDetailData->GRVQty * $purchaseReturnDetailData->GRVcostPerUnitComRptCur), $grvTotalSupplierTransactionCurrency->reportingTotalSum, $purchaseReturnDetailData->GRVQty, $purchaseReturnDetailData->noQty);

            $logisticLocal = $this->calculateLogisticAmount($value->reqAmountInPOLocalCur, ($purchaseReturnDetailData->GRVQty * $purchaseReturnDetailData->GRVcostPerUnitLocalCur), $grvTotalSupplierTransactionCurrency->localTotalSum, $purchaseReturnDetailData->GRVQty, $purchaseReturnDetailData->noQty);

            $logisticTransVAT = $this->calculateLogisticAmount($value->VATAmount, ($purchaseReturnDetailData->GRVQty * $purchaseReturnDetailData->GRVcostPerUnitSupTransCur), $grvTotalSupplierTransactionCurrency->transactionTotalSum, $purchaseReturnDetailData->GRVQty, $purchaseReturnDetailData->noQty);

            $logisticRptVAT = $this->calculateLogisticAmount($value->VATAmountRpt, ($purchaseReturnDetailData->GRVQty * $purchaseReturnDetailData->GRVcostPerUnitComRptCur), $grvTotalSupplierTransactionCurrency->reportingTotalSum, $purchaseReturnDetailData->GRVQty, $purchaseReturnDetailData->noQty);

            $logisticLocalVAT = $this->calculateLogisticAmount($value->VATAmountLocal, ($purchaseReturnDetailData->GRVQty * $purchaseReturnDetailData->GRVcostPerUnitLocalCur), $grvTotalSupplierTransactionCurrency->localTotalSum, $purchaseReturnDetailData->GRVQty, $purchaseReturnDetailData->noQty);


            $prnLogistics = [
                'poAdvPaymentID' => $value->poAdvPaymentID,
                'grvAutoID' => $purchaseReturnDetailData->grvAutoID,
                'grvDetailID' => $purchaseReturnDetailData->grvDetailsID,
                'purchaseReturnID' => $purchaseReturnDetailData->purhaseReturnAutoID,
                'purchaseReturnDetailID' => $purchaseReturnDetailData->purhasereturnDetailID,
                'logisticAmountTrans' => $logisticTrans,
                'logisticAmountRpt' => $logisticRpt,
                'logisticAmountLocal' => $logisticLocal,
                'logisticVATAmount' => $logisticTransVAT,
                'logisticVATAmountLocal' => $logisticLocalVAT,
                'logisticVATAmountRpt' => $logisticRptVAT,
                'UnbilledGRVAccountSystemID' => $value->UnbilledGRVAccountSystemID,
                'supplierID' => $value->supplierID,
                'vatSubCategoryID' => $value->vatSubCategoryID,
                'supplierTransactionCurrencyID' => $value->currencyID
            ];

            $res = PurchaseReturnLogistic::create($prnLogistics);

        }

        return ['status' => true];
    }

    public function calculateLogisticAmount($logistic, $lineGrvCost, $totalGrvCost, $grvQty, $returnQty)
    {
        return (($logistic * ($lineGrvCost/$totalGrvCost))/$grvQty) * $returnQty;
    }
}
