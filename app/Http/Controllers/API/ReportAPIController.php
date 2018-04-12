<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportAPIController extends AppBaseController
{
    public function generateReport(Request $request)
    {
        $reportID = 'POA';
        switch ($reportID) {
            case 'POA':
                $validatedData = $request->validate([
                    'daterange' => 'required',
                    'suppliers' => 'required',
                ]);

                $startDate = new Carbon($request->daterange['daterange'][0]);
                $endDate = new Carbon($request->daterange['daterange'][1]);
                $companyID = (array)$request->companySystemID;

                $suppliers = (array)$request->suppliers;
                $suppliers = collect($suppliers)->pluck('supplierCodeSytem');

                $output = PurchaseOrderDetails::whereHas('order',function ($query) use ($companyID, $startDate, $endDate){
                    $query->whereBetween('approvedDate', [$startDate, $endDate])
                        ->where('approved', -1)
                        ->whereIN('companySystemID', $companyID);
                })->with(['order' => function ($query) use ($companyID, $startDate, $endDate) {
                    $query->with(['segment' => function ($query) {
                        $query->select('ServiceLineDes', 'serviceLineSystemID');
                    }, 'location' => function ($query) {
                        $query->select('locationName', 'locationID');
                    }]);
                    $query->selectRaw('purchaseOrderID,companyID,poLocation,serviceLineSystemID,approved,companyID,YEAR ( approvedDate ) AS PostingYear,approvedDate AS OrderDate,purchaseOrderCode,IF
	( sentToSupplier = 0, "Not Released", "Released" ) AS STATUS,supplierID,supplierPrimaryCode,supplierName,creditPeriod,deliveryTerms,paymentTerms,expectedDeliveryDate,narration')
                        ->whereBetween('approvedDate', [$startDate, $endDate])
                        ->where('approved', -1)
                        ->whereIN('companySystemID', $companyID);
                }, 'financecategory' => function ($query) {
                    $query->select('itemCategoryID', 'categoryDescription');
                }, 'financecategorysub' => function ($query) {
                    $query->with(['finance_gl_code_pl' => function ($query) {
                        $query->select('chartOfAccountSystemID', 'AccountDescription', 'AccountCode');
                    }]);
                    $query->select('itemCategorySubID', 'categoryDescription', 'financeGLcodePLSystemID');
                }, 'unit' => function ($query) {
                    $query->select('UnitID', 'UnitShortCode');
                }])->leftJoin('erp_grvdetails as gdet', function ($join) use ($companyID) {
                    $join->on('erp_purchaseorderdetails.purchaseOrderDetailsID', '=', 'gdet.purchaseOrderDetailsID');
                    $join->whereIN('gdet.companySystemID', $companyID);
                    $join->selectRaw('SUM(gdet.noQty) as noQty');
                })->leftJoin(
                    DB::raw('(SELECT
    erp_grvdetails.purchaseOrderDetailsID,
    erp_grvdetails.purchaseOrderMastertID,
    erp_grvdetails.itemCode,
    max(erp_grvmaster.grvDate) AS lastOfgrvDate 
FROM
    (
    erp_grvmaster INNER JOIN erp_grvdetails ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID 
    ) 
WHERE
    erp_grvmaster.approved=- 1 and purchaseOrderDetailsID>0 GROUP BY erp_grvdetails.purchaseOrderMastertID,erp_grvdetails.purchaseOrderDetailsID,erp_grvdetails.itemCode) as gdet2'),
                    function ($join) use ($companyID) {
                        $join->on('erp_purchaseorderdetails.purchaseOrderMasterID', '=', 'gdet2.purchaseOrderMastertID');
                        $join->on('erp_purchaseorderdetails.purchaseOrderDetailsID', '=', 'gdet2.purchaseOrderDetailsID');
                        $join->on('erp_purchaseorderdetails.itemCode', '=', 'gdet2.itemCode');
                    })->selectRaw('erp_purchaseorderdetails.purchaseOrderMasterID,erp_purchaseorderdetails.purchaseOrderDetailsID,gdet2.lastOfgrvDate,
                    erp_purchaseorderdetails.unitOfMeasure,
                    IF((erp_purchaseorderdetails.noQty-gdet.noQty) = 0,"Fully Received",if(ISNULL(gdet.noQty) OR gdet.noQty=0 ,"Not Recieved","Partially Recieved")) as receivedStatus,
                    IFNULL((erp_purchaseorderdetails.noQty-gdet.noQty),0) as qtyToReceive,IFNULL(gdet.noQty,0) as qtyReceived,erp_purchaseorderdetails.itemFinanceCategoryID,erp_purchaseorderdetails.itemFinanceCategorySubID,erp_purchaseorderdetails.itemPrimaryCode,erp_purchaseorderdetails.itemDescription,erp_purchaseorderdetails.supplierPartNumber,erp_purchaseorderdetails.noQty,( ( erp_purchaseorderdetails.GRVcostPerUnitComRptCur / ( 100- erp_purchaseorderdetails.discountPercentage ) ) * 100 ) AS UnitCostWithOutDiscount,erp_purchaseorderdetails.GRVcostPerUnitComRptCur as UnitCostWithDiscount,erp_purchaseorderdetails.discountPercentage,( ( ( ( erp_purchaseorderdetails.GRVcostPerUnitComRptCur / ( 100- erp_purchaseorderdetails.discountPercentage ) ) * 100 ) ) - erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS discountAmount,( erp_purchaseorderdetails.noQty * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS Total')
                    ->whereIN('erp_purchaseorderdetails.companySystemID', $companyID)->get();
                $outputArray = [];
                if ($output) {
                    foreach ($output as $key => $val) {
                        $outputArray[] = array(
                            'companyID' => $val->order->companyID,
                            'purchaseOrderCode' => $val->order->purchaseOrderCode,
                            'postingYear' => $val->order->PostingYear,
                            'orderDate' => $val->order->OrderDate,
                            'STATUS' => $val->order->STATUS,
                            'supplierPrimaryCode' => $val->order->supplierPrimaryCode,
                            'supplierName' => $val->order->supplierName,
                            'creditPeriod' => $val->order->creditPeriod,
                            'deliveryTerms' => $val->order->deliveryTerms,
                            'paymentTerms' => $val->order->paymentTerms,
                            'expectedDeliveryDate' => $val->order->expectedDeliveryDate,
                            'narration' => $val->order->narration,
                            'total' => $val->Total,
                            'lastOfgrvDate' => $val->lastOfgrvDate,
                            'unitCostWithDiscount' => $val->UnitCostWithDiscount,
                            'unitCostWithOutDiscount' => $val->UnitCostWithOutDiscount,
                            'qtyReceived' => $val->qtyReceived,
                            'qtyToReceive' => $val->qtyToReceive,
                            'discountAmount' => $val->discountAmount,
                            'discountPercentage' => $val->discountPercentage,
                            'unitShortCode' => isset($val->unit) ? $val->unit->UnitShortCode : '',
                            'financecategory' => isset($val->financecategory) ? $val->financecategory->categoryDescription : '',
                            'financecategorysub' => isset($val->financecategory) ? $val->financecategorysub->categoryDescription : '',
                            'finance_gl_code_pl' => isset ($val->financecategorysub) ? isset ($val->financecategorysub->finance_gl_code_pl) ? $val->financecategorysub->finance_gl_code_pl->AccountDescription : '' : '',
                            'AccountCode' => isset ($val->financecategorysub->finance_gl_code_pl) ? $val->financecategorysub->finance_gl_code_pl->AccountCode : '',
                            'itemDescription' => $val->itemDescription,
                            'itemPrimaryCode' => $val->itemPrimaryCode,
                            'noQty' => $val->noQty,
                            'receivedStatus' => $val->receivedStatus,
                            'receivedDate' => $val->lastOfgrvDate,
                            'location' => isset($val->order->location) ? $val->order->location->locationName : '',
                            'segment' => isset($val->order->segment) ? $val->order->segment->ServiceLineDes : '');
                    }
                }
                return $this->sendResponse($outputArray, 'Successfully report retrieved');
            default:
                'Error Message';
        }
    }
}
