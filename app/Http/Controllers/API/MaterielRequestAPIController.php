<?php
/**
 * =============================================
 * -- File Name : MaterielRequestAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Materiel Request
 * -- Author : Mohamed Fayas
 * -- Create date : 12 - June 2018
 * -- Description : This file contains the all CRUD for Materiel Request
 * -- REVISION HISTORY
 * -- Date: 12-June 2018 By: Fayas Description: Added new functions named as getAllRequestByCompany(),getRequestFormData()
 * -- Date: 19-June 2018 By: Fayas Description: Added new functions named as materielRequestAudit()
 * -- Date: 13-July 2018 By: Fayas Description: Added new functions named as getAllNotApprovedRequestByUser(),getApprovedMaterielRequestsByUser()
 * -- Date: 30-July 2018 By: Fayas Description: Added new functions named as requestReopen(),printMaterielRequest()
 * -- Date: 06-December 2018 By: Fayas Description: Added new functions named as requestReferBack()
 */
namespace App\Http\Controllers\API;

use App\helper\inventory;
use App\Http\Requests\API\CreateMaterielRequestAPIRequest;
use App\Http\Requests\API\UpdateMaterielRequestAPIRequest;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyPolicyMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\Employee;
use App\Models\EmployeesDepartment;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemIssueDetails;
use App\Models\ItemMaster;
use App\Models\PurchaseRequestDetails;
use App\Models\ItemAssigned;
use App\Models\Location;
use App\Models\MaterielRequest;
use App\Models\MaterielRequestDetails;
use App\Models\Priority;
use App\Models\RequestDetailsRefferedBack;
use App\Models\RequestRefferedBack;
use App\Models\SegmentMaster;
use App\Models\PurchaseOrderDetails;
use App\Models\ErpItemLedger;
use App\Models\GRVDetails;
use App\Models\Unit;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\MaterielRequestRepository;
use App\Services\Inventory\MaterialIssueService;
use App\Services\Inventory\PullMaterialRequestFromMaterialIssueService;
use App\Traits\AuditTrial;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\helper\CancelDocument;
use App\Models\GeneralLedger;
use Response;
use App\Repositories\MaterielRequestDetailsRepository;
use Auth;
use App\Models\ItemIssueMaster;
use App\Services\ValidateDocumentAmend;
use function Clue\StreamFilter\fun;

/**
 * Class MaterielRequestController
 * @package App\Http\Controllers\API
 */

class MaterielRequestAPIController extends AppBaseController
{
    /** @var  MaterielRequestRepository */
    private $materielRequestRepository;
    private $materielRequestDetailsRepository;

    private $pullMrService;


    public function __construct(MaterielRequestRepository $materielRequestRepo, MaterielRequestDetailsRepository $materielRequestDetailsRepo, PullMaterialRequestFromMaterialIssueService $pullMrService)
    {
        $this->materielRequestRepository = $materielRequestRepo;
        $this->materielRequestDetailsRepository = $materielRequestDetailsRepo;
        $this->pullMrService = $pullMrService;

    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/materielRequests",
     *      summary="Get a listing of the MaterielRequests.",
     *      tags={"MaterielRequest"},
     *      description="Get all MaterielRequests",
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
     *                  @SWG\Items(ref="#/definitions/MaterielRequest")
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
        $this->materielRequestRepository->pushCriteria(new RequestCriteria($request));
        $this->materielRequestRepository->pushCriteria(new LimitOffsetCriteria($request));
        $materielRequests = $this->materielRequestRepository->all();
        return $this->sendResponse($materielRequests->toArray(), 'Materiel Requests retrieved successfully');
    }

