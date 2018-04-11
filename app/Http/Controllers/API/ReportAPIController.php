<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\ProcumentOrder;
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
                $output = ProcumentOrder::with(['detail' => function ($query) use ($companyID) {
                    $query->with(['financecategory' => function ($query) {
                        $query->select('itemCategoryID', 'categoryDescription');
                    }, 'financecategorysub' => function ($query) {
                        $query->with(['finance_gl_code_pl' => function ($query) {
                            $query->select('chartOfAccountSystemID', 'AccountDescription','AccountCode');
                        }]);
                        $query->select('itemCategorySubID', 'categoryDescription', 'financeGLcodePLSystemID');
                    },'unit' => function ($query){
                        $query->select('UnitID', 'UnitShortCode');
                    }]);
                    $query->leftJoin('erp_grvdetails as gdet',function($join) use ($companyID){
                        $join->on('erp_purchaseorderdetails.purchaseOrderDetailsID','=','gdet.purchaseOrderDetailsID');
                        $join->whereIN('gdet.companySystemID',$companyID);
                        $join->selectRaw('SUM(gdet.noQty) as noQty');
                    });
                    $query->leftJoin(
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
                        function($join) use ($companyID)
                        {
                            $join->on('erp_purchaseorderdetails.purchaseOrderMasterID','=','gdet2.purchaseOrderMastertID');
                            $join->on('erp_purchaseorderdetails.purchaseOrderDetailsID','=','gdet2.purchaseOrderDetailsID');
                            $join->on('erp_purchaseorderdetails.itemCode','=','gdet2.itemCode');
                        });

                    $query->selectRaw('gdet2.lastOfgrvDate,
                    erp_purchaseorderdetails.unitOfMeasure,
                    IF((erp_purchaseorderdetails.noQty-gdet.noQty) = 0,"Fully Received",if(ISNULL(gdet.noQty) OR gdet.noQty=0 ,"Not Recieved","Partially Recieved")) as receivedStatus,
                    IFNULL((erp_purchaseorderdetails.noQty-gdet.noQty),0) as qtyToReceive,IFNULL(gdet.noQty,0) as qtyReceived,erp_purchaseorderdetails.itemFinanceCategoryID,erp_purchaseorderdetails.itemFinanceCategorySubID,erp_purchaseorderdetails.purchaseOrderDetailsID,erp_purchaseorderdetails.purchaseOrderMasterID,erp_purchaseorderdetails.itemPrimaryCode,erp_purchaseorderdetails.itemDescription,erp_purchaseorderdetails.supplierPartNumber,erp_purchaseorderdetails.noQty,( ( erp_purchaseorderdetails.GRVcostPerUnitComRptCur / ( 100- erp_purchaseorderdetails.discountPercentage ) ) * 100 ) AS UnitCostWithOutDiscount,erp_purchaseorderdetails.GRVcostPerUnitComRptCur as UnitCostWithDiscount,erp_purchaseorderdetails.discountPercentage,( ( ( ( erp_purchaseorderdetails.GRVcostPerUnitComRptCur / ( 100- erp_purchaseorderdetails.discountPercentage ) ) * 100 ) ) - erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS discountAmount,( erp_purchaseorderdetails.noQty * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS Total');
                }, 'segment' => function ($query) {
                    $query->select('ServiceLineDes', 'serviceLineSystemID');
                }, 'location' => function ($query) {
                    $query->select('locationName', 'locationID');
                }])->selectRaw('companyID,poLocation,serviceLineSystemID,purchaseOrderID,approved,companyID,YEAR ( approvedDate ) AS PostingYear,approvedDate AS OrderDate,purchaseOrderCode,IF
	( sentToSupplier = 0, "Not Released", "Released" ) AS STATUS,supplierID,supplierPrimaryCode,supplierName,creditPeriod,deliveryTerms,paymentTerms,expectedDeliveryDate,narration')->whereBetween('approvedDate', [$startDate, $endDate])->where('approved', -1)->whereIN('companySystemID', $companyID)->get();

                $outputArray = [];
                if ($output) {
                    foreach ($output as $key => $val) {
                        $outputArray[] = array(
                            'companyID' => $val->companyID,
                            'purchaseOrderCode' => $val->purchaseOrderCode,
                            'postingYear' => $val->PostingYear,
                            'orderDate' => $val->OrderDate,
                            'STATUS' => $val->STATUS,
                            'supplierPrimaryCode' => $val->supplierPrimaryCode,
                            'supplierName' => $val->supplierName,
                            'creditPeriod' => $val->creditPeriod,
                            'deliveryTerms' => $val->deliveryTerms,
                            'paymentTerms' => $val->paymentTerms,
                            'expectedDeliveryDate' => $val->expectedDeliveryDate,
                            'narration' => $val->narration,
                            'total' => isset($val->detail)? $val->detail->Total: '',
                            'lastOfgrvDate' => isset($val->detail)? $val->detail->lastOfgrvDate: '',
                            'unitCostWithDiscount' => isset($val->detail) ? $val->detail->UnitCostWithDiscount: '',
                            'unitCostWithOutDiscount' => isset($val->detail) ? $val->detail->UnitCostWithOutDiscount: '',
                            'qtyReceived' => isset($val->detail) ? $val->detail->qtyReceived: '',
                            'qtyToReceive' => isset($val->detail) ? $val->detail->qtyToReceive: '',
                            'discountAmount' => isset($val->detail) ? $val->detail->discountAmount: '',
                            'discountPercentage' => isset($val->detail) ? $val->detail->discountPercentage: '',
                            'unitShortCode' => isset($val->detail) ? isset($val->detail->unit) ? $val->detail->unit->UnitShortCode : '': '',
                            'financecategory' => isset($val->detail) ? isset($val->detail->financecategory) ? $val->detail->financecategory->categoryDescription : '': '',
                            'financecategorysub' => isset($val->detail) ? isset($val->detail->financecategorysub) ? $val->detail->financecategorysub->categoryDescription : '': '',
                            'finance_gl_code_pl' => isset($val->detail) ? isset($val->detail->financecategorysub) ? isset ($val->detail->financecategorysub->finance_gl_code_pl) ? $val->detail->financecategorysub->finance_gl_code_pl->AccountDescription : '' : '': '',
                            'AccountCode' => isset($val->detail) ? isset($val->detail->financecategorysub) ? isset ($val->detail->financecategorysub->finance_gl_code_pl) ? $val->detail->financecategorysub->finance_gl_code_pl->AccountCode : '' : '': '',
                            'itemDescription' => isset($val->detail) ?$val->detail->itemDescription: '',
                            'itemPrimaryCode' => isset($val->detail) ?$val->detail->itemPrimaryCode: '',
                            'noQty' => isset($val->detail) ? $val->detail->noQty: '',
                            'receivedStatus' => isset($val->detail) ? $val->detail->receivedStatus: '',
                            'receivedDate' => isset($val->detail) ? $val->detail->lastOfgrvDate: '',
                            'location' => isset($val->location) ? $val->location->locationName : '',
                            'segment' => isset($val->segment) ? $val->segment->ServiceLineDes : '');
                    }
                }

                return $this->sendResponse($outputArray, 'Successfully report retrieved');
            default:
                'Error Message';
        }
    }
}
