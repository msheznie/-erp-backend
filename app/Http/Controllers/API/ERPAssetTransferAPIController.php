<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateERPAssetTransferAPIRequest;
use App\Http\Requests\API\UpdateERPAssetTransferAPIRequest;
use App\Models\ERPAssetTransfer;
use App\Repositories\ERPAssetTransferRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\AssetTransferReferredback;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyPolicyMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\Employee;
use App\Models\AssetRequest;
use App\Models\ERPAssetTransferDetail;
use App\Models\ERPAssetTransferDetailsRefferedback;
use App\Models\FixedAssetMaster;
use App\Models\Location;
use App\Models\PurchaseOrderDetails;
use App\Models\SegmentMaster;
use Carbon\Carbon;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use App\Traits\AuditTrial;

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

        if(isset($input['type']) && $input['type'] == 4) {
            $validator = \Validator::make($input, [
                'document_date' => 'required|date',
                'narration' => 'required',
                'reference_no' => 'required',
                'type' => 'required',
            ], $messages);
        }else if(isset($input['type']) && $input['type'] == 3) {
            $validator = \Validator::make($input, [
                'document_date' => 'required|date',
                'narration' => 'required',
                'reference_no' => 'required',
                'type' => 'required',
                'serviceLineSystemID' => 'required'
            ], $messages);
        }else {
            $validator = \Validator::make($input, [
                'document_date' => 'required|date',
                'narration' => 'required',
                'reference_no' => 'required',
                'type' => 'required',
                'location' => 'required',
                'serviceLineSystemID' => 'required',
            ], $messages);
        }

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

            if(isset($input['serviceLineSystemID'])) {
                $input['serviceLineSystemID'] = $input['serviceLineSystemID'];
                $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
                if ($segment) {
                    $input['serviceLineCode'] = $segment->ServiceLineCode;
                }
            }else {
                $input['serviceLineCode'] = NULL;
                $input['serviceLineSystemID'] = NULL;

            }

            $input['reference_no'] = $input['reference_no'];
            $input['document_date'] = new Carbon($input['document_date']);
            $input['serial_no'] = $lastSerialNumber;
            $input['narration'] = $input['narration'];
            $input['location'] = (isset($input['location'])) ? $input['location'] : NULL;
            $input['company_id'] = $company_id;
            $input['created_user_id'] = \Helper::getEmployeeSystemID();
            $input['prBelongsYear'] = $input['prBelongsYear'];
            $input['budgetYear'] = $input['budgetYear'];
            $input['documentSystemID'] = 103;
            $company = Company::where('companySystemID', $company_id)->first();
            if ($company) {
                $input['company_code'] = $company->CompanyID;
            }
            $input['updated_user_id'] = \Helper::getEmployeeSystemID();
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

        if(isset($input['type']) && $input['type'] == 4) {
            $validator = \Validator::make($input, [
                'document_date' => 'required|date',
                'narration' => 'required',
                'reference_no' => 'required',
                'type' => 'required',
            ], $messages);
        }else if (isset($input['type']) && $input['type'] == 3) {
            $validator = \Validator::make($input, [
                'document_date' => 'required|date',
                'narration' => 'required',
                'reference_no' => 'required',
                'type' => 'required',
                'serviceLineSystemID' => 'required',
            ], $messages);
        }else {
            $validator = \Validator::make($input, [
                'document_date' => 'required|date',
                'narration' => 'required',
                'reference_no' => 'required',
                'type' => 'required',
                'location' => 'required',
                'serviceLineSystemID' => 'required',
            ], $messages);
        }

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if(($input['type'] == 2  || $input['type'] == 1 || $input['type'] == 3) && $input['serviceLineSystemID'] == 0) {
            return $this->sendError('Segment cannot be null. Select a segment and try again', 500);
        }

        if(($input['type'] == 2 || $input['type'] == 1) && $input['location'] == 0) {
            return $this->sendError('Location required cannot be null. Select a location and try again', 500);
        }

        if(isset( $input['serviceLineSystemID'])) {
            $data['serviceLineSystemID'] =  $input['serviceLineSystemID'];
            $segment = SegmentMaster::where('serviceLineSystemID', $data['serviceLineSystemID'])->first();
            if ($segment) {
                $data['serviceLineCode'] = $segment->ServiceLineCode;
            }
        }else {
            $data['serviceLineSystemID'] = NULL;
            $data['serviceLineCode'] = NULL;
        }

        $data['prBelongsYear'] = $input['prBelongsYear'];
        $data['budgetYear'] = $input['budgetYear'];

        $data['type'] = $input['type'];
        $data['location'] = isset($input['location']) ? $input['location'] : NULL;
        $data['reference_no'] = $input['reference_no'];
        $data['document_date'] = new Carbon($input['document_date']);
        $data['narration'] = $input['narration'];
        $data['updated_user_id'] = \Helper::getEmployeeSystemID();

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
                'employeesdepartments.approvalDeligated',
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
        $request['documentSystemCode'] = $request['id'];
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
        $data['assetMaster_drop'] =  PurchaseOrderDetails::with(['grvDetails' => function ($q) {
            $q->with(['assetMaster' => function ($q2) {
                $q2->where('docOriginDocumentID', 'GRV')
                    ->where('approved', -1);
            }]);
        }])
            ->whereHas('grvDetails', function ($q) {
                $q->whereHas('assetMaster', function ($q2) {
                    $q2->where('docOriginDocumentID', 'GRV')
                        ->where('approved', -1);
                });
            })
            ->where('companySystemID', $companyId)
            ->where('purchaseRequestID', $assetTransferMaster->purchaseRequestID)->get();

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
    public function getAssetTransferMasterRecord(Request $request)
    {
        $id = $request->get('assetTransferID');
        $assetTransfer = $this->eRPAssetTransferRepository->getAudit($id);

        if (empty($assetTransfer)) {
            return $this->sendError('Asset Transfer not found');
        }

        return $this->sendResponse($assetTransfer, 'Data retrieved successfully');
    }
    public function assetTransferReopen(Request $request)
    {
        $input = $request->all();

        $id = $input['assetTransferID'];
        $assetTransfer = $this->eRPAssetTransferRepository->findWithoutFail($id);
        $emails = array();
        if (empty($assetTransfer)) {
            return $this->sendError('Asset Transfer not found');
        }

        if ($assetTransfer->approved_yn == 1) {
            return $this->sendError('You cannot reopen this Asset Transfer it is already fully approved');
        }

        if ($assetTransfer->current_level_no > 1) {
            return $this->sendError('You cannot reopen this Asset Transfer it is already partially approved');
        }

        if ($assetTransfer->confirmed_yn == 0) {
            return $this->sendError('You cannot reopen this Asset Transfer, it is not confirmed');
        }

        $updateInput = [
            'confirmed_yn' => 0, 'confirmedByEmpID' => null, 'confirmedByName' => null,
            'confirmed_by_emp_id' => null, 'confirmed_date' => null, 'current_level_no' => 1
        ];

        $this->eRPAssetTransferRepository->update($updateInput, $id);

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', 103)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $assetTransfer->document_code . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $assetTransfer->document_code;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $document->companySystemID)
            ->where('documentSystemCode', $assetTransfer->id)
            ->where('documentSystemID', 103)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $assetTransfer->company_id)
                    ->where('documentSystemID', 103)
                    ->first();

                if (empty($companyDocument)) {
                    return ['success' => false, 'message' => 'Policy not found for this document'];
                }

                $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                    ->where('companySystemID', $documentApproval->companySystemID)
                    ->where('documentSystemID', $documentApproval->documentSystemID);

                $approvalList = $approvalList
                    ->with(['employee'])
                    ->groupBy('employeeSystemID')
                    ->get();

                foreach ($approvalList as $da) {
                    if ($da->employee) {
                        $emails[] = array(
                            'empSystemID' => $da->employee->employeeSystemID,
                            'companySystemID' => $documentApproval->companySystemID,
                            'docSystemID' => $documentApproval->documentSystemID,
                            'alertMessage' => $subject,
                            'emailAlertMessage' => $body,
                            'docSystemCode' => $documentApproval->documentSystemCode
                        );
                    }
                }

                $sendEmail = \Email::sendEmail($emails);
                if (!$sendEmail["success"]) {
                    return ['success' => false, 'message' => $sendEmail["message"]];
                }
            }
        }

        DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $assetTransfer->company_id)
            ->where('documentSystemID', 103)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial(103, $id, $input['reopenComments'], 'Reopened');

        return $this->sendResponse($assetTransfer->toArray(), 'Asset Transfer reopened successfully');
    }

    public function amendAssetTrasfer(Request $request)
    {
        $input = $request->all();

        $assetTransferAutoID = $input['assetTransferID'];

        $assetTransferMasterData = ERPAssetTransfer::find($assetTransferAutoID);
        if (empty($assetTransferMasterData)) {
            return $this->sendError('Asset Transfer not found');
        }

        if ($assetTransferMasterData->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this asset transfer');
        }

        $assetTransferArray = $assetTransferMasterData->toArray();
        $assetTransferArray = \array_diff_key($assetTransferArray, ["transfer_type" => "Transfer Type", "document_date_formatted" => "Document Date Formatted"]);
        $storeAssetTransferHistory = AssetTransferReferredback::insert($assetTransferArray);

        $assetTransferDetailRec = ERPAssetTransferDetail::where('erp_fa_fa_asset_transfer_id', $assetTransferAutoID)->get();

        if (!empty($assetTransferDetailRec)) {
            foreach ($assetTransferDetailRec as $assetTrans) {
                $assetTrans['timesReferred'] = $assetTransferMasterData->timesReferred;
            }
        }

        $assetTransferDetailArray = $assetTransferDetailRec->toArray();

        $storeAssetTransferDetailHistory = ERPAssetTransferDetailsRefferedback::insert($assetTransferDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $assetTransferAutoID)
            ->where('companySystemID', $assetTransferMasterData->company_id)
            ->where('documentSystemID', $assetTransferMasterData->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $assetTransferMasterData->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $assetTransferAutoID)
            ->where('companySystemID', $assetTransferMasterData->company_id)
            ->where('documentSystemID', $assetTransferMasterData->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $assetTransferMasterData->refferedBackYN = 0;
            $assetTransferMasterData->confirmed_yn = 0;
            $assetTransferMasterData->confirmed_by_emp_id = null;
            $assetTransferMasterData->confirmedByName = null;
            $assetTransferMasterData->confirmedByEmpID = null;
            $assetTransferMasterData->confirmed_date = null;
            $assetTransferMasterData->current_level_no = 1;
            $assetTransferMasterData->save();
        }
        return $this->sendResponse($assetTransferMasterData->toArray(), 'Asset Transfer amend successfully');
    }

    public function assetStatus(Request $request)
    {
        $input = $request->all();
        $companyID = $input['companyId'];
        $assetID = $input['assetID'];

        $assetRequestValidation = $this->validateAssetRequest($input);

        $data['data']['acknowledgedYN'] = 0;

        if(!$assetRequestValidation['success']) {
            $data['data']['assetCode'] = isset($assetRequestValidation['data'][0]->assetMaster) ? $assetRequestValidation['data'][0]->assetMaster->asset_code_concat : '-';
            if(isset($assetRequestValidation['data'])) {
                $data['data']['assetRecords'] = $assetRequestValidation['data'];
            }
            $data['data']['message'] = $assetRequestValidation['message'];
            $data['data']['acknowledgedYN'] = 1;
        }else {
            $data['data'] = [];
        }
        return $data['data'];


    }

    public function validateAssetRequest($input) {
        $companyID = $input['companyId'];
        $assetID = $input['assetID'];
        $assetRequestMasterID = $input['assetRequestMasterID'];
        $assetRequest  = AssetRequest::select(['departmentSystemID','type','emp_id'])->where('id',$assetRequestMasterID)->first();
        $fixedAsset = FixedAssetMaster::select(['LOCATION','departmentSystemID','empID'])->where('faID',$assetID)->first();

        if($assetRequest->type == 1) {
            return $this->requestForEmployeeValidation($assetID,$assetRequestMasterID,$assetRequest,$fixedAsset,$companyID);
        }

        if($assetRequest->type == 2) {
           return $this->requestForDepartmentValidation($assetID,$assetRequestMasterID,$assetRequest,$fixedAsset,$companyID);
        }

        return ['success'=> false, 'message' => "Asset Request type not found"];

    }

    public function requestForEmployeeValidation($assetID,$assetRequestMasterID,$assetRequest,$fixedAsset,$companyID){
        // check asset's previous request type
        $isAssetAlreadyAssigned = ERPAssetTransferDetail::with(['assetTransferMaster' => function ($query) use ($companyID) {
            $query->where('company_id', $companyID)
                ->where('approved_yn', -1);
        }, 'assetMaster', 'assetRequestDetail' => function ($q) {
            $q->with(['createdUserID']);
        }, 'smePayAsset'])
            ->whereHas('assetTransferMaster', function ($query) use ($companyID) {
                $query->where('company_id', $companyID)
                    ->where('approved_yn', -1);
            })->where('fa_master_id',$assetID)->orderby('id','desc')->first();

        $data = $this->getDataOfAssetAcknowldged($assetID,$companyID);


        if($isAssetAlreadyAssigned && $isAssetAlreadyAssigned->erp_fa_fa_asset_request_id) {
            $assetRequestedAssigned  = AssetRequest::select(['departmentSystemID','type','emp_id'])->where('id',$isAssetAlreadyAssigned->erp_fa_fa_asset_request_id)->first();
            if($assetRequestedAssigned) {
                if($assetRequestedAssigned->type == 2) {
                    if($isAssetAlreadyAssigned && $isAssetAlreadyAssigned->receivedYN == 0) {
                        return ['success'=> false, 'message' => "Asset transferred to department and still not acknowledged",'data' => $data];
                    }
                }
            }
        }



        // check wether the request is from the same employee of the asset assigned
        if($fixedAsset->empID == $assetRequest->emp_id) {
            // check wether the asset already assigned to the employee
            $isAssetAlreadyAssignedForEmployee = ERPAssetTransferDetail::with(['assetTransferMaster' => function ($query) use ($companyID) {
                $query->where('company_id', $companyID)
                    ->where('approved_yn', -1);
            }, 'assetMaster', 'assetRequestDetail' => function ($q) {
                $q->with(['createdUserID']);
            }, 'smePayAsset'])
                ->whereHas('assetTransferMaster', function ($query) use ($companyID) {
                    $query->where('company_id', $companyID)
                        ->where('approved_yn', -1);
                })->where('to_emp_id',$assetRequest->emp_id)->where('fa_master_id',$assetID)->orderby('id','desc')->first();
            if(isset($isAssetAlreadyAssignedForEmployee)) {
                if($isAssetAlreadyAssignedForEmployee->receivedYN == 1) {
                    $data = ERPAssetTransferDetail::with(['assetTransferMaster' => function ($query) use ($companyID) {
                        $query->where('company_id', $companyID)
                            ->where('approved_yn', -1);
                    }, 'assetMaster', 'assetRequestDetail' => function ($q) {
                        $q->with(['createdUserID']);
                    }, 'smePayAsset'])
                        ->whereHas('assetTransferMaster', function ($query) use ($companyID) {
                            $query->where('company_id', $companyID)
                                ->where('approved_yn', -1);
                        })
                        ->where('fa_master_id', $assetID)
                        ->where('to_emp_id',$assetRequest->emp_id)
                        ->where('receivedYN', 1)
                        ->get();
                    return ['success'=> false, 'message' => "Asset transferred and acknowdged already for this employee",'data' => $data];
                }else {
                    $data2 = ERPAssetTransferDetail::with(['assetTransferMaster' => function ($query) use ($companyID) {
                        $query->where('company_id', $companyID)
                            ->where('approved_yn', -1);
                    }, 'assetMaster', 'assetRequestDetail' => function ($q) {
                        $q->with(['createdUserID']);
                    }, 'smePayAsset'])
                        ->whereHas('assetTransferMaster', function ($query) use ($companyID) {
                            $query->where('company_id', $companyID)
                                ->where('approved_yn', -1);
                        })
                        ->where('fa_master_id', $assetID)
                        ->where('to_emp_id',$assetRequest->emp_id)
                        ->where('receivedYN', 0)
                        ->get();
                    return ['success'=> false, 'message' => "Asset transferred and still not acknowledged for this employee",'data' => $data2];
                }
            }
        }

        // check wether the asset already assigned to any employee
        $isAssetAlreadyAssigned = ERPAssetTransferDetail::with(['assetTransferMaster' => function ($query) use ($companyID) {
            $query->where('company_id', $companyID)
                ->where('approved_yn', -1);
        }, 'assetMaster', 'assetRequestDetail' => function ($q) {
            $q->with(['createdUserID']);
        }, 'smePayAsset'])
            ->whereHas('assetTransferMaster', function ($query) use ($companyID) {
                $query->where('company_id', $companyID)
                    ->where('approved_yn', -1);
            })->where('fa_master_id',$assetID)->orderby('id','desc')->first();
        if($isAssetAlreadyAssigned && $isAssetAlreadyAssigned->receivedYN == 0) {
            $data = $this->getDataOfAssetNotAcknowldged($assetID,$companyID);
            return ['success'=> false, 'message' => "Asset transferred and still not acknowledged",'data' => $data];
        }

        $isAssetAlreadyConfirmedToDepartment = ERPAssetTransferDetail::with(['assetTransferMaster' => function ($query) use ($companyID) {
            $query->where('company_id', $companyID)
                ->where('confirmed_yn', 1);
        }, 'assetMaster', 'assetRequestDetail' => function ($q) {
            $q->with(['createdUserID']);
        }, 'smePayAsset'])
            ->whereHas('assetTransferMaster', function ($query) use ($companyID) {
                $query->where('company_id', $companyID)
                    ->where('confirmed_yn', 1);
            })->where('fa_master_id',$assetID)->orderby('id','desc')->first();
        if($isAssetAlreadyConfirmedToDepartment && $isAssetAlreadyConfirmedToDepartment->receivedYN == 0) {
            $dataNew = $this->getDataOfAssetConfirmed($assetID,$companyID);
            return ['success'=> false, 'message' => "Asset transferred and still not acknowledged",'data' => $dataNew];
        }

        return ['success'=> true, 'message' => "Asset transferred successfully"];

        
    }

    public function requestForDepartmentValidation($assetID,$assetRequestMasterID,$assetRequest,$fixedAsset,$companyID) {
        // check asset's previous request type
        $isAssetAlreadyAssigned = ERPAssetTransferDetail::with(['assetTransferMaster' => function ($query) use ($companyID) {
            $query->where('company_id', $companyID)
                ->where('approved_yn', -1);
        }, 'assetMaster', 'assetRequestDetail' => function ($q) {
            $q->with(['createdUserID']);
        }, 'smePayAsset'])
            ->whereHas('assetTransferMaster', function ($query) use ($companyID) {
                $query->where('company_id', $companyID)
                    ->where('approved_yn', -1);
            })->where('fa_master_id',$assetID)->orderby('id','desc')->first();

        $data = $this->getDataOfAssetNotAcknowldgedByEmployee($assetID,$companyID);
        if($isAssetAlreadyAssigned) {
            $assetRequestedAssigned  = AssetRequest::select(['departmentSystemID','type','emp_id'])->where('id',$isAssetAlreadyAssigned->erp_fa_fa_asset_request_id)->first();
            if($assetRequestedAssigned) {
                if($assetRequestedAssigned->type == 1) {
                    if($isAssetAlreadyAssigned && $isAssetAlreadyAssigned->receivedYN == 0) {
                        return ['success'=> false, 'message' => "Asset transferred to employee and still not acknowledged",'data' => $data];
                    }
                }
            }
        }

        


        // check wether the request is from the same department of the asset assigned
        if($fixedAsset->departmentSystemID == $assetRequest->departmentSystemID) {
            // check wether the asset already assigned to the department
            $isAssetAlreadyAssignedForDepartment = ERPAssetTransferDetail::with(['assetTransferMaster' => function ($query) use ($companyID) {
                $query->where('company_id', $companyID)
                    ->where('approved_yn', -1);
            }, 'assetMaster', 'assetRequestDetail' => function ($q) {
                $q->with(['createdUserID']);
            }, 'smePayAsset'])
                ->whereHas('assetTransferMaster', function ($query) use ($companyID) {
                    $query->where('company_id', $companyID)
                        ->where('approved_yn', -1);
                })->where('departmentSystemID',$assetRequest->departmentSystemID)->where('fa_master_id',$assetID)->orderby('id','desc')->first();
            if(isset($isAssetAlreadyAssignedForDepartment)) {
                $dataDepartment = $this->getDataOfAssetNotAcknowldgedByDepartment($assetID,$companyID,$assetRequest->departmentSystemID,$isAssetAlreadyAssignedForDepartment->receivedYN);

                if($isAssetAlreadyAssignedForDepartment->receivedYN == 1) {
                    return ['success'=> false, 'message' => "Asset already transferred to this department",'data' => $dataDepartment];
                }else {
                    return ['success'=> false, 'message' => "Asset transferred and still not acknowledged to this department",'data' => $dataDepartment];
                }
            }
        }

        // check wether the asset already assigned to any department
        $isAssetAlreadyAssigned = ERPAssetTransferDetail::with(['assetTransferMaster' => function ($query) use ($companyID) {
            $query->where('company_id', $companyID)
                ->where('approved_yn', -1);
        }, 'assetMaster', 'assetRequestDetail' => function ($q) {
            $q->with(['createdUserID']);
        }, 'smePayAsset'])
            ->whereHas('assetTransferMaster', function ($query) use ($companyID) {
                $query->where('company_id', $companyID)
                    ->where('approved_yn', -1);
            })->where('fa_master_id',$assetID)->orderby('id','desc')->first();
        if($isAssetAlreadyAssigned && $isAssetAlreadyAssigned->receivedYN == 0) {
            $data = $this->getDataOfAssetNotAcknowldged($assetID,$companyID);
            return ['success'=> false, 'message' => "Asset transferred and still not acknowledged",'data' => $data];
        }


        $isAssetAlreadyConfirmedToDepartment = ERPAssetTransferDetail::with(['assetTransferMaster' => function ($query) use ($companyID) {
            $query->where('company_id', $companyID)
                ->where('confirmed_yn', 1);
        }, 'assetMaster', 'assetRequestDetail' => function ($q) {
            $q->with(['createdUserID']);
        }, 'smePayAsset'])
            ->whereHas('assetTransferMaster', function ($query) use ($companyID) {
                $query->where('company_id', $companyID)
                    ->where('confirmed_yn', 1);
            })->where('fa_master_id',$assetID)->orderby('id','desc')->first();
        if($isAssetAlreadyConfirmedToDepartment && $isAssetAlreadyConfirmedToDepartment->receivedYN == 0) {
            $dataNew = $this->getDataOfAssetConfirmed($assetID,$companyID);
            return ['success'=> false, 'message' => "Asset transferred and still not acknowledged",'data' => $dataNew];
        }

        return ['success'=> true, 'message' => "Asset Transferred successfully"];


    }

    public function getDataOfAssetConfirmed($assetID,$companyID) {
        return ERPAssetTransferDetail::with(['assetTransferMaster' => function ($query) use ($companyID) {
            $query->where('company_id', $companyID)
                ->where('confirmed_yn', 1);
        }, 'assetMaster', 'assetRequestDetail' => function ($q) {
            $q->with(['createdUserID']);
        }, 'smePayAsset'])
            ->whereHas('assetTransferMaster', function ($query) use ($companyID) {
                $query->where('company_id', $companyID)
                    ->where('confirmed_yn', 1);
            })
            ->where('fa_master_id', $assetID)
            ->where('receivedYN', 0)
            ->get();
    }

    public function getDataOfAssetNotAcknowldged($assetID,$companyID) {
        return ERPAssetTransferDetail::with(['assetTransferMaster' => function ($query) use ($companyID) {
            $query->where('company_id', $companyID)
                ->where('approved_yn', -1);
        }, 'assetMaster', 'assetRequestDetail' => function ($q) {
            $q->with(['createdUserID']);
        }, 'smePayAsset'])
            ->whereHas('assetTransferMaster', function ($query) use ($companyID) {
                $query->where('company_id', $companyID)
                    ->where('approved_yn', -1);
            })
            ->where('fa_master_id', $assetID)
            ->where('receivedYN', 0)
            ->get();
    }

    public function getDataOfAssetNotAcknowldgedByEmployee($assetID,$companyID) {
        return ERPAssetTransferDetail::with(['assetTransferMaster' => function ($query) use ($companyID) {
            $query->where('company_id', $companyID)
                ->where('approved_yn', -1);
        }, 'assetMaster', 'assetRequestDetail' => function ($q) {
            $q->with(['createdUserID']);
        }, 'smePayAsset'])
            ->whereHas('assetTransferMaster', function ($query) use ($companyID) {
                $query->where('company_id', $companyID)
                    ->where('approved_yn', -1);
            })
            ->where('fa_master_id', $assetID)
            ->where('receivedYN', 0)
            ->get();
    }

    public function getDataOfAssetNotAcknowldgedByDepartment($assetID,$companyID,$department,$receiveYn) {
        return ERPAssetTransferDetail::with(['assetTransferMaster' => function ($query) use ($companyID) {
            $query->where('company_id', $companyID)
                ->where('approved_yn', -1);
        }, 'assetMaster', 'assetRequestDetail' => function ($q) {
            $q->with(['createdUserID']);
        }, 'smePayAsset'])
            ->whereHas('assetTransferMaster', function ($query) use ($companyID) {
                $query->where('company_id', $companyID)
                    ->where('approved_yn', -1);
            })
            ->where('fa_master_id', $assetID)
            ->where('departmentSystemID', $department)
            ->where('receivedYN', $receiveYn)
            ->get();
    }

    
    public function getDataOfAssetAcknowldged($assetID,$companyID) {
        return ERPAssetTransferDetail::with(['assetTransferMaster' => function ($query) use ($companyID) {
            $query->where('company_id', $companyID)
                ->where('approved_yn', -1);
        }, 'assetMaster', 'assetRequestDetail' => function ($q) {
            $q->with(['createdUserID']);
        }, 'smePayAsset'])
            ->whereHas('assetTransferMaster', function ($query) use ($companyID) {
                $query->where('company_id', $companyID)
                    ->where('approved_yn', -1);
            })
            ->where('fa_master_id', $assetID)
            ->where('receivedYN', 1)
            ->get();
    }

    public function getEmployeesToSelectDrpdwn(Request $request) {
        $input = $request->all();
        $companyID = $input['companyID'];

        $toEmployeeList = Employee::where('discharegedYN','!=',-1)->whereHas('hr_emp', function($q){
            $q->where('isDischarged', 0)->where('empConfirmedYN', 1);
        })->get();

        $fromEmployeeList = Employee::where('empCompanySystemID',$companyID)->whereHas('hr_emp', function($q){
            $q->where('empConfirmedYN', 1);
        })->get();

        $data = [
            'to_employees' => $toEmployeeList,
            'from_employees' => $fromEmployeeList
        ];

        return $this->sendResponse($data, 'Employee data reterived successfully');

    }
}