    /**
     * get Request By Company.
     * POST /getAllRequestByCompany
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getAllRequestByCompany(Request $request)
    {

         $input = $request->all();
         $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'ConfirmedYN', 'approved','cancelledYN'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $serviceLineSystemID = $request['serviceLineSystemID'];
        $serviceLineSystemID = (array)$serviceLineSystemID;
        $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');
        $search = $request->input('search.value');
        $materielRequests = $this->materielRequestRepository->materialrequestsListQuery($request, $input, $search, $serviceLineSystemID);
        return \DataTables::eloquent($materielRequests)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('RequestID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * get All Not Approved Request By User
     * POST /getAllNotApprovedRequestByUser
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getAllNotApprovedRequestByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'ConfirmedYN', 'approved'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $companyId = $request['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $materielRequests = DB::table('erp_documentapproved')
            ->select(
                'employeesdepartments.approvalDeligated',
                'erp_request.*',
                'serviceline.ServiceLineDes As MRServiceLineDes',
                'warehousemaster.wareHouseDescription As MRWareHouseDescription',
                'erp_priority.priorityDescription As MRPriorityDescription',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID')
                    ->whereIn('employeesdepartments.documentSystemID', [9])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_request', function ($query) use ($companyId) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'RequestID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_request.companySystemID', $companyId)
                    ->where('erp_request.approved', 0)
                    ->where('erp_request.isFromPortal', 0)
                    ->where('erp_request.ConfirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('warehousemaster', 'location', 'warehousemaster.wareHouseSystemCode')
            ->leftJoin('serviceline', 'erp_request.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->leftJoin('erp_priority', 'erp_request.priority', 'erp_priority.priorityID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [9])
            ->where('erp_documentapproved.companySystemID', $companyId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $materielRequests = $materielRequests->where(function ($query) use ($search) {
                $query->where('RequestCode', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%");
            });
        }


        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $materielRequests = [];
        }

        $data['order'] = [];
        $data['search']['value'] = '';
        $request->merge($data);

        return \DataTables::of($materielRequests)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('RequestID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * get Approved Materiel Requests By User
     * POST /getMaterielIssueApprovedByUser
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getApprovedMaterielRequestsByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'wareHouseFrom', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();
        $mangerID = Employee::find($empID)->empID;
        $search = $request->input('search.value');
        $materielRequests = DB::table('erp_documentapproved')
            ->select(
                'erp_request.*',
                'employees.empName As created_emp',
                'serviceline.ServiceLineDes As MRServiceLineDes',
                'warehousemaster.wareHouseDescription As MRWareHouseDescription',
                'erp_priority.priorityDescription As MRPriorityDescription',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_request', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'RequestID')
                    ->where('erp_request.companySystemID', $companyId)
                    ->where('erp_request.approved', -1)
                    ->where('erp_request.ConfirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('warehousemaster', 'location', 'warehousemaster.wareHouseSystemCode')
            ->leftJoin('serviceline', 'erp_request.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->leftJoin('erp_priority', 'erp_request.priority', 'erp_priority.priorityID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [9])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('employees.empManagerAttached', $mangerID)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $materielRequests = $materielRequests->where(function ($query) use ($search) {
                $query->where('RequestCode', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%");
            });
        }
        $request->request->remove('search.value');
        return \DataTables::of($materielRequests)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('RequestID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * @param CreateMaterielRequestAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/materielRequests",
     *      summary="Store a newly created MaterielRequest in storage",
     *      tags={"MaterielRequest"},
     *      description="Store MaterielRequest",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MaterielRequest that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MaterielRequest")
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
     *                  ref="#/definitions/MaterielRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMaterielRequestAPIRequest $request)
    {
        $input = $this->convertArrayToValue($request->all());

        $employee = \Helper::getEmployeeInfo();

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $validator = \Validator::make($input, [
            // 'serviceLineSystemID' => 'required|numeric|min:1',
            // 'location' => 'required|numeric|min:1',
            'priority' => 'required|numeric|min:1',
            'comments' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $input['RequestedDate'] = now();
        $input['departmentID'] = 'IM';
        $input['departmentSystemID'] = 10;
        $input['documentSystemID'] =  9;
        $input['ConfirmedYN'] =  0;
        $input['RollLevForApp_curr'] = 1;

        $lastSerial = MaterielRequest::where('companySystemID', $input['companySystemID'])
                                        ->where('documentSystemID', $input['documentSystemID'])
                                        ->orderBy('serialNumber', 'desc')
                                        ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNumber) + 1;
        }

        $input['serialNumber'] = $lastSerialNumber;


        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $document = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();
        if ($document) {
            $input['documentID'] = $document->documentID;
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $code = str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT);
        $input['RequestCode'] = $input['companyID'] . '\\' . $input['departmentID'] . '\\' . $input['serviceLineCode'] . '\\' . $input['documentID'] . $code;

        $materielRequests = $this->materielRequestRepository->create($input);

        return $this->sendResponse($materielRequests->toArray(), 'Materiel Request saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/materielRequests/{id}",
     *      summary="Display the specified MaterielRequest",
     *      tags={"MaterielRequest"},
     *      description="Get MaterielRequest",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MaterielRequest",
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
     *                  ref="#/definitions/MaterielRequest"
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
        /** @var MaterielRequest $materielRequest */
        $materielRequest = $this->materielRequestRepository->with(['segment_by','created_by','confirmed_by','warehouse_by'])->findWithoutFail($id);

        if (empty($materielRequest)) {
            return $this->sendError('Materiel Request not found');
        }

