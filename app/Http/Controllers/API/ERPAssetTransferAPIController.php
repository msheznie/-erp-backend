<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateERPAssetTransferAPIRequest;
use App\Http\Requests\API\UpdateERPAssetTransferAPIRequest;
use App\Models\ERPAssetTransfer;
use App\Repositories\ERPAssetTransferRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\CompanyPolicyMaster;
use App\Models\DocumentMaster;
use App\Models\ERPAssetTransferDetail;
use App\Models\FixedAssetMaster;
use App\Models\Location;
use App\Models\PurchaseOrderDetails;
use App\Models\SegmentMaster;
use Carbon\Carbon;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;

/**
 * Class ERPAssetTransferController
 * @package App\Http\Controllers\API
 */

class ERPAssetTransferAPIController extends AppBaseController
{
    /** @var  ERPAssetTransferRepository */
    private $eRPAssetTransferRepository;

    public function __construct(ERPAssetTransferRepository $eRPAssetTransferRepo)
    {
        $this->eRPAssetTransferRepository = $eRPAssetTransferRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/eRPAssetTransfers",
     *      summary="Get a listing of the ERPAssetTransfers.",
     *      tags={"ERPAssetTransfer"},
     *      description="Get all ERPAssetTransfers",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/ERPAssetTransfer")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->eRPAssetTransferRepository->pushCriteria(new RequestCriteria($request));
        $this->eRPAssetTransferRepository->pushCriteria(new LimitOffsetCriteria($request));
        $eRPAssetTransfers = $this->eRPAssetTransferRepository->all();

        return $this->sendResponse($eRPAssetTransfers->toArray(), 'E R P Asset Transfers retrieved successfully');
    }

    /**
     * @param CreateERPAssetTransferAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/eRPAssetTransfers",
     *      summary="Store a newly created ERPAssetTransfer in storage",
     *      tags={"ERPAssetTransfer"},
     *      description="Store ERPAssetTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ERPAssetTransfer that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ERPAssetTransfer")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ERPAssetTransfer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateERPAssetTransferAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, ['type', 'serviceLineSystemID', 'location', 'prBelongsYear', 'budgetYear']);
        $data = array_only($input, ['type', 'serviceLineSystemID', 'location']);
        $company_id = $input['companyID'];
        $messages = [
            'document_date.required' => 'Document date field is required.',
            'narration.required' => 'Narration field is required.',
            'type.required' => 'Type field is required.',
            'reference_no.required' => 'Reference No is required.',
            'location.required' => 'Location No is required.',
            'serviceLineSystemID.required' => 'Segment is required.',
        ];
        $validator = \Validator::make($input, [
            'document_date' => 'required|date',
            'narration' => 'required',
            'reference_no' => 'required',
            'type' => 'required',
            'location' => 'required',
            'serviceLineSystemID' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }
        $company = Company::where('companySystemID', $company_id)->first();
        $lastSerial = ERPAssetTransfer::where('company_id', $company_id)
            ->orderBy('serial_no', 'desc')
            ->first();
        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serial_no) + 1;
        }
        $documentMaster = DocumentMaster::where('documentSystemID', 103)->first();
        DB::beginTransaction();
        try {
            if ($documentMaster) {
                $assetRequestCode = ($company->CompanyID . '/' . $documentMaster['documentID'] . '/' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                $input['document_id'] = $documentMaster->documentID;
                $input['document_code'] = $assetRequestCode;
            }
            $input['type'] = $input['type'];
            $input['serviceLineSystemID'] = $input['serviceLineSystemID'];
            $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
            if ($segment) {
                $input['serviceLineCode'] = $segment->ServiceLineCode;
            }
            $input['reference_no'] = $input['reference_no'];
            $input['document_date'] = new Carbon($input['document_date']);
            $input['serial_no'] = $lastSerialNumber;
            $input['narration'] = $input['narration'];
            $input['location'] = $input['location'];
            $input['company_id'] = $company_id;
            $input['created_user_id'] = \Helper::getEmployeeSystemID();
            $input['prBelongsYear'] = $input['prBelongsYear'];
            $input['budgetYear'] = $input['budgetYear'];
            $eRPAssetTransfer = $this->eRPAssetTransferRepository->create($input);
            DB::commit();
            return $this->sendResponse($eRPAssetTransfer->toArray(), 'Asset Transfer saved successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Error in  Asset Transfer create process');
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/eRPAssetTransfers/{id}",
     *      summary="Display the specified ERPAssetTransfer",
     *      tags={"ERPAssetTransfer"},
     *      description="Get ERPAssetTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ERPAssetTransfer",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ERPAssetTransfer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var ERPAssetTransfer $eRPAssetTransfer */
        $eRPAssetTransfer = $this->eRPAssetTransferRepository->findWithoutFail($id);

