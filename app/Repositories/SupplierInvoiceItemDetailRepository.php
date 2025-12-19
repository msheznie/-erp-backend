<?php

namespace App\Repositories;

use App\Models\SupplierInvoiceItemDetail;
use App\Models\BookInvSuppMaster;
use App\Models\BookInvSuppDet;
use App\Models\UnbilledGrvGroupBy;
use App\Models\PoAdvancePayment;
use App\Models\GRVDetails;
use App\Models\Company;
use App\Models\SupplierAssigned;
use Illuminate\Support\Facades\DB;
use App\helper\TaxService;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierInvoiceItemDetailRepository
 * @package App\Repositories
 * @version October 8, 2021, 4:11 pm +04
 *
 * @method SupplierInvoiceItemDetail findWithoutFail($id, $columns = ['*'])
 * @method SupplierInvoiceItemDetail find($id, $columns = ['*'])
 * @method SupplierInvoiceItemDetail first($columns = ['*'])
*/
class SupplierInvoiceItemDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bookingSupInvoiceDetAutoID',
        'bookingSuppMasInvAutoID',
        'unbilledgrvAutoID',
        'companySystemID',
        'companyID',
        'grvDetailsID',
        'purchaseOrderID',
        'grvAutoID',
        'supplierTransactionCurrencyID',
        'supplierTransactionCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'supplierInvoOrderedAmount',
        'supplierInvoAmount',
        'transSupplierInvoAmount',
        'localSupplierInvoAmount',
        'rptSupplierInvoAmount',
        'totTransactionAmount',
        'totLocalAmount',
        'totRptAmount',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierInvoiceItemDetail::class;
    }

    public function getGRVDetailsForSupplierInvoice($input)
    {
        $bookingSupInvoiceDetAutoID = $input['bookingSupInvoiceDetAutoID'];

        $bookInvSuppDetail = BookInvSuppDet::find($bookingSupInvoiceDetAutoID);

        $groupMaster = UnbilledGrvGroupBy::find($bookInvSuppDetail->unbilledgrvAutoID);

        $bookingSuppMasInvAutoID = $input['bookingSuppMasInvAutoID'];

        $bookInvSuppMaster = BookInvSuppMaster::find($bookingSuppMasInvAutoID);

        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Supplier Invoice not found');
        }

        $company = Company::where('companySystemID', $bookInvSuppMaster->companySystemID)->first();
        $supplierAssignedDetail = SupplierAssigned::where('supplierCodeSytem', $bookInvSuppMaster->supplierID)
                                                    ->where('companySystemID', $bookInvSuppMaster->companySystemID)
                                                    ->first();
        $valEligible = false;
        if ($company->vatRegisteredYN == 1 || $supplierAssignedDetail->vatEligible == 1) {
            $valEligible = true;
        }

        $rcmActivated = TaxService::isGRVRCMActivation($bookInvSuppDetail->grvAutoID);

        if (isset($groupMaster) && $groupMaster->logisticYN) {
            $pulledQry = DB::table('erp_bookinvsupp_item_det')
                                ->selectRaw("SUM(totTransactionAmount) as SumOftotTransactionAmount, logisticID")
                                ->where('erp_bookinvsupp_item_det.bookingSupInvoiceDetAutoID', '!=', $bookingSupInvoiceDetAutoID)
                                ->where(function($query) {
                                    $query->where('logisticID', 0)
                                          ->orWhereNotNull('logisticID');
                                })
                                ->groupBy('logisticID');

            $returnedLogistic = DB::table('purchase_return_logistic')
                                        ->selectRaw("(SUM(logisticAmountTrans) + SUM(logisticVATAmount)) as prLogisticAmount, poAdvPaymentID")
                                        ->groupBy('poAdvPaymentID');

            $grvDetails = PoAdvancePayment::selectRaw("0 as grvDetailsID, erp_purchaseorderadvpayment.poAdvPaymentID as logisticID, itemmaster.primaryCode as itemPrimaryCode, itemmaster.itemDescription as itemDescription, erp_tax_vat_sub_categories.mainCategory as vatMasterCategoryID, erp_purchaseorderadvpayment.vatSubCategoryID, 0 as exempt_vat_portion, ROUND(((reqAmountTransCur_amount)),7) as transactionAmount, ROUND(((reqAmountInPORptCur)),7) as rptAmount, ROUND(((reqAmountInPOLocalCur)),7) as localAmount, ROUND(((reqAmountTransCur_amount) - IFNULL(pulledQry.SumOftotTransactionAmount,0) - IFNULL(returnedLogistic.prLogisticAmount,0)),7) as balanceAmount, ROUND(((reqAmountTransCur_amount) - IFNULL(pulledQry.SumOftotTransactionAmount,0) - IFNULL(returnedLogistic.prLogisticAmount,0)),7) as balanceAmountCheck, erp_bookinvsupp_item_det.supplierInvoAmount, IFNULL(pulledQry.SumOftotTransactionAmount,0) as invoicedAmount,
                                                      erp_purchaseorderadvpayment.VATAmount,
                                                      erp_purchaseorderadvpayment.VATAmountLocal,
                                                      erp_purchaseorderadvpayment.VATAmountRpt")
                                                    ->leftJoin('erp_grvmaster', 'erp_purchaseorderadvpayment.grvAutoID', '=', 'erp_grvmaster.grvAutoID')
                                                    ->leftJoin('erp_tax_vat_sub_categories', 'erp_purchaseorderadvpayment.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                                    ->leftJoin('erp_purchaseordermaster', 'erp_purchaseorderadvpayment.poID', '=', 'erp_purchaseordermaster.purchaseOrderID')
                                                    ->join('erp_addoncostcategories', 'erp_purchaseorderadvpayment.logisticCategoryID', '=', 'erp_addoncostcategories.idaddOnCostCategories')
                                                    ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'erp_addoncostcategories.itemSystemCode')
                                                    ->leftJoin(\DB::raw("({$pulledQry->toSql()}) as pulledQry"), function($join) use ($pulledQry){
                                                        $join->mergeBindings($pulledQry)
                                                             ->on('pulledQry.logisticID', '=', 'erp_purchaseorderadvpayment.poAdvPaymentID');
                                                   })
                                                   ->leftJoin(\DB::raw("({$returnedLogistic->toSql()}) as returnedLogistic"), function($join) use ($returnedLogistic){
                                                        $join->mergeBindings($returnedLogistic)
                                                             ->on('returnedLogistic.poAdvPaymentID', '=', 'erp_purchaseorderadvpayment.poAdvPaymentID');
                                                   })
                                                    ->leftJoin('erp_bookinvsupp_item_det', function($join) use ($bookingSupInvoiceDetAutoID) {
                                                        $join->on('erp_bookinvsupp_item_det.logisticID', '=', 'erp_purchaseorderadvpayment.poAdvPaymentID')
                                                             ->where('erp_bookinvsupp_item_det.bookingSupInvoiceDetAutoID', $bookingSupInvoiceDetAutoID);
                                                   })
                                                    ->where('erp_purchaseorderadvpayment.grvAutoID', $bookInvSuppDetail->grvAutoID)
                                                    ->where('erp_purchaseorderadvpayment.poID', $bookInvSuppDetail->purchaseOrderID)
                                                    ->where('erp_purchaseorderadvpayment.supplierID',$bookInvSuppMaster->supplierID)
                                                    ->groupBy('erp_purchaseorderadvpayment.poAdvPaymentID');          
            $grvDetails = $grvDetails->get();   

            foreach ($grvDetails as $key => $valueGrv) {
                $valueGrv->transactionAmount = $valueGrv->transactionAmount + $valueGrv->VATAmount;
                $valueGrv->rptAmount = $valueGrv->rptAmount + $valueGrv->VATAmountRpt;
                $valueGrv->localAmount = $valueGrv->localAmount + $valueGrv->VATAmountLocal;

                $valueGrv->balanceAmount = $valueGrv->balanceAmount + $valueGrv->VATAmount;
                $valueGrv->balanceAmountCheck = $valueGrv->balanceAmountCheck + $valueGrv->VATAmount;
            }
        } else {

            $pulledQry = DB::table('erp_bookinvsupp_item_det')
                            ->selectRaw("SUM(totTransactionAmount) as SumOftotTransactionAmount, grvDetailsID")
                            ->where('erp_bookinvsupp_item_det.bookingSupInvoiceDetAutoID', '!=', $bookingSupInvoiceDetAutoID)
                            ->groupBy('grvDetailsID');



            $grvDetails = GRVDetails::where('erp_grvdetails.grvAutoID', $bookInvSuppDetail->grvAutoID)
                                   ->where('erp_grvdetails.purchaseOrderMastertID', $bookInvSuppDetail->purchaseOrderID)
                                   ->leftJoin(\DB::raw("({$pulledQry->toSql()}) as pulledQry"), function($join) use ($pulledQry){
                                        $join->mergeBindings($pulledQry)
                                             ->on('pulledQry.grvDetailsID', '=', 'erp_grvdetails.grvDetailsID');
                                   })
                                   ->leftJoin('erp_bookinvsupp_item_det', function($join) use ($bookingSupInvoiceDetAutoID) {
                                        $join->on('erp_bookinvsupp_item_det.grvDetailsID', '=', 'erp_grvdetails.grvDetailsID')
                                             ->where('erp_bookinvsupp_item_det.bookingSupInvoiceDetAutoID', $bookingSupInvoiceDetAutoID);
                                   })
                                   ->with(['supplier_invoice_item_detail' => function($query) use ($bookingSupInvoiceDetAutoID){
                                        $query->where('bookingSupInvoiceDetAutoID', $bookingSupInvoiceDetAutoID)
                                              ->with(['grv' => function($q){
                                                  $q->select(['grvAutoID','grvPrimaryCode']);
                                              },'order' => function($q){
                                                  $q->select(['purchaseOrderID','purchaseOrderCode']);
                                              }]);
                                   }]);

            if ($valEligible && !$rcmActivated) {
                $grvDetails = $grvDetails->selectRaw('erp_bookinvsupp_item_det.supplierInvoAmount, erp_grvdetails.grvDetailsID, itemPrimaryCode, itemDescription, erp_grvdetails.vatMasterCategoryID, erp_grvdetails.vatSubCategoryID, erp_grvdetails.exempt_vat_portion, ROUND(((GRVcostPerUnitSupTransCur*noQty) + (erp_grvdetails.VATAmount*noQty)),7) as transactionAmount, ROUND(((GRVcostPerUnitComRptCur*noQty) + (erp_grvdetails.VATAmountRpt*noQty)),7) as rptAmount, ROUND(((GRVcostPerUnitLocalCur*noQty) + (erp_grvdetails.VATAmountRpt*noQty)),7) as localAmount, ROUND((((GRVcostPerUnitSupTransCur*noQty) + (erp_grvdetails.VATAmount*noQty) - IFNULL(pulledQry.SumOftotTransactionAmount,0)) - ((ROUND(((GRVcostPerUnitSupTransCur*noQty) + (erp_grvdetails.VATAmount*noQty)),7) / noQty) * returnQty)),7) as balanceAmount, ROUND((((GRVcostPerUnitSupTransCur*noQty) + (erp_grvdetails.VATAmount*noQty) - IFNULL(pulledQry.SumOftotTransactionAmount,0)) - ((ROUND(((GRVcostPerUnitSupTransCur*noQty) + (erp_grvdetails.VATAmount*noQty)),7) / noQty) * returnQty)),7) as balanceAmountCheck, IFNULL(pulledQry.SumOftotTransactionAmount,0) as invoicedAmount, noQty, returnQty');
                
                $grvDetails = $grvDetails->get(); 

                foreach ($grvDetails as $key1 => $value1) {
                    $res = TaxService::processGRVDetailVATForUnbilled($value1->grvDetailsID);

                    $value1->transactionAmount = $res['totalTransAmount'];
                    $value1->rptAmount = $res['totalRptAmount'];
                    $value1->localAmount = $res['totalLocalAmount'];

                    $value1->balanceAmount = $value1->transactionAmount - $value1->invoicedAmount - round(($value1->transactionAmount / $value1->noQty) * $value1->returnQty, 7);
                    $value1->balanceAmountCheck = $value1->transactionAmount - $value1->invoicedAmount - round(($value1->transactionAmount / $value1->noQty) * $value1->returnQty, 7);
                }
            } else {
                $grvDetails = $grvDetails->selectRaw('erp_bookinvsupp_item_det.supplierInvoAmount, erp_grvdetails.grvDetailsID, itemPrimaryCode, itemDescription, erp_grvdetails.vatMasterCategoryID, erp_grvdetails.vatSubCategoryID, erp_grvdetails.exempt_vat_portion, ROUND(((GRVcostPerUnitSupTransCur*noQty)),7) as transactionAmount, ROUND(((GRVcostPerUnitComRptCur*noQty)),7) as rptAmount, ROUND(((GRVcostPerUnitLocalCur*noQty)),7) as localAmount, ROUND((((GRVcostPerUnitSupTransCur*noQty) - IFNULL(pulledQry.SumOftotTransactionAmount,0)) - ((ROUND(((GRVcostPerUnitSupTransCur*noQty)),7) / noQty) * returnQty)),7) as balanceAmount, ROUND((((GRVcostPerUnitSupTransCur*noQty) - IFNULL(pulledQry.SumOftotTransactionAmount,0)) - ((ROUND(((GRVcostPerUnitSupTransCur*noQty)),7) / noQty) * returnQty)),7) as balanceAmountCheck, IFNULL(pulledQry.SumOftotTransactionAmount,0) as invoicedAmount, noQty, returnQty');
                $grvDetails = $grvDetails->get(); 

                
                foreach ($grvDetails as $key1 => $value1) {
                    $res = TaxService::processGRVDetailVATForUnbilled($value1->grvDetailsID);

                    $value1->transactionAmount = $value1->transactionAmount - $res['exemptVATTrans'];
                    $value1->rptAmount = $value1->rptAmount - $res['exemptVATRpt'];
                    $value1->localAmount = $value1->localAmount - $res['exemptVATLocal'];

                    $value1->balanceAmount = $value1->transactionAmount - $value1->invoicedAmount - round(($value1->transactionAmount / $value1->noQty) * $value1->returnQty, 7);
                    $value1->balanceAmountCheck = $value1->transactionAmount - $value1->invoicedAmount - round(($value1->transactionAmount / $value1->noQty) * $value1->returnQty, 7);
                }
            }
        }

        foreach ($grvDetails as $key1 => $value1) {

            if(isset($value1->grvDetailsID)){
                $poDetails = GRVDetails::with(['po_detail', 'grv_master'])->where('grvDetailsID', $value1->grvDetailsID)->first();

                if ($poDetails && $poDetails->grv_master && $poDetails->grv_master->grvTypeID == 1) {
                    $value1->vatPercentage = isset($poDetails->VATPercentage) ? $poDetails->VATPercentage: 0;
                } else {
                    $value1->vatPercentage = isset($poDetails->po_detail->VATPercentage) ? $poDetails->po_detail->VATPercentage: 0;
                }
            }

            if(isset( $value1->logisticID)){
                $logisticDetails = PoAdvancePayment::where('poAdvPaymentID', $value1->logisticID)->first();
                $value1->vatPercentageLogistic = isset($logisticDetails->VATPercentage) ? $logisticDetails->VATPercentage: 0;
            }
           ;
        }

        return ['status' => true, 'data' => ['grvDetails' => $grvDetails, 'logisticYN' => $groupMaster->logisticYN]];
    }


    public function updateSupplierInvoiceItemDetail($bookingSuppMasInvAutoID)
    {
        $bookInvSuppDetail = BookInvSuppDet::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                                           ->get();


        foreach ($bookInvSuppDetail as $key => $value) {
            $input = [
                'bookingSuppMasInvAutoID' => $bookingSuppMasInvAutoID,
                'bookingSupInvoiceDetAutoID' => $value->bookingSupInvoiceDetAutoID,
                'companySystemID' => $value->companySystemID
            ];

            $grvDetails = $this->getGRVDetailsForSupplierInvoice($input);

            if ($grvDetails['status']) {
                foreach ($grvDetails['data']['grvDetails'] as $key1 => $value1) {
                    if (isset($value1->supplier_invoice_item_detail) && !is_null($value1->supplier_invoice_item_detail)) {
                        $updateData = [
                            'grvRecivedQty' => $value1->noQty,
                            'grvReturnQty' => $value1->returnQty,
                            'invoicedAmount' => $value1->invoicedAmount,
                            'balanceAmount' => floatval($value1->balanceAmountCheck) - floatval($value1->supplierInvoAmount),
                        ];

                        SupplierInvoiceItemDetail::where('id', $value1->supplier_invoice_item_detail->id)
                                                 ->update($updateData);
                    }   
                }   
            }
        }

        return ['status' => true];
    }
}