        return $this->sendResponse($materielRequest->toArray(), 'Materiel Request retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMaterielRequestAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/materielRequests/{id}",
     *      summary="Update the specified MaterielRequest in storage",
     *      tags={"MaterielRequest"},
     *      description="Update MaterielRequest",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MaterielRequest",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MaterielRequest that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MaterielRequest")
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
     *                  ref="#/definitions/MaterielRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMaterielRequestAPIRequest $request)
    {

        $input = $request->all();
        $input = array_except($input, ['created_by', 'priority_by','warehouse_by', 'segment_by','confirmedEmpName',
                                       'ConfirmedBy','ConfirmedDate','confirmed_by','ConfirmedBySystemID']);

        $input = $this->convertArrayToValue($input);

        /** @var MaterielRequest $materielRequest */
        $materielRequest = $this->materielRequestRepository->findWithoutFail($id);

        if (empty($materielRequest)) {
            return $this->sendError('Materiel Request not found');
        }

        $employee = \Helper::getEmployeeInfo();

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;


        if($materielRequest->location != $input['location']){
            $checkWareHouseActive = WarehouseMaster::find($input['location']);
            if (empty($checkWareHouseActive)) {
                return $this->sendError('Location not found');
            }

            if($checkWareHouseActive->isActive == 0){
                return $this->sendError('Selected Location is not active please select different location',500);
            }
        }

        if ($materielRequest->ConfirmedYN == 0 && $input['ConfirmedYN'] == 1) {

            $validator = \Validator::make($input, [
                'serviceLineSystemID' => 'required|numeric|min:1',
                'location' => 'required|numeric|min:1',
                'priority' => 'required|numeric|min:1',
                'comments1' => 'required'
            ]);

            if ($validator->fails()) {
               // return $this->sendError($validator->messages(), 422);
            }


            $checkItems = MaterielRequestDetails::where('RequestID', $id)
                                                ->count();
            if ($checkItems == 0) {
                return $this->sendError('Every request should have at least one item', 500);
            }

            $checkQuantity = MaterielRequestDetails::where('RequestID', $id)
                                                    ->where('quantityRequested', '<=', 0)
                                                    ->count();

            if ($checkQuantity > 0) {
                return $this->sendError('Every Item should have at least one minimum Qty Requested', 500);
            }

            $params = array('autoID' => $id,
                'company' => $materielRequest->companySystemID,
                'document' => $materielRequest->documentSystemID,
                'segment' => $input['serviceLineSystemID'],
                'category' => 0,
                'amount' => 0
            );

            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }

        $materielRequest = $this->materielRequestRepository->update($input, $id);

        return $this->sendResponse($materielRequest->toArray(), 'MaterielRequest updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/materielRequests/{id}",
     *      summary="Remove the specified MaterielRequest from storage",
     *      tags={"MaterielRequest"},
     *      description="Delete MaterielRequest",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MaterielRequest",
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
        /** @var MaterielRequest $materielRequest */
        $materielRequest = $this->materielRequestRepository->findWithoutFail($id);

        if (empty($materielRequest)) {
            return $this->sendError('Materiel Request not found');
        }

        $materielRequest->delete();

        return $this->sendResponse($id, 'Materiel Request deleted successfully');
    }

    /**
     * get Request Form Data
     * get /getRequestFormData
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getRequestFormData(Request $request)
    {

        $input = $request->all();
        $companyId = $input['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($companyId);
        }else{
            $subCompanies = [$companyId];
        }

        $segments = SegmentMaster::whereIn("companySystemID", $subCompanies);
        $wareHouses = WarehouseMaster::whereIn('companySystemID',$subCompanies);

        if (array_key_exists('isFilter', $input)) {
            if ($input['isFilter'] != 1) {
                $segments = $segments->where('isActive', 1);
                $wareHouses = $wareHouses->where('isActive', 1);
            }
        } else {
            $segments = $segments->where('isActive', 1);
            $wareHouses = $wareHouses->where('isActive', 1);
        }

        $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 54)
                                            ->where('companySystemID', $companyId)
                                            ->first();


        $allowItemToTypePolicy = 0;
        if ($allowItemToType) {
            $allowItemToTypePolicy = $allowItemToType->isYesNO;
        }


        $allowToCreatePRfromMR = CompanyPolicyMaster::where('companyPolicyCategoryID', 58)
                                            ->where('companySystemID', $companyId)
                                            ->first();

        $allowPRfromMR = 0;
        if($allowToCreatePRfromMR) {
            $allowPRfromMR = $allowToCreatePRfromMR->isYesNO;
        }

        $segments = $segments->get();
        $wareHouses = $wareHouses->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $priorities = Priority::all();

        $locations = Location::where('is_deleted',0)->get();



        $units = Unit::all();

        $output = array('segments' => $segments,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'priorities' => $priorities,
            'locations' => $locations,
            'wareHouses' => $wareHouses,
            'allowItemToTypePolicy' => $allowItemToTypePolicy,
            'units' => $units,
            'allowPRfromMR' => $allowPRfromMR
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    /**
     * Display the specified Materiel Request Audit.
     * GET|HEAD /materielRequestAudit
     *
     * @param  int $id
     *
     * @return Response
     */
    public function materielRequestAudit(Request $request)
    {
        $id = $request->get('id');

        $materielRequest = $this->materielRequestRepository->getAudit($id);
        if (empty($materielRequest)) {
            return $this->sendError('Materiel Request not found');
        }

        return $this->sendResponse($materielRequest->toArray(), 'Materiel Request retrieved successfully');
    }

    public function printMaterielRequest(Request $request)
    {
        $id = $request->get('id');
        $materielRequest = $this->materielRequestRepository->getAudit($id);

        if (empty($materielRequest)) {
            return $this->sendError('Materiel Request not found');
        }

        $materielRequest->docRefNo = \Helper::getCompanyDocRefNo($materielRequest->companySystemID, $materielRequest->documentSystemID);

        $array = array('entity' => $materielRequest);
        $time = strtotime("now");
        $fileName = 'materiel_request' . $id . '_' . $time . '.pdf';
        $html = view('print.materiel_request', $array);
        $htmlFooter = view('print.materiel_request_footer', $array);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-L', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
        $mpdf->AddPage('L');
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->WriteHTML($html);
        return $mpdf->Output($fileName, 'I');
    }

    public function requestReopen(Request $request)
    {
        $input = $request->all();

        $requestID = $input['RequestID'];

        $materielRequest = MaterielRequest::find($requestID);
        $emails = array();
        if (empty($materielRequest)) {
            return $this->sendError('Materiel Request not found');
        }

        if ($materielRequest->RollLevForApp_curr > 1) {
            return $this->sendError('You cannot reopen this Request its already partially approved');
        }

        if ($materielRequest->approved == -1) {
            return $this->sendError('You cannot reopen this Request its already fully approved');
        }

        if ($materielRequest->ConfirmedYN == 0) {
            return $this->sendError('You cannot reopen this Request, its not confirmed');
        }

        // updating fields

        $materielRequest->ConfirmedYN = 0;
        $materielRequest->ConfirmedBySystemID = null;
        $materielRequest->ConfirmedBy = null;
        $materielRequest->confirmedEmpName = null;
        $materielRequest->ConfirmedDate = null;
        $materielRequest->RollLevForApp_curr = 1;
        $materielRequest->save();

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $materielRequest->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $materielRequest->RequestCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $materielRequest->RequestCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $materielRequest->companySystemID)
            ->where('documentSystemCode', $materielRequest->RequestID)
            ->where('documentSystemID', $materielRequest->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $materielRequest->companySystemID)
                    ->where('documentSystemID', $materielRequest->documentSystemID)
                    ->first();

                if (empty($companyDocument)) {
                    return ['success' => false, 'message' => 'Policy not found for this document'];
                }

                $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                    ->where('companySystemID', $documentApproval->companySystemID)
                    ->where('documentSystemID', $documentApproval->documentSystemID);

                if ($companyDocument['isServiceLineApproval'] == -1) {
                    $approvalList = $approvalList->where('ServiceLineSystemID', $documentApproval->serviceLineSystemID);
                }

                $approvalList = $approvalList
                    ->with(['employee'])
                    ->groupBy('employeeSystemID')
                    ->get();

                foreach ($approvalList as $da) {
                    if ($da->employee) {
                        $emails[] = array('empSystemID' => $da->employee->employeeSystemID,
                            'companySystemID' => $documentApproval->companySystemID,
                            'docSystemID' => $documentApproval->documentSystemID,
                            'alertMessage' => $subject,
                            'emailAlertMessage' => $body,
                            'docSystemCode' => $documentApproval->documentSystemCode);
                    }
                }

                $sendEmail = \Email::sendEmail($emails);
                if (!$sendEmail["success"]) {
                    return ['success' => false, 'message' => $sendEmail["message"]];
                }
            }
        }

        DocumentApproved::where('documentSystemCode', $requestID)
                                        ->where('companySystemID', $materielRequest->companySystemID)
                                        ->where('documentSystemID', $materielRequest->documentSystemID)
                                        ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($materielRequest->documentSystemID,$requestID,$input['reopenComments'],'Reopened');

        return $this->sendResponse($materielRequest->toArray(), 'Request reopened successfully');
    }

    public function createMaterialAPI(CreateMaterielRequestAPIRequest $request)
    {
        $input = $this->convertArrayToValue($request->all());

        DB::beginTransaction();
        try {

            $input['createdPcID'] = gethostname();
            $input['createdUserSystemID'] = $request->employee_id;

            $validator = \Validator::make($input, [
                // 'serviceLineSystemID' => 'required|numeric|min:1',
                // 'location' => 'required|numeric|min:1',
                'priority' => 'required|numeric|min:1',
                'comments' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $input['RequestedDate'] = now();
            $input['departmentID'] = 'IM';
            $input['departmentSystemID'] = 10;
            $input['documentSystemID'] = 9;
            $input['ConfirmedYN'] = 0;
            $input['RollLevForApp_curr'] = 1;

            $lastSerial = MaterielRequest::where('companySystemID', $input['companySystemID'])
                ->where('documentSystemID', $input['documentSystemID'])
                ->orderBy('serialNumber', 'desc')
                ->first();

            $lastSerialNumber = 1;
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNumber) + 1;
            }

            $input['serialNumber'] = $lastSerialNumber;


            $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
            if ($segment) {
                $input['serviceLineCode'] = $segment->ServiceLineCode;
            }

            $document = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();
            if ($document) {
                $input['documentID'] = $document->documentID;
            }

            $company = Company::where('companySystemID', $input['companySystemID'])->first();
            if ($company) {
                $input['companyID'] = $company->CompanyID;
            }

            $code = str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT);
            $input['RequestCode'] = $input['companyID'] . '\\' . $input['departmentID'] . '\\' . $input['serviceLineCode'] . '\\' . $input['documentID'] . $code;

            $materielRequests = $this->materielRequestRepository->create($input);
            $items = $request->items;
            $materielRequest = $materielRequests;
            $companySystemID = $input['companySystemID'];

            $errors = array();
            $insertedItems = array();
            $i = 0;
            foreach ($items as $itemMaterial) {
                 $i++;
                $input = $itemMaterial;
                $input = $this->convertArrayToValue($input);


                $allowItemToTypePolicy = false;
                $itemNotound = false;
                $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 54)
                    ->where('companySystemID', $companySystemID)
                    ->first();

                if ($allowItemToType) {
                    $allowItemToTypePolicy = true;
                }


                if ($allowItemToTypePolicy) {
                    $input['itemCode'] = isset($input['itemCode']['id']) ? $input['itemCode']['id'] : $input['itemCode'];
                }

                $item = ItemAssigned::where('itemCodeSystem', $input['itemCode'])
                    ->where('companySystemID', $companySystemID)
                    ->first();

                if (empty($item)) {
                    if (!$allowItemToTypePolicy) {
                        $errors[$i] = $input['itemCode']." - Item not found";
                        continue;
                    } else {
                        $itemNotound = true;
                    }
                }


                $input['qtyIssuedDefaultMeasure'] = 0;
                if (!$itemNotound) {


                    $input['itemCode'] = $item->itemCodeSystem;
                    $input['itemDescription'] = $item->itemDescription;
                    $input['partNumber'] = $item->secondaryItemCode;
                    $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
                    $input['itemFinanceCategorySubID'] = $item->financeCategorySub;
                    $input['unitOfMeasure'] = $item->itemUnitOfMeasure;
                    $input['unitOfMeasureIssued'] = $item->itemUnitOfMeasure;
                    $input['RequestID'] = $materielRequest['RequestID'];
                    if ($item->maximunQty) {
                        $input['maxQty'] = $item->maximunQty;
                    } else {
                        $input['maxQty'] = 0;
                    }

                    if ($item->minimumQty) {
                        $input['minQty'] = $item->minimumQty;
                    } else {
                        $input['minQty'] = 0;
                    }

                    $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
                        ->where('mainItemCategoryID', $item->financeCategoryMaster)
                        ->where('itemCategorySubID', $item->financeCategorySub)
                        ->first();

                    if (empty($financeItemCategorySubAssigned)) {
                        $errors[$i] = $input['itemCode']." - Finance Category not found";

                        continue;

                    }

                    if ($item->financeCategoryMaster == 1) {

                        $alreadyAdded = MaterielRequest::where('RequestID', $materielRequest['RequestID'])
                            ->whereHas('details', function ($query) use ($item) {
                                $query->where('itemCode', $item->itemCodeSystem);
                            })
                            ->first();

                        if ($alreadyAdded) {
                            $errors[$i]= $input['itemCode']." - Selected item is already added. Please check again";

                            continue;
                        }
                    }

                    $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                    $input['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                    $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;


                    $poQty = PurchaseOrderDetails::whereHas('order', function ($query) use ($companySystemID, $materielRequest) {
                        $query->where('companySystemID', $companySystemID)
                            ->where('poLocation', $materielRequest->location)
                            ->where('approved', -1)
                            ->where('poCancelledYN', 0);
                    })
                        ->where('itemCode', $input['itemCode'])
                        ->groupBy('erp_purchaseorderdetails.companySystemID',
                            'erp_purchaseorderdetails.itemCode')
                        ->select(
                            [
                                'erp_purchaseorderdetails.companySystemID',
                                'erp_purchaseorderdetails.itemCode',
                                'erp_purchaseorderdetails.itemPrimaryCode'
                            ]
                        )
                        ->sum('noQty');

                    $quantityInHand = ErpItemLedger::where('itemSystemCode', $input['itemCode'])
                        ->where('companySystemID', $companySystemID)
                        ->groupBy('itemSystemCode')
                        ->sum('inOutQty');

                    $grvQty = GRVDetails::whereHas('grv_master', function ($query) use ($companySystemID, $materielRequest) {
                        $query->where('companySystemID', $companySystemID)
                            ->where('grvTypeID', 2)
                            ->groupBy('erp_grvmaster.companySystemID');
                    })
                        ->where('itemCode', $input['itemCode'])
                        ->groupBy('erp_grvdetails.itemCode')
                        ->select(
                            [
                                'erp_grvdetails.companySystemID',
                                'erp_grvdetails.itemCode'
                            ])
                        ->sum('noQty');

                    $quantityOnOrder = $poQty - $grvQty;
                    $input['quantityOnOrder'] = $quantityOnOrder;
                    $input['quantityInHand'] = $quantityInHand;

           
                } else {
                    $input['RequestID'] = $materielRequest['RequestID'];
                    $input['itemDescription'] = $input['itemCode'];
                    $input['itemCode'] = $input['itemCode'];
                    $input['partNumber'] = null;
                    $input['itemFinanceCategoryID'] = null;
                    $input['itemFinanceCategorySubID'] = null;
                    $input['unitOfMeasure'] = null;
                    $input['unitOfMeasureIssued'] = null;
                    $input['maxQty'] = 0;
                    $input['minQty'] = 0;
                    $input['quantityOnOrder'] = 0;
                    $input['quantityInHand'] = 0;

                }

                $input['estimatedCost'] = 0;
                $input['quantityRequested'];

                $input['ClosedYN'] = 0;
                $input['selectedForIssue'] = 0;
                $input['comments'];
                $input['convertionMeasureVal'] = 1;

                $input['allowCreatePR'] = 0;
                $input['selectedToCreatePR'] = 0;

                $materielRequestDetails = $this->materielRequestDetailsRepository->create($input);
                if($materielRequestDetails['itemCode'] != null) {
                    array_push($insertedItems, $materielRequestDetails['itemCode']);
                }
            }
            $x = count($insertedItems);
            if($x == 0){
                return $this->sendError("No Items were added");
            }
            DB::commit();

            return $this->sendResponse($errors, 'Materiel Request saved successfully');
        }
        catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function requestReferBack(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];

        $itemRequest = $this->materielRequestRepository->find($id);
        if (empty($itemRequest)) {
            return $this->sendError('Request not found');
        }


        if ($itemRequest->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this request');
        }

        $itemRequestArray = $itemRequest->toArray();

        if(isset($itemRequestArray['material_issue'])) {
            unset($itemRequestArray['material_issue']);
        }

        $storeMRHistory = RequestRefferedBack::insert($itemRequestArray);

        $fetchDetails = MaterielRequestDetails::where('RequestID', $id)
            ->get();

        if (!empty($fetchDetails)) {
            foreach ($fetchDetails as $detail) {
                $detail['timesReferred'] = $itemRequest->timesReferred;
            }
        }

        $itemRequestDetailArray = $fetchDetails->toArray();

        $storeMRDetailHistory = RequestDetailsRefferedBack::insert($itemRequestDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $itemRequest->companySystemID)
            ->where('documentSystemID', $itemRequest->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $itemRequest->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentRefereedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $itemRequest->companySystemID)
            ->where('documentSystemID', $itemRequest->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $updateArray = ['refferedBackYN' => 0,'ConfirmedYN' => 0,'ConfirmedBySystemID' => null,
                'ConfirmedBy' => null,'confirmedEmpName' => null,'ConfirmedDate' => null,'RollLevForApp_curr' => 1];

            $this->materielRequestRepository->update($updateArray,$id);
        }

        return $this->sendResponse($itemRequest->toArray(), 'Request Amend successfully');
    }

    public function checkPurcahseRequestExist($id) {
        $materielRequest = MaterielRequest::findOrFail($id);

            if( count($materielRequest->purchase_requests) > 0) {
                $items = PurchaseRequestDetails::select('itemCode')->where('purchaseRequestID', $materielRequest->purchase_requests->first()->purchaseRequestID)
                ->pluck('itemCode')->toArray();
                $data = [
                    'status' => true,
                    'data'   => $items,
                    'puchaseId' =>  $materielRequest->purchase_requests->first()->purchaseRequestID,
                    'purchaseReq' => ($materielRequest->purchase_requests->first()->purchase_request) ? $materielRequest->purchase_requests->first()->purchase_request->purchaseRequestCode: ""
                ];
                return $this->sendResponse($data, 'Purchase request received successfully');
            }else {
                $data = [
                    'status' => false,
                    'data'   => []
                ];
                return $this->sendResponse($data, 'No Purchase request found');
            }
    }


    public function cancelMaterielRequest(Request $request) {

        $input = $request->all();

        $requestID = $input['RequestID'];


        $materielRequest = MaterielRequest::find($requestID);

        if (empty($materielRequest)) {
            return $this->sendError('Materiel Request not found');
        }

        if ($materielRequest->cancelledYN == -1) {
            return $this->sendError('You cannot cancel this request as it is already cancelled');
        }

        if(count($materielRequest->materialIssue) > 0) {
            return $this->sendError('Cannot cancel. Materiel Issue is created for this request');
        }

        if(count($materielRequest->purchase_requests) > 0) {
            return $this->sendError('Cannot cancel. Purchase Request is created for this request');
        }

        $employee = \Helper::getEmployeeInfo();


        $materielRequest->cancelledYN = -1;
        $materielRequest->cancelledByEmpSystemID = $employee->employeeSystemID;
        $materielRequest->cancelledByEmpID = $employee->empID;
        $materielRequest->cancelledByEmpName = $employee->empName;
        $materielRequest->cancelledComments = $input['cancelledComments'];
        $materielRequest->cancelledDate = now();
        $materielRequest->save();

        AuditTrial::createAuditTrial($materielRequest->documentSystemID,$input['RequestID'],$input['cancelledComments'],'cancelled');

        
        $emails = array();
        $document = DocumentMaster::where('documentSystemID', $materielRequest->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $materielRequest->RequestCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $materielRequest->RequestCode;

        $body = '<p>' . $cancelDocNameBody . ' is cancelled by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['cancelledComments'] . '</p>';
        $subject = $cancelDocNameSubject . ' is cancelled';

        if ($materielRequest->ConfirmedYN == 1) {
            $emails[] = array('empSystemID' => $materielRequest->ConfirmedBySystemID,
                'companySystemID' => $materielRequest->companySystemID,
                'docSystemID' => $materielRequest->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $materielRequest->RequestID);
        }

        $documentApproval = DocumentApproved::where('companySystemID', $materielRequest->companySystemID)
            ->where('documentSystemCode', $materielRequest->RequestID)
            ->where('documentSystemID', $materielRequest->documentSystemID)
            ->where('approvedYN', -1)
            ->get();

        foreach ($documentApproval as $da) {
            $emails[] = array('empSystemID' => $da->employeeSystemID,
                'companySystemID' => $materielRequest->companySystemID,
                'docSystemID' => $materielRequest->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $materielRequest->RequestID);
        }

        $sendEmail = \Email::sendEmail($emails);
        if (!$sendEmail["success"]) {
            return $this->sendError($sendEmail["message"], 500);
        }
        CancelDocument::sendEmail($input);

        return $this->sendResponse($materielRequest, 'Materiel Request successfully canceled');
    }


    public function updateQntyByLocation(Request $request) {
        $input = $request->all();

        $location = $input['location'];
        $requestID = $input['RequestID'];
        $companySystemID =  $input['companySystemID'];

        $materielRequest = MaterielRequest::find($requestID);

        if($location !=  $materielRequest->location) {

           $itemDetails = $materielRequest->details;
            foreach($itemDetails as $item) {

                    $poQty = PurchaseOrderDetails::whereHas('order' , function ($query) use ($companySystemID,$materielRequest,$location) {
                        $query->where('companySystemID', $companySystemID)
                            ->where('poLocation', $location)
                            ->where('approved', -1)
                            ->where('poCancelledYN', 0);
                    })
                    ->where('itemCode', $item->itemCode)
                    ->groupBy('erp_purchaseorderdetails.companySystemID',
                        'erp_purchaseorderdetails.itemCode')
                    ->select(
                        [
                            'erp_purchaseorderdetails.companySystemID',
                            'erp_purchaseorderdetails.itemCode',
                            'erp_purchaseorderdetails.itemPrimaryCode'
                        ]
                    )
                    ->sum('noQty');

                    $quantityInHand = ErpItemLedger::where('itemSystemCode', $item->itemCode)
                        ->where('companySystemID', $companySystemID)
                        ->where('wareHouseSystemCode', $location)
                        ->groupBy('itemSystemCode')
                        ->sum('inOutQty');

                    $grvQty = GRVDetails::whereHas('grv_master' , function ($query) use ($companySystemID,$item,$location) {
                    $query->where('companySystemID', $companySystemID)
                        ->where('grvTypeID', 2)
                        ->where('grvLocation', $location)
                        ->groupBy('erp_grvmaster.companySystemID');
                    })
                    ->where('itemCode', $item->itemCode)
                    ->groupBy('erp_grvdetails.itemCode')
                    ->select(
                        [
                            'erp_grvdetails.companySystemID',
                            'erp_grvdetails.itemCode'
                        ])
                    ->sum('noQty');

                    $quantityOnOrder = $poQty - $grvQty;
                    $item['quantityOnOrder'] = $quantityOnOrder;
                    $item['quantityInHand']  = $quantityInHand;
                    $item->save();
            }

            $materielRequest->location = $location;
            $materielRequest->save();
        }

        return $this->sendResponse($materielRequest,'Materiel Details Updated!');

    }


    public function getMaterielRequestDetails($id) {

        $materielRequest = MaterielRequest::find($id);

        if($materielRequest->details) {
            $details = $materielRequest->details;
            foreach($details as $detail) {
                $detail['itemPrimaryCode']= ($detail->item_by) ? $detail->item_by->primaryItemCode : null;
            }
            return $this->sendResponse($details,'Materiel Details Received!');
        }else {
            return $this->sendResponse([],'Materiel Not Received!');
        }
        
    }

    public function returnMaterialRequestPreCheck(Request $request)
    {
        $input = $request->all();
            
        $materialRequest = MaterielRequest::with(['confirmed_by'])->find($input['materialRequestID']);

        if (empty($materialRequest)) {
            return $this->sendError('Purchase Request not found');
        }
  

        if ($materialRequest->ClosedYN == 1) {
            return $this->sendError('You cannot revert back this request as it is closed manually');
        }

        $checkMr = ItemIssueMaster::where('reqDocID', $input['materialRequestID']);


        if ($checkMr->count() > 0) {

            return $this->sendError('Cannot return back to amend.The Material Request linked with following Material Issues', 500, ['data' => $checkMr->pluck('itemIssueCode')]);
        }

        return $this->sendResponse($materialRequest, 'Purchase Request successfully return back to amend');
    }

    public function returnMaterialRequest(Request $request)
    {
      
        $input = $request->all();
            
        $materialRequest = MaterielRequest::with(['confirmed_by'])->find($input['RequestID']);

        if (empty($materialRequest)) {
            return $this->sendError('Purchase Request not found');
        }
  

        if ($materialRequest->ClosedYN == 1) {
            return $this->sendError('You cannot revert back this request as it is closed manually');
        }

        $checkMr = ItemIssueMaster::where('reqDocID', $input['RequestID'])->count();
        if ($checkMr > 0) {
            return $this->sendError('Cannot return back to amend. Itemissue is created for this request');
        }


        $employee = \Helper::getEmployeeInfo();

        $emails = array();
        $ids_to_delete = array();

      

        $document = DocumentMaster::where('documentSystemID', $materialRequest->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $materialRequest->RequestCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $materialRequest->RequestCode;
       
        $body = '<p>' . $cancelDocNameBody . ' is return back to amend by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['ammendComments'] . '</p>';
        $subject = $cancelDocNameSubject . ' is return back to amend';



        if ($materialRequest->ConfirmedYN == 1) {
            $emails[] = array('empSystemID' => $materialRequest->ConfirmedBySystemID,
                'companySystemID' => $materialRequest->companySystemID,
                'docSystemID' => $materialRequest->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $materialRequest->RequestID);
        }

        $materialRequest->ConfirmedYN = 0;
        $materialRequest->ConfirmedBy = NULL;
        $materialRequest->confirmedEmpName = NULL;
        $materialRequest->ConfirmedBySystemID = NULL;
        $materialRequest->ConfirmedDate = NULL;
        $materialRequest->approved = 0;
        $materialRequest->approvedDate = NULL;
        $materialRequest->approvedByUserSystemID = NULL;
        $materialRequest->RollLevForApp_curr = 1;
        $materialRequest->save();

        AuditTrial::createAuditTrial($materialRequest->documentSystemID,$input['RequestID'],$input['ammendComments'],'returned back to amend');




        $documentApproval = DocumentApproved::where('companySystemID', $materialRequest->companySystemID)
            ->where('documentSystemCode', $materialRequest->RequestID)
            ->where('documentSystemID', $materialRequest->documentSystemID)
            ->get();




        foreach ($documentApproval as $da) {

            if ($da->approvedYN == -1) {
                $emails[] = array('empSystemID' => $da->employeeSystemID,
                    'companySystemID' => $materialRequest->companySystemID,
                    'docSystemID' => $materialRequest->documentSystemID,
                    'alertMessage' => $subject,
                    'emailAlertMessage' => $body,
                    'docSystemCode' => $materialRequest->RequestID);
            }

            array_push($ids_to_delete, $da->documentApprovedID);
        }

        $sendEmail = \Email::sendEmail($emails);
        if (!$sendEmail["success"]) {
            return $this->sendError($sendEmail["message"], 500);
        }

        DocumentApproved::destroy($ids_to_delete);

        return $this->sendResponse($materialRequest, 'Purchase Request successfully return back to amend');
    }

    public function getMaterialRequestRequestCodes(Request $request)
    {
        $input = $request->all();

        $origin = $input['origin'];

        switch ($origin)
        {
            case "materiel-issue":

                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'wareHouseFrom'));

                $selectedCompanyId = $input['companyId'];
                $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);
                if ($isGroup) {
                    $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
                } else {
                    $subCompanies = [$selectedCompanyId];
                }

                $confirmYn= 0;
                if(isset($input['id']))
                    $materialIssue = ItemIssueMaster::select('confirmedYN')->where('itemIssueAutoID',$input['id'])->first();
                $confirmYn = $materialIssue->confirmedYN;

                $search = isset($input['search']) ? $input['search'] : null;

                $materielRequests = MaterielRequest::whereIn('companySystemID', $subCompanies)
                    ->where("approved", -1)
                    ->where("cancelledYN", 0)
                    ->where("serviceLineSystemID", $input['serviceLineSystemID']);

                if ($search) {
                    $search = str_replace("\\", "\\\\", $search);
                    $materielRequests = $materielRequests->where(function ($query) use ($search) {
                        $query->where('itemIssueCode', 'LIKE', "%{$search}%")
                            ->orWhere('comment', 'LIKE', "%{$search}%");
                    });
                }

                $materielRequests = $materielRequests->get(['RequestID', 'RequestCode']);
                return $materielRequests;
                break;
        }

        return [];
    }

    public function getMaterialRequestDetails(Request $request) {
       $input = $request->all();

       $origin = $input['origin'];

       switch ($origin)
       {
           case "materiel-issue":
             return $this->pullMrFromMi($input);
             break;
       }

       return [];

    }

    public function pullMrFromMi($input)
    {

        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'wareHouseFrom'));

        return $this->pullMrService->getMaterialRequest($input);

    }



    public function getItemsToLink(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companyId'];
        $search = $input['search'];
        switch ($input['origin'])
        {
            case "material-issue" :
                $itemMastersQuery = ItemMaster::where('primaryCompanySystemID',$companyId)->where('isActive',1)->where('itemApprovedYN',1)->where('financeCategoryMaster',1)
                    ->whereHas('itemAssigned', function($q) use ($companyId) {
                        $q->where('isActive',1)->where('isAssigned',-1)->where('companySystemID',$companyId);
                    });


                if($search) {
                    $itemMastersQuery
                        ->orWhere('itemDescription', 'LIKE', "%{$search}%")
                        ->orWhere('primaryCode', 'LIKE', "%{$search}%");
                }
                    $itemMasters = $itemMastersQuery->limit(100)->get();

                return $this->sendResponse($itemMasters->toArray(), 'Data retrieved successfully');
                break;
            default :
                return [];
                break;
        }
    }

    public function getLinkedItemsDetails(Request $request)
    {
        $input = $request->all();

        if(!isset($input['itemIssueAutoId']))
            return $this->sendError('Materiel Issue id not found');

        if(!isset($input['itemSystemCode']))
            return $this->sendError('Item id not found to link');

        if(!isset($input['companyId']))
            return $this->sendError('Company Id not found');

        $itemIssueMaster =  ItemIssueMaster::where('itemIssueAutoID',$input['itemIssueAutoId'])->first();

        $data = array('companySystemID' => $input['companyId'],
            'itemCodeSystem' => $input['itemSystemCode'],
            'wareHouseId' =>  $itemIssueMaster->wareHouseFrom);
        $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);
        $itemCurrentCostAndQty['originalItem'] = ItemMaster::where('itemCodeSystem',$input['itemSystemCode'])->first();
        if(!$itemCurrentCostAndQty)
            return $this->sendError('Item details not found');

        return $this->sendResponse($itemCurrentCostAndQty,"Details reterived successfully!");

    }

    
}
