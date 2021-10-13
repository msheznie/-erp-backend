<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\createPullItemsFromPRAPIRequest;
use App\Repositories\ERPPulledMRDetailsRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\PulledItemFromMR;
use App\helper\Helper;
use App\helper\PurcahseRequestDetail;
use App\Models\Company;
use App\Models\GRVDetails;
use App\Models\MaterielRequest;
use App\Models\Unit;
use App\Models\SegmentAllocatedItem;
use App\Models\ItemAssigned;
use App\Models\ProcumentOrder;
use App\Models\ErpItemLedger;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetails;
use Response;
use Illuminate\Support\Facades\DB;
use App\Repositories\SegmentAllocatedItemRepository;


class PulledItemFromMRController extends AppBaseController
{

    private $erpPulledMRDetailsRepository;
    private $segmentAllocatedItemRepository;

    public function __construct(ERPPulledMRDetailsRepository $erpPulledMRDetailsRepository, SegmentAllocatedItemRepository $segmentAllocatedItemRepository)
    {
        $this->erpPulledMRDetailsRepository = $erpPulledMRDetailsRepository;
        $this->segmentAllocatedItemRepository = $segmentAllocatedItemRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       return 123;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(createPullItemsFromPRAPIRequest $request)
    {


        $input = $request->all();

        $this->validatePolicies();

        $add = app()->make(PurcahseRequestDetail::class);
        $purchaseRequestDetails = $add->validateItem($input);
        $purchaseRequest = PurchaseRequest::find($input['purcahseRequestID']);
        $materialReuest = MaterielRequest::find($input['RequestID']);
        if(isset($materialReuest)) {
            $materialReuest->isSelectedToPR = $input['isChecked'];
            $materialReuest->save();
        }
        
        $id =  $purchaseRequestDetails->purchaseRequestDetailsID;

        DB::beginTransaction();
            if ($purchaseRequestDetails->quantityRequested != $input['quantityRequested']) {
                $checkAlreadyAllocated = SegmentAllocatedItem::where('serviceLineSystemID', '!=',$purchaseRequest->serviceLineSystemID)
                                                         ->where('documentSystemID', $purchaseRequest->documentSystemID)
                                                         ->where('documentMasterAutoID', $input['purcahseRequestID'])
                                                         ->where('documentDetailAutoID', $id)
                                                         ->get();

                if (sizeof($checkAlreadyAllocated) == 0) {
                    $checkAlreadyAllocated = SegmentAllocatedItem::where('serviceLineSystemID',$purchaseRequest->serviceLineSystemID)
                                                         ->where('documentSystemID', $purchaseRequest->documentSystemID)
                                                         ->where('documentMasterAutoID', $input['purcahseRequestID'])
                                                         ->where('documentDetailAutoID', $id)
                                                         ->delete();

                    $allocationData = [
                        'serviceLineSystemID' => $purchaseRequest->serviceLineSystemID,
                        'documentSystemID' => $purchaseRequest->documentSystemID,
                        'docAutoID' => $input['purcahseRequestID'],
                        'docDetailID' => $id
                    ];

                    $segmentAllocatedItem = $this->segmentAllocatedItemRepository->allocateSegmentWiseItem($allocationData);

                    if (!$segmentAllocatedItem['status']) {
                        return $this->sendError($segmentAllocatedItem['message']);
                    }
                } else {
                     $allocatedQty = SegmentAllocatedItem::where('documentSystemID', $purchaseRequest->documentSystemID)
                                                 ->where('documentMasterAutoID', $input['purcahseRequestID'])
                                                 ->where('documentDetailAutoID', $id)
                                                 ->sum('allocatedQty');

                    if ($allocatedQty > $input['quantityRequested']) {
                        return $this->sendError("You cannot update the requested quantity. since quantity has been allocated to segments", 500);
                    }
                }
            }


            DB::commit();
        if($purchaseRequestDetails) {
            $data = $this->erpPulledMRDetailsRepository->create($input);
            return $this->sendResponse($data->toArray(), 'Item added successfully');
        }else {
            return $this->sendError($purchaseRequestDetails['message']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }

    private function validatePolicies() {

    }

    public function removeMRDetails(Request $request) {
        $input = $request->all();

        $data = PulledItemFromMR::where('RequestID',$input['RequestID'])
                                ->where('companySystemID',$input['companySystemID'])
                                ->where('itemCodeSystem',$input['itemCodeSystem'])
                                ->where('purcahseRequestID',$input['purcahseRequestID'])
                                ->first();

        if(!empty($data)) {
            $data->delete();
            return $this->sendResponse($input, 'Data removed successfully');
        }else{
            return $this->sendError('Data not found');
        }
    }

    public function pullAllItemsByPr(Request $request) {
        $input = $request->all();

        $companySystemID = $input['companySystemID'];
        $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $input['purcahseRequestID'])
        ->first();


        $datas =   PulledItemFromMR::selectRaw('erp_requestdetails.partNumber as partNumber, itemmaster.unit as unit,erp_pulled_from_mr.itemCodeSystem,if(itemPrimaryCode="INV",sum(pr_qnty),pr_qnty) as quantityRequested,sum(mr_qnty) as total_mr_qnty,itemmaster.primaryItemCode,itemmaster.itemDescription,itemmaster.itemCodeSystem as itemcode, itemmaster.primaryCode as itemPrimaryCode')
                     ->leftJoin('itemmaster', 'itemmaster.itemCodeSystem', '=', 'erp_pulled_from_mr.itemCodeSystem')
                     ->leftJoin('erp_request', 'erp_request.RequestID', '=', 'erp_pulled_from_mr.RequestID')
                     ->leftJoin('erp_requestdetails', 'erp_requestdetails.RequestDetailsID', '=', 'erp_pulled_from_mr.RequestDetailsID')
                     ->where('purcahseRequestID', $input['purcahseRequestID'])
                    ->groupBy('erp_pulled_from_mr.itemCodeSystem')
                    ->get();
        $group_companies = Helper::getSimilarGroupCompanies($companySystemID);

        foreach($datas as $data) {

            $unit = Unit::where('UnitID',$data->unit)->first();

            $data['uom'] = $unit;

            $puchaseRequestDetails = PurchaseRequestDetails::where('purchaseRequestID',$input['purcahseRequestID'])->where('itemCode',$data->itemcode)->first();
            $data['purchaseRequestDetailsID'] = $puchaseRequestDetails->purchaseRequestDetailsID;
            $data['purchaseRequestID'] = $input['purcahseRequestID'];
            $poQty = PurchaseOrderDetails::whereHas('order', function ($query) use ($group_companies,$data) {
                $query->whereIn('companySystemID', $group_companies)
                    ->where('approved', -1)
                    ->where('poType_N', '!=',5)// poType_N = 5 =>work order
                    ->where('poCancelledYN', 0)
                    ->where('manuallyClosed', 0);
                 })
                ->where('itemCode', $data->itemcode)
                ->where('manuallyClosed',0)
                ->groupBy('erp_purchaseorderdetails.itemCode')
                ->select(
                    [
                        'erp_purchaseorderdetails.companySystemID',
                        'erp_purchaseorderdetails.itemCode',
                        'erp_purchaseorderdetails.itemPrimaryCode'
                    ]
                )
                ->sum('noQty');
                 
                $quantityInHand = ErpItemLedger::where('itemSystemCode',$data->itemcode)
                ->where('companySystemID', $companySystemID)
                ->groupBy('itemSystemCode')
                ->sum('inOutQty');
                $grvQty = GRVDetails::whereHas('grv_master', function ($query) use ($group_companies,$data) {
                    $query->whereIn('companySystemID', $group_companies)
                        ->where('grvTypeID', 2)
                        ->where('approved', -1)
                        ->groupBy('erp_grvmaster.companySystemID');
                })->whereHas('po_detail', function ($query){
                    $query->where('manuallyClosed',0)
                    ->whereHas('order', function ($query){
                        $query->where('manuallyClosed',0);
                    });
                })
                    ->where('itemCode', $data->itemcode)
                    ->groupBy('erp_grvdetails.itemCode')
                    ->select(
                        [
                            'erp_grvdetails.companySystemID',
                            'erp_grvdetails.itemCode'
                        ])
                    ->sum('noQty');

                    $item = ItemAssigned::where('itemCodeSystem', $data->itemcode)
                    ->where('companySystemID', $companySystemID)
                    ->first();

                    $currencyConversion = \Helper::currencyConversion($companySystemID, $item->wacValueLocalCurrencyID, $purchaseRequest->currency, $data->wacValueLocal);
                    $data['estimatedCost'] = $currencyConversion['documentAmount'];


                    $quantityOnOrder = $poQty - $grvQty;
                    $data['poQuantity'] = $poQty;
                    $data['quantityOnOrder'] = $quantityOnOrder;
                    $data['quantityInHand'] = $quantityInHand;
        }
                   
        return $this->sendResponse($datas, 'Data retreived successfully');

    }
}
