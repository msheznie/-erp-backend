<?php
/**
 * =============================================
 * -- File Name : PurchaseRequestAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Purchase Request
 * -- Author : Mohamed Fayas
 * -- Create date : 26 - March 2018
 * -- Description : This file contains the all CRUD for PPurchase Request
 * -- REVISION HISTORY
 * -- Date: 26-March 2018 By: Fayas Description: Added new functions named as getPurchaseRequestByDocumentType()
 * -- Date: 27-March 2018 By: Fayas Description: Added new functions named as getPurchaseRequestFormData()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseRequestAPIRequest;
use App\Http\Requests\API\UpdatePurchaseRequestAPIRequest;
use App\Models\Months;
use App\Models\PurchaseRequest;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\PurchaseRequestRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;

/**
 * Class PurchaseRequestController
 * @package App\Http\Controllers\API
 */
class PurchaseRequestAPIController extends AppBaseController
{
    /** @var  PurchaseRequestRepository */
    private $purchaseRequestRepository;

    public function __construct(PurchaseRequestRepository $purchaseRequestRepo)
    {
        $this->purchaseRequestRepository = $purchaseRequestRepo;
    }

    /**
     * Display a listing of the PurchaseRequest.
     * GET|HEAD /purchaseRequests
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->purchaseRequestRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseRequestRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseRequests = $this->purchaseRequestRepository->all();

        return $this->sendResponse($purchaseRequests->toArray(), 'Purchase Requests retrieved successfully');
    }

    /**
     * get Purchase Request Form Data
     * get /getPurchaseRequestFormData
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getPurchaseRequestFormData(Request $request){

        $companyId = $request['companyId'];

        $segments = SegmentMaster::where("companySystemID",$companyId)->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();


        $years = PurchaseRequest::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year','desc')
            ->get();

        $output = array('segments' => $segments,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    /**
     * get Purchase Request By Document Type.
     * POST /getPurchaseRequestByDocumentType
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getPurchaseRequestByDocumentType(Request $request)
    {

        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $purchaseRequests = PurchaseRequest::where('companySystemID', $input['companyId'])
            ->where('documentSystemID', $input['documentId'])
            ->with(['created_by' => function ($query) {
                //$query->select(['empName']);
            },'priority' => function($query){
                //$query->select(['priorityDescription']);
            },'location' => function($query){
            },'segment' => function($query){

            }]);

        if(array_key_exists ('serviceLineSystemID' , $input ))
        {
            $purchaseRequests->where('serviceLineSystemID',$input['serviceLineSystemID']);
        }

        if(array_key_exists ('cancelledYN' , $input )){
            if($input['cancelledYN'] == 0 || $input['cancelledYN'] == -1)
            {
                $purchaseRequests->where('cancelledYN',$input['cancelledYN']);
            }
        }

        if(array_key_exists ('PRConfirmedYN' , $input )){
            if($input['PRConfirmedYN'] == 0 || $input['PRConfirmedYN'] == 1)
            {
                $purchaseRequests->where('PRConfirmedYN',$input['PRConfirmedYN']);
            }
        }

        if(array_key_exists ('approved' , $input )){
            if($input['approved'] == 0 || $input['approved'] == 1)
            {
                $purchaseRequests->where('PRConfirmedYN',$input['PRConfirmedYN']);
            }
        }

        if(array_key_exists ('month' , $input )){
            $purchaseRequests->whereMonth('createdDateTime', '=', $input['month']);
        }

        if(array_key_exists ('year' , $input )){
            $purchaseRequests->whereYear('createdDateTime', '=', $input['year']);
        }

        $purchaseRequests =  $purchaseRequests->select(
                ['erp_purchaserequest.purchaseRequestID',
                    'erp_purchaserequest.purchaseRequestCode',
                    'erp_purchaserequest.createdDateTime',
                    'erp_purchaserequest.createdUserSystemID',
                    'erp_purchaserequest.comments',
                    'erp_purchaserequest.location',
                    'erp_purchaserequest.priority',
                    'erp_purchaserequest.cancelledYN',
                    'erp_purchaserequest.PRConfirmedYN',
                    'erp_purchaserequest.approved',
                    'erp_purchaserequest.timesReferred',
                    'erp_purchaserequest.serviceLineSystemID',
                ]);

        return \DataTables::eloquent($purchaseRequests)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('purchaseRequestID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
        ///return $this->sendResponse($supplierMasters->toArray(), 'Supplier Masters retrieved successfully');*/
    }

    /**
     * Store a newly created PurchaseRequest in storage.
     * POST /purchaseRequests
     *
     * @param CreatePurchaseRequestAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePurchaseRequestAPIRequest $request)
    {
        $input = $request->all();

        $purchaseRequests = $this->purchaseRequestRepository->create($input);

        return $this->sendResponse($purchaseRequests->toArray(), 'Purchase Request saved successfully');
    }

    /**
     * Display the specified PurchaseRequest.
     * GET|HEAD /purchaseRequests/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->purchaseRequestRepository->findWithoutFail($id);

        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        return $this->sendResponse($purchaseRequest->toArray(), 'Purchase Request retrieved successfully');
    }

    /**
     * Update the specified PurchaseRequest in storage.
     * PUT/PATCH /purchaseRequests/{id}
     *
     * @param  int $id
     * @param UpdatePurchaseRequestAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePurchaseRequestAPIRequest $request)
    {
        $input = $request->all();

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->purchaseRequestRepository->findWithoutFail($id);

        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        $purchaseRequest = $this->purchaseRequestRepository->update($input, $id);

        return $this->sendResponse($purchaseRequest->toArray(), 'PurchaseRequest updated successfully');
    }

    /**
     * Remove the specified PurchaseRequest from storage.
     * DELETE /purchaseRequests/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->purchaseRequestRepository->findWithoutFail($id);

        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        $purchaseRequest->delete();

        return $this->sendResponse($id, 'Purchase Request deleted successfully');
    }
}