        if (empty($eRPAssetTransfer)) {
            return $this->sendError('E R P Asset Transfer not found');
        }

        return $this->sendResponse($eRPAssetTransfer->toArray(), 'E R P Asset Transfer retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateERPAssetTransferAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/eRPAssetTransfers/{id}",
     *      summary="Update the specified ERPAssetTransfer in storage",
     *      tags={"ERPAssetTransfer"},
     *      description="Update ERPAssetTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ERPAssetTransfer",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ERPAssetTransfer that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ERPAssetTransfer")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ERPAssetTransfer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateERPAssetTransferAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, ['type', 'serviceLineSystemID', 'location', 'prBelongsYear', 'budgetYear']);
        $data = array_only($input, ['type', 'serviceLineSystemID', 'location', 'prBelongsYear', 'budgetYear']);
        /** @var ERPAssetTransfer $eRPAssetTransfer */
        $eRPAssetTransfer = $this->eRPAssetTransferRepository->findWithoutFail($id);

        if (empty($eRPAssetTransfer)) {
            return $this->sendError('E R P Asset Transfer not found');
        }

        $messages = [
            'document_date.required' => 'Document date field is required.',
            'narration.required' => 'Narration field is required.',
            'type.required' => 'Type field is required.',
            'reference_no.required' => 'Reference No is required.',
            'location.required' => 'Location is required.',
        ];
        $validator = \Validator::make($input, [
            'document_date' => 'required|date',
            'narration' => 'required',
            'reference_no' => 'required',
            'type' => 'required',
            'serviceLineSystemID' => 'required',
            'location' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $data['serviceLineSystemID'] = $input['serviceLineSystemID'];
        $segment = SegmentMaster::where('serviceLineSystemID', $data['serviceLineSystemID'])->first();
        if ($segment) {
            $data['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $data['prBelongsYear'] = $input['prBelongsYear'];
        $data['budgetYear'] = $input['budgetYear'];

        $data['type'] = $input['type'];
        $data['location'] = $input['location'];
        $data['reference_no'] = $input['reference_no'];
        $data['document_date'] = new Carbon($input['document_date']);
        $data['narration'] = $input['narration'];

        if (isset($input['confirmed_yn']) == 1) {
            if ($eRPAssetTransfer->confirmed_yn == 0 && $input['confirmed_yn'] == 1) {
                $checkRecordCount = ERPAssetTransferDetail::where('erp_fa_fa_asset_transfer_id', $id)->count();
                if ($checkRecordCount <= 0) {
                    return $this->sendError('Transfer should have at least one record', 500);
                }

                $params = array(
                    'autoID' => $id,
                    'company' => $eRPAssetTransfer->company_id,
                    'document' => 103
                );
                $confirm = \Helper::confirmDocument($params);
                if (!$confirm["success"]) {
                    return $this->sendError($confirm["message"], 500);
                }
            }
        }
        $eRPAssetTransfer = $this->eRPAssetTransferRepository->update($data, $id);
        return $this->sendResponse([], 'Asset Transfer updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/eRPAssetTransfers/{id}",
     *      summary="Remove the specified ERPAssetTransfer from storage",
     *      tags={"ERPAssetTransfer"},
     *      description="Delete ERPAssetTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ERPAssetTransfer",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var ERPAssetTransfer $eRPAssetTransfer */
        $eRPAssetTransfer = $this->eRPAssetTransferRepository->findWithoutFail($id);

        if (empty($eRPAssetTransfer)) {
            return $this->sendError('E R P Asset Transfer not found');
        }
        $eRPAssetTransfer->delete();
        $AssetTransferDetail = new ERPAssetTransferDetail();
        $AssetTransferDetail->where('erp_fa_fa_asset_transfer_id', $id)
            ->delete();
        return $this->sendResponse($id, 'Asset Transfer  deleted successfully');
    }

    public function getAllAssetTransferList(Request $request)
    {
        $input = $request->all();
        $companyID = $input['companyID'];
        $search = $request->input('search.value');


        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $assetTransferList = ERPAssetTransfer::where('company_id', $companyID);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetTransferList->where(function ($query) use ($search) {
                $query->where('document_code', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('reference_no', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($assetTransferList)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })

            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    function fetchAssetTransferMaster($id)
    {
        $assetTransfer = ERPAssetTransfer::with(['confirmed_by'])->where('id', $id)->first();
        return $this->sendResponse($assetTransfer, 'Asset Request Transfer data');
    }
    public function getAssetTransferApprovalByUser(Request $request)
    {

        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();
        $documentSystemID = 103;
        $assetTransfer = DB::table('erp_documentapproved')
            ->select(
                'erp_fa_fa_asset_transfer.*',
                'employees.empName As created_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode'
            )
            ->join('employeesdepartments', function ($query) use ($companyId, $empID, $documentSystemID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
                $query
                    ->where('employeesdepartments.documentSystemID', $documentSystemID)
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID);
            })->join('erp_fa_fa_asset_transfer', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'current_level_no')
                    ->where('erp_fa_fa_asset_transfer.company_id', $companyId)
                    ->where('erp_fa_fa_asset_transfer.approved_yn', 0)
                    ->where('erp_fa_fa_asset_transfer.confirmed_yn', 1);
            })->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'created_user_id', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', $documentSystemID)
            ->where('erp_documentapproved.companySystemID', $companyId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetTransfer = $assetTransfer->where(function ($query) use ($search) {
                $query->where('document_code', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('reference_no', 'LIKE', "%{$search}%");
            });
        }
        $assetTransfer = $assetTransfer->groupBy('id');

        return \DataTables::of($assetTransfer)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }
    public function rejectAssetTransfer(Request $request)
    {
        $request['documentSystemID'] = 103;
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }
    }
    public function approveAssetTransfer(Request $request)
    {
        $request['documentSystemID'] = 103;
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }
    }
    public function getAssetTransferData(Request $request)
    {
        $input = $request->all();
        $companyId = $request['companyId'];
        $data['locations'] = Location::all();
        $segments = SegmentMaster::where("companySystemID", $companyId);
        $segments = $segments->where('isActive', 1);
        $data['segments'] = $segments->get();



        $data['financialYears'] = array(
            array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year")))
        );
        $checkBudget = CompanyPolicyMaster::where('companyPolicyCategoryID', 17)
            ->where('companySystemID', $companyId)
            ->first();

        $data['checkBudget'] = array('checkBudget' => 0);
        if ($checkBudget) {
            $data['checkBudget'] = $checkBudget->isYesNO;
        }

        return $this->sendResponse($data, 'Record retrieved successfully');
    }
    public function getAssetDropPR(Request $request)
    {
        $companyId = $request['companyId'];
        $assetTransferID =  $request['id'];
        $assetTransferMaster = ERPAssetTransfer::where('company_id', $companyId)->where('id', $assetTransferID)->first();
      /*   $data['assetMaster_drop'] = DB::select("SELECT faID,
        CONCAT(faCode,'-',assetDescription) as asset
        FROM
        `erp_purchaseorderdetails`
        JOIN erp_grvdetails ON erp_grvdetails.purchaseOrderDetailsID = erp_purchaseorderdetails.purchaseOrderDetailsID
        JOIN erp_fa_asset_master ON erp_fa_asset_master.docOriginDetailID = erp_grvdetails.grvDetailsID  
        WHERE
        erp_purchaseorderdetails.companySystemID = $companyId 
        AND purchaseRequestID = $assetTransferMaster->purchaseRequestID  
        AND docOriginDocumentID = 'GRV'"); */
         $data['assetMaster_drop'] =  PurchaseOrderDetails::with(['grvDetails'=> function ($q) {
            $q->with(['assetMaster'=> function ($q2)  {
                $q2->where('docOriginDocumentID','GRV')
                ->where('approved',-1);
            }]);
        } ])
        ->whereHas('grvDetails', function($q){
            $q->whereHas('assetMaster',function ($q2)  {
                $q2->where('docOriginDocumentID','GRV')
                ->where('approved',-1);
            });
        })
        ->where('companySystemID',$companyId )
        ->where('purchaseRequestID',$assetTransferMaster->purchaseRequestID)->get(); 

        return $data;
    }
    public function getAssetTransferApprovalByUserApproved(Request $request)
    {

        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();
        $documentSystemID = 103;
        $assetTransfer = DB::table('erp_documentapproved')
            ->select(
                'erp_fa_fa_asset_transfer.*',
                'employees.empName As created_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode'
            )
            ->join('employeesdepartments', function ($query) use ($companyId, $empID, $documentSystemID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
                $query
                    ->where('employeesdepartments.documentSystemID', $documentSystemID)
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID);
            })->join('erp_fa_fa_asset_transfer', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'current_level_no')
                    ->where('erp_fa_fa_asset_transfer.company_id', $companyId)
                    ->where('erp_fa_fa_asset_transfer.approved_yn', -1)
                    ->where('erp_fa_fa_asset_transfer.confirmed_yn', 1);
            })->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'created_user_id', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', $documentSystemID)
            ->where('erp_documentapproved.companySystemID', $companyId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetTransfer = $assetTransfer->where(function ($query) use ($search) {
                $query->where('document_code', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('reference_no', 'LIKE', "%{$search}%");
            });
        }
        $assetTransfer = $assetTransfer->groupBy('id');

        return \DataTables::of($assetTransfer)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }
}
