<?php
/**
 * =============================================
 * -- File Name : SegmentMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name : Segment Master
 * -- Author : Mohamed Nazir
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Segment Master
 * -- REVISION HISTORY
 * -- Date: 15-March 2018 By: Nazir Description: Added new functions named as getAllSegmentMaster()
 * -- Date: 16-March 2018 By: Nazir Description: Added new functions named as getSegmentMasterFormData()
 * -- Date: 05-June 2018 By: Mubashir Description: Modified getAllSegmentMaster() to handle filters from local storage
 **/
namespace App\Http\Controllers\API;

use App\helper\CreateExcel;
use App\helper\Helper;
use App\helper\ReopenDocument;
use App\Http\Requests\API\CreateSegmentMasterAPIRequest;
use App\Http\Requests\API\UpdateSegmentMasterAPIRequest;
use App\Models\BookInvSuppMaster;
use App\Models\CreditNote;
use App\Models\CreditNoteDetails;
use App\Models\CustomerInvoice;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\DebitNote;
use App\Models\DebitNoteDetails;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use App\Models\DirectInvoiceDetails;
use App\Models\DirectPaymentDetails;
use App\Models\DirectReceiptDetail;
use App\Models\DocumentApproved;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeeDetails;
use App\Models\GRVMaster;
use App\Models\ItemIssueMaster;
use App\Models\ItemMaster;
use App\Models\JvDetail;
use App\Models\MaterielRequest;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\PurchaseReturn;
use App\Models\QuotationMaster;
use App\Models\SegmentAssigned;
use App\Models\SegmentMaster;
use App\Models\ProcumentOrder;
use App\Models\GeneralLedger;
use App\Models\PurchaseRequest;
use App\Models\Company;
use App\Models\ServiceLine;
use App\Models\SrpEmployeeDetails;
use App\Models\StockAdjustment;
use App\Models\StockReceive;
use App\Models\StockTransfer;
use App\Models\YesNoSelection;
use App\Repositories\SegmentMasterRepository;
use App\Services\UserTypeService;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\ErpItemLedger;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use App\Traits\AuditLogsTrait;

/**
 * Class SegmentMasterController
 * @package App\Http\Controllers\API
 */

class SegmentMasterAPIController extends AppBaseController
{
    /** @var  SegmentMasterRepository */
    private $segmentMasterRepository;
    private $userRepository;
    use AuditLogsTrait;

    public function __construct(SegmentMasterRepository $segmentMasterRepo, UserRepository $userRepo)
    {
        $this->segmentMasterRepository = $segmentMasterRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the SegmentMaster.
     * GET|HEAD /segmentMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->segmentMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->segmentMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $segmentMasters = $this->segmentMasterRepository->all();

        return $this->sendResponse($segmentMasters->toArray(), 'Segment Masters retrieved successfully');
    }

    /**
     * Store a newly created SegmentMaster in storage.
     * POST /segmentMasters
     *
     * @param CreateSegmentMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSegmentMasterAPIRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $input['masterID'] = is_array($input['masterID']) ? (int) $input['masterID'][0] : (int) $input['masterID'];

            if(isset($input['companySystemID']))
            {
                $input['companyID'] = $this->getCompanyById($input['companySystemID']);
            }

            if (isset($input['isPublic']) && $input['isPublic']){
                $companyPublicCheck = SegmentMaster::where('companySystemID', $input['companySystemID'])
                                                    ->where('isPublic', 1)
                                                    ->where('isDeleted',0)
                                                    ->first();

                if ($companyPublicCheck) {
                    return $this->sendError(['ServiceLineCode' => ["Public segment is configured already! (" . $companyPublicCheck->ServiceLineCode. " - " . $companyPublicCheck->ServiceLineDes. ") "]], 422);
                }

            }
            
            $segmentCodeCheck = SegmentMaster::withoutGlobalScope('final_level')
                                            ->where('ServiceLineCode', $input['ServiceLineCode'])
                                            ->where('isDeleted',0)
                                            ->first();

            if ($segmentCodeCheck) {
            return $this->sendError(['ServiceLineCode' => ["Segment code already exists"]], 422);
            }

            $id = Auth::id();
            $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

            $empId = $user->employee['empID'];
            $input['createdPcID'] = gethostname();
            $input['createdUserID'] = $empId;
            $input['createdUserSystemID'] = Helper::getEmployeeSystemID();

            $input['serviceLineMasterCode'] =  $input['ServiceLineCode'];
            $input['documentSystemID'] =  132;
            $input['masterID'] = $input['masterID'] == 0 ? null : $input['masterID'];
            $input['modifiedPc'] = gethostname();
            $input['modifiedUser'] = $empId;
            $input['modifiedUserSystemID'] = Helper::getEmployeeSystemID();

            $segmentMasters = $this->segmentMasterRepository->create($input);

            if(isset($input['confirmed_yn']) && $input['confirmed_yn'] == 1) {
                $params = array('autoID' => $segmentMasters->serviceLineSystemID, 'company' => $input["companySystemID"], 'document' => 132);
                $confirm = \Helper::confirmDocument($params);
                if (!$confirm["success"]) {
                    return $this->sendError($confirm["message"], 500);
                }

                $data['confirmed_by_emp_system_id'] = $user && $user->employee ? $user->employee['employeeSystemID'] : null;
                $data['confirmed_by_emp_id'] = $empId;
                $data['confirmed_by_name'] = $user && $user->employee ? $user->employee['empName'] : null;
                $data['confirmed_date'] = now();
                $data['confirmed_yn'] = 1;

                $segmentMaster = SegmentMaster::withoutGlobalScope('final_level')
                                        ->where('serviceLineSystemID', $segmentMasters->serviceLineSystemID)
                                        ->update($data);
            }
            DB::commit();
            return $this->sendResponse($segmentMasters->toArray(), 'Segment Master saved successfully');
        } catch (\Exception $e) {
        DB::rollBack(); 
        return $this->sendError($e->getMessage(), 500);
    }
    }

    /**
     * Display the specified SegmentMaster.
     * GET|HEAD /segmentMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */

    public function show($id)
    {
        /** @var SegmentMaster $segmentMaster */
        $segmentMaster = $this->segmentMasterRepository->withoutGlobalScope('final_level')
            ->with(['approved_by_emp','company','parent'])
            ->withcount(['sub_levels'])->find($id);

        if (empty($segmentMaster)) {
            return $this->sendError('Segment Master not found');
        }

        return $this->sendResponse($segmentMaster->toArray(), 'Segment Master retrieved successfully');
    }

    /**
     * Update the specified SegmentMaster in storage.
     * PUT/PATCH /segmentMasters/{id}
     *
     * @param  int $id
     * @param UpdateSegmentMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSegmentMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var SegmentMaster $segmentMaster */
        $segmentMaster = $this->segmentMasterRepository->findWithoutFail($id);

        if (empty($segmentMaster)) {
            return $this->sendError('Segment Master not found');
        }

        $userId = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($userId);
        $empId = $user->employee['empID'];
        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $empId;

        $segmentMaster = $this->segmentMasterRepository->update($input, $id);

        return $this->sendResponse($segmentMaster->toArray(), 'SegmentMaster updated successfully');
    }

    /**
     * Remove the specified SegmentMaster from storage.
     * DELETE /segmentMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id, Request $request)
    {
        $input = $request->all();
        
        /** @var SegmentMaster $segmentMaster */
        $segmentMaster = $this->segmentMasterRepository->withoutGlobalScope('final_level')->with(['sub_levels'])->find($id);
        $previousValue = $segmentMaster ? $segmentMaster->toArray() : [];

        if (empty($segmentMaster)) {
            return $this->sendError('Segment Master not found');
        }

        if ($segmentMaster->isPublic){
            return $this->sendError('Cannot delete this segment. This segment is a public segment.');
        }

        //delete validation 
        $segmentUsed = false;
        $segmentUsedByDocs = false;
        $segmentUsedByEmp = false;


        $checkGeneralLedger = GeneralLedger::where('serviceLineSystemID', $id)
                                 ->first();

        if ($checkGeneralLedger) {
            $segmentUsed = true;
        }

        $checkItemLedger = ErpItemLedger::where('serviceLineSystemID', $id)
        ->first();

        if ($checkItemLedger) {
            $segmentUsed = true;
        }

        $company = Company::find($segmentMaster->companySystemID);
        if ($company && $company->isHrmsIntergrated) {
            $checkEmployeeDetails = SrpEmployeeDetails::where('segmentID', $id)
                                 ->first();

            if ($checkEmployeeDetails) {
                $segmentUsedByEmp = true;
            }
        }


        $isSubSegments = ServiceLine::where('masterID', $id)->where('isDeleted', 0)->first();

        if (!empty($isSubSegments)) {
            return $this->sendError("This segment ". $segmentMaster->ServiceLineDes . " cannot be deleted. There are child segments associated with this", 500);
        }

         $isDocs = $this->affectedDocumentsBySegment($id);


        if (!empty($isDocs)) {
            $segmentUsedByDocs = true;
        }

        if ($segmentUsedByDocs) {
            return $this->sendError("Cannot delete this segment", 500,[1, $segmentMaster->ServiceLineDes]);
        }


        if ($segmentUsedByEmp) {
            return $this->sendError("Cannot delete this segment", 500,[2, $segmentMaster->ServiceLineDes]);
        }

        if ($segmentUsed) {
            return $this->sendError("This segment is used in some documents. Therefore, cannot delete", 500);
        }



        DB::beginTransaction();
        try {
            if (sizeof($segmentMaster->sub_levels) > 0) {
                $this->deleteSubLevels($segmentMaster->sub_levels);
            }

            $segmentMaster->isDeleted = 1;
            $segmentMaster->save();

            $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid'] : 'local';
            $db = isset($input['db']) ? $input['db'] : '';

            $this->auditLog($db, $id,$uuid, "serviceline", "Segment master ".$segmentMaster->ServiceLineDes." has been deleted", "D", [], $previousValue);

            DB::commit();
            return $this->sendResponse($id, 'Segment Master deleted successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Error occred while deleting segments'.$e->getMessage());
        }
    }

    public function getAffectedDocuments(Request $request){
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $segmentId = $request->segmentId;

        $controlData = $this->affectedDocumentsBySegment($segmentId);

        return \DataTables::collection($controlData)
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function getAssignedEmployees(Request $request){
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $segmentId = $request->segmentId;

        $controlData = $this->assignedEmployeesBySegment($segmentId);

        return \DataTables::collection($controlData)
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function assignedEmployeesBySegment($segmentId){

        $assignedEmployees = SrpEmployeeDetails::where('segmentID', $segmentId)->get();

        return $assignedEmployees;
    }

    public function affectedDocumentsBySegment($segmentId){

        $controlData = [];

        $jvs = JvDetail::with(['master'])->where('serviceLineSystemID', $segmentId)
            ->whereHas('master', function ($q) {
                $q->where('confirmedYN', 1);
            })->get();

        foreach ($jvs as $jv) {
            $controlData[] = [
                'documentCode' => $jv->master->JVcode,
            ];
        }

        $prs = PurchaseRequest::where('PRConfirmedYN', 1)->where('serviceLineSystemID', $segmentId)->get();

        foreach ($prs as $pr) {
            $controlData[] = [
                'documentCode' => $pr->purchaseRequestCode,
            ];
        }

        $pos = ProcumentOrder::where('poConfirmedYN', 1)->where('serviceLineSystemID', $segmentId)->get();

        foreach ($pos as $po) {
            $controlData[] = [
                'documentCode' => $po->purchaseOrderCode,
            ];
        }


        $grvs = GRVMaster::where('grvConfirmedYN', 1)->where('serviceLineSystemID', $segmentId)->get();

        foreach ($grvs as $grv) {
            $controlData[] = [
                'documentCode' => $grv->grvPrimaryCode,
            ];
        }

        $sis = BookInvSuppMaster::where('serviceLineSystemID', $segmentId)->where('confirmedYN', 1)->get();

        foreach ($sis as $si){
            $controlData[] = [
                'documentCode' => $si->bookingInvCode,
            ];
        }

        $dis = DirectInvoiceDetails::with(['supplier_invoice_master'])->where('serviceLineSystemID', $segmentId)
            ->whereHas('supplier_invoice_master', function ($q) {
                $q->where('confirmedYN', 1);
            })->get();

        foreach ($dis as $di) {
            $controlData[] = [
                'documentCode' => $di->supplier_invoice_master->bookingInvCode,
            ];
        }

        $dpvs = DirectPaymentDetails::with(['master'])->where('serviceLineSystemID', $segmentId)
            ->whereHas('master', function ($q) {
                $q->where('confirmedYN', 1);
            })->get();

        foreach ($dpvs as $dpv) {
            $controlData[] = [
                'documentCode' => $dpv->master->BPVcode,
            ];
        }

        $dns = DebitNoteDetails::with(['master'])->where('serviceLineSystemID', $segmentId)
            ->whereHas('master', function ($q) {
                $q->where('confirmedYN', 1);
            })->get();

        foreach ($dns as $dn) {
            $controlData[] = [
                'documentCode' => $dn->master->debitNoteCode,
            ];
        }

        $qts = QuotationMaster::where('confirmedYN', 1)->where('serviceLineSystemID', $segmentId)->get();

        foreach ($qts as $qt) {
            $controlData[] = [
                'documentCode' => $qt->quotationCode,
            ];
        }

        $dos = DeliveryOrder::where('confirmedYN', 1)->where('serviceLineSystemID', $segmentId)->get();


        foreach ($dos as $do) {
            $controlData[] = [
                'documentCode' => $do->deliveryOrderCode,
            ];
        }

        $cis = CustomerInvoiceDirectDetail::with(['master'])->where('serviceLineSystemID', $segmentId)
            ->whereHas('master', function ($q) {
                $q->where('confirmedYN', 1);
            })->get();

        foreach ($cis as $ci) {
            $controlData[] = [
                'documentCode' => $ci->master->bookingInvCode,
            ];
        }

        $cims = CustomerInvoice::where('serviceLineSystemID', $segmentId)->where('confirmedYN', 1)->get();

        foreach ($cims as $cim){
            $controlData[] = [
                'documentCode' => $cim->bookingInvCode,
            ];
        }

        $dvs = DirectReceiptDetail::with(['master'])->where('serviceLineSystemID', $segmentId)
            ->whereHas('master', function ($q) {
                $q->where('confirmedYN', 1);
            })->get();

        foreach ($dvs as $dv) {
            $controlData[] = [
                'documentCode' => $dv->master->custPaymentReceiveCode,
            ];
        }

        $cns = CreditNoteDetails::with(['master'])->where('serviceLineSystemID', $segmentId)
            ->whereHas('master', function ($q) {
                $q->where('confirmedYN', 1);
            })->get();

        foreach ($cns as $cn) {
            $controlData[] = [
                'documentCode' => $cn->master->creditNoteCode,
            ];
        }

        $mis = ItemIssueMaster::where('serviceLineSystemID', $segmentId)->where('confirmedYN', 1)->get();

        foreach ($mis as $mi){
            $controlData[] = [
                'documentCode' => $mi->itemIssueCode,
            ];
        }

        $mrts = MaterielRequest::where('serviceLineSystemID', $segmentId)->where('confirmedYN', 1)->get();

        foreach ($mrts as $mrt){
            $controlData[] = [
                'documentCode' => $mrt->RequestCode,
            ];
        }

        $sts = StockTransfer::where('serviceLineSystemID', $segmentId)->where('confirmedYN', 1)->get();

        foreach ($sts as $st){
            $controlData[] = [
                'documentCode' => $st->stockTransferCode,
            ];
        }

        $srs = StockReceive::where('serviceLineSystemID', $segmentId)->where('confirmedYN', 1)->get();

        foreach ($srs as $sr){
            $controlData[] = [
                'documentCode' => $sr->stockReceiveCode,
            ];
        }

        $sas = StockAdjustment::where('serviceLineSystemID', $segmentId)->where('confirmedYN', 1)->get();

        foreach ($sas as $sa){
            $controlData[] = [
                'documentCode' => $sa->stockAdjustmentCode,
            ];
        }

        $prs = PurchaseReturn::where('serviceLineSystemID', $segmentId)->where('confirmedYN', 1)->get();

        foreach ($prs as $pr){
            $controlData[] = [
                'documentCode' => $pr->purchaseReturnCode,
            ];
        }

        return $controlData;
    }

    public function exportProcessedSegments(Request $request){
        $type = $request->type;
        $segmentId = $request->segmentId;
        $output = $this->affectedDocumentsBySegment($segmentId);
        if ($output) {
            $x = 0;
            foreach ($output as $val) {
                $data[$x]['#'] = $x + 1;
                $data[$x]['Document Code'] = $val['documentCode'];
                $x++;

            }
        }
        else {
            $data = array();
        }
        $companyMaster = Company::find(isset($request->companySystemID)?$request->companySystemID:null);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $detail_array = array(
            'company_code'=>$companyCode,
        );
        $doc_name = 'assigned_employees';
        $path = 'segments/processed_documents/excel/';
        $basePath = CreateExcel::process($data,$type,$doc_name,$path,$detail_array);

        if($basePath == '')
        {
            return $this->sendError('Unable to export excel');
        }
        else
        {
            return $this->sendResponse($basePath, trans('custom.success_export'));
        }
    }
    public function exportAssignedEmp(Request $request){

        $type = $request->type;
        $segmentId = $request->segmentId;
        $output = $this->assignedEmployeesBySegment($segmentId);
        if ($output) {
            $x = 0;
            foreach ($output as $val) {
                $data[$x]['#'] = $x + 1;
                $data[$x]['Employee Code'] = $val->ECode;
                $x++;

            }
        }
        else {
            $data = array();
        }
        $companyMaster = Company::find(isset($request->companySystemID)?$request->companySystemID:null);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $detail_array = array(
            'company_code'=>$companyCode,
        );
        $doc_name = 'assigned_employees';
        $path = 'segments/assigned_employees/excel/';
        $basePath = CreateExcel::process($data,$type,$doc_name,$path,$detail_array);

        if($basePath == '')
        {
            return $this->sendError('Unable to export excel');
        }
        else
        {
            return $this->sendResponse($basePath, trans('custom.success_export'));
        }
    }

    public function deleteSubLevels($sub_levels)
    {
        foreach ($sub_levels as $key => $value) {
            if (sizeof($value->sub_levels) > 0) {
                $this->deleteSubLevels($value->sub_levels);
            }

            $segmentMaster = $this->segmentMasterRepository->withoutGlobalScope('final_level')->with(['sub_levels'])->find($value->serviceLineSystemID);

            if (empty($segmentMaster)) {
                return $this->sendError('Segment Master not found');
            }

            $segmentMaster->isDeleted = 1;
            $segmentMaster->save();
        }
    }

    /**
     * Loading data table using below query
     */

    public function getAllSegmentMaster(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input,array('companyId'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $segmentId = $input['serviceLineSystemID'];
        $isActive = $input['isActive'];
        $approvalStatus = $input['approved_yn'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if($isGroup){
            $childCompanies = \Helper::getGroupCompany($companyId);
        }else{
            $childCompanies = [$companyId];
        }

        $segmentMasters = SegmentMaster::withoutGlobalScope('final_level')
                                ->whereIn('companySystemID',$childCompanies)
                                ->with(['company']);

        if(isset($segmentId) && $segmentId !== null && $segmentId != 0)  {
            $allChild = SegmentMaster::getAllChildSegmentIds($segmentId);
            $allSegmentIds = array_merge([$segmentId], $allChild);

            $segmentMasters->whereIn('serviceLineSystemID', $allSegmentIds);
        }

        if(isset($isActive) && !is_null($isActive)) {
            $segmentMasters->where('isActive', $isActive);
        }

        if(isset($approvalStatus) && !is_null($approvalStatus)) {
            if ($approvalStatus == 2) {
                $segmentMasters->where('approved_yn', 1);
            } 
            else if ($approvalStatus == 1)
            {
                $segmentMasters->where('confirmed_yn', $approvalStatus)
                    ->where('approved_yn', 0)
                    ->where('refferedBackYN', 0);
            }
            else
            {
                $segmentMasters->where('confirmed_yn', $approvalStatus);
            }
        }

        $search = $request->input('search.value');
        if($search){
            $search = str_replace("\\", "\\\\", $search);
            $segmentMasters =   $segmentMasters->where(function ($query) use($search) {
                $query->where('ServiceLineCode','LIKE',"%{$search}%")
                    ->orWhere('ServiceLineDes', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($segmentMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('serviceLineSystemID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function getSegmentMasterFormData(Request $request)
    {

        $selectedCompanyId = $request['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }

        $allCompanies = Company::whereIn("companySystemID",$subCompanies)
            ->select('companySystemID', 'CompanyID', 'CompanyName')
            ->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();
        $yesNoSelectionMaster = YesNoSelection::all();

        $output = array(
            'allCompanies' => $allCompanies,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionMaster' => $yesNoSelectionMaster
        );

        return $this->sendResponse($output, 'Record retrieved successfully');

    }

    /**
     * Get company by id
     * @param $companySystemID
     * @return mixed
     */
    private function getCompanyById($companySystemID)
    {
        $company = Company::select('CompanyID')->where("companySystemID",$companySystemID)->first();

        return $company->CompanyID;
    }

    /**
     * Update segment master
     */

    public function updateSegmentMaster(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $segmentMaster = $this->segmentMasterRepository->withoutGlobalScope('final_level')->find($input['serviceLineSystemID']);
        $companySystemId = $input['companySystemID'];

        if (!$segmentMaster) {
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                return [
                    'status' => false,
                    'message' => 'Segment not found'
                ];
            }
            else {
                return $this->sendError("Segment not found", 500);
            }
        }

        $previousValue = $segmentMaster->toArray();

        if(isset($input['companySystemID']))
        {
            $input['companyID'] = $this->getCompanyById($input['companySystemID']);
        }

        if (is_array($input['companySystemID']))
            $input['companySystemID'] = $input['companySystemID'][0];

        if (is_array($input['isActive']))
            $input['isActive'] = $input['isActive'][0];

        if (is_array($input['isMaster']))
            $input['isMaster'] = $input['isMaster'][0];


        $checkForDuplicateCode = $this->segmentMasterRepository->withoutGlobalScope('final_level')
                                                               ->where('serviceLineSystemID', '!=', $input['serviceLineSystemID'])
                                                               ->where('ServiceLineCode', $input['ServiceLineCode'])
                                                               ->where('isDeleted', 0)
                                                               ->first();
        if ($checkForDuplicateCode) {
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                return [
                    'status' => false,
                    'message' => 'Segment code already exists'
                ];
            }
            else {
                return $this->sendError("Segment code already exists", 500);
            }
        }


        $segmentUsed = false;
        if ($segmentMaster->isFinalLevel != $input['isFinalLevel']) {
            //validate
            $procumentOrderCheck = ProcumentOrder::where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                 ->first();

            if ($procumentOrderCheck) {
                $segmentUsed = true;
            }

            $checkPR = PurchaseRequest::where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                 ->first();

            if ($checkPR) {
                $segmentUsed = true;
            }

            $checkGeneralLedger = GeneralLedger::where('serviceLineSystemID', $input['serviceLineSystemID'])
                                     ->first();

            if ($checkGeneralLedger) {
                $segmentUsed = true;
            }

            $company = Company::find($segmentMaster->companySystemID);
            if ($company && $company->isHrmsIntergrated) {
                $checkEmployeeDetails = SrpEmployeeDetails::where('segmentID', $input['serviceLineSystemID'])
                                     ->first();

                if ($checkEmployeeDetails) {
                    $segmentUsed = true;
                }
            }
            
            if (SegmentMaster::isSegmentUsedInDepartment($input['serviceLineSystemID'])) {
                return $this->sendError('Cannot change final level — segment is already used in departments', 500);
            }

            if ($segmentUsed) {
                if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                    return [
                        'status' => false,
                        'message' => 'This segment is used in some documents. Therefore, Final level status cannot be changed'
                    ];
                }
                else {
                    return $this->sendError("This segment is used in some documents. Therefore, Final level status cannot be changed", 500);
                }
            }

            if($input['sub_levels_count'] > 0) {
                return $this->sendError("Parent type cannot be changed, as it has sub levels", 500);
            }
        }

        if (isset($input['isPublic']) && $input['isPublic']){
            $companyPublicCheck = SegmentMaster::where('companySystemID', $input['companySystemID'])
                                                ->where('isPublic', 1)
                                                ->where('isDeleted',0)
                                                ->first();

            if ($companyPublicCheck) {
                if($companyPublicCheck->serviceLineSystemID != $input['serviceLineSystemID']){
                    if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                        return [
                            'status' => false,
                            'message' => "Public segment is configured already! (" . $companyPublicCheck->ServiceLineCode. " - " . $companyPublicCheck->ServiceLineDes. ") "
                        ];
                    }
                    else {
                        return $this->sendError("Public segment is configured already! (" . $companyPublicCheck->ServiceLineCode. " - " . $companyPublicCheck->ServiceLineDes. ") ", 500);
                    }
                }
            }

        }

        $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid'] : 'local';
        $db = isset($input['db']) ? $input['db'] : '';

        unset($input['companySystemID']);

        if(array_key_exists('tenant_uuid', $input)){
            unset($input['tenant_uuid']);
        }

        if(array_key_exists('db', $input)){
            unset($input['db']);
        }

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            $empInfo = UserTypeService::getSystemEmployee();
            $input['modifiedUser'] = $empInfo->empID;
            $input['modifiedUserSystemID'] = $empInfo->employeeSystemID;
        }
        else {
            $userId = Auth::id();
            $user = $this->userRepository->with(['employee'])->findWithoutFail($userId);
            $empId = $user->employee['empID'];
            $input['modifiedUser'] = $empId;
            $input['modifiedUserSystemID'] = Helper::getEmployeeSystemID();
        }

        $input['modifiedPc'] = gethostname();
        $input['documentSystemID'] =  132;

        $input['timeStamp'] = now();

        if(isset($input['confirmed_yn']) && $input['confirmed_yn'] == 1 && ($segmentMaster->confirmed_yn != $input['confirmed_yn']) && (isset($input['approved_yn']) && $input['approved_yn'] != 1)) {
            $params = array(
                'autoID' => $input['serviceLineSystemID'],
                'company' => $companySystemId,
                'document' => 132,
                'isAutoCreateDocument' => isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']
            );
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                    return [
                        'status' => false,
                        'message' => $confirm["message"]
                    ];
                }
                else {
                    return $this->sendError($confirm["message"], 500);
                }
            }

            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                $empInfo = UserTypeService::getSystemEmployee();
                $input['confirmed_by_emp_id'] = $empInfo->empID;
                $input['confirmed_by_emp_system_id'] = $empInfo->employeeSystemID;
                $input['confirmed_by_name'] = $empInfo->empName;
            }
            else {
                $input['confirmed_by_emp_system_id'] = $user->employee['employeeSystemID'];
                $input['confirmed_by_emp_id'] = $empId;
                $input['confirmed_by_name'] = $user->employee['empName'];
            }

            $input['confirmed_date'] = $input['timeStamp'];
        }

        $data = array_except($input, ['serviceLineSystemID', 'createdUserGroup', 'createdPcID', 'createdUserID', 'sub_levels_count', 'isAutoCreateDocument']);

        $segmentMaster = SegmentMaster::withoutGlobalScope('final_level')
                                      ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                      ->update($data);

        $this->auditLog($db, $input['serviceLineSystemID'],$uuid, "serviceline", "Segment master ".$input['ServiceLineDes']." has been updated", "U", $data, $previousValue);

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            return ['status' => true];
        }
        else {
            return $this->sendResponse($segmentMaster, 'Segment master updated successfully');
        }
    }

    public function getOrganizationStructure(Request $request)
    {
        $input = $request->all();

        $isDeletedShow = ($input['isDeletedShow'] == 'false') ? 0 : 1;
        $selectedCompanyId = $input['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId,true);

            $companyData = Company::find($selectedCompanyId);
            $segmenntData = [];
            foreach ($subCompanies as $key => $value) {
                if($isDeletedShow){
                    $segmenntData[] = $this->getNonGroupCompanyOrganizationStructurewithDeleted($value, true);
                } else{
                    $segmenntData[] = $this->getNonGroupCompanyOrganizationStructure($value, true);
                }
            }

            $companyData->subCompanies = $segmenntData;

            return $this->sendResponse(['orgData' => $companyData, 'isGroup' => true], 'Organization Levels retrieved successfully');
        }else{
            if($isDeletedShow){
                return $this->getNonGroupCompanyOrganizationStructurewithDeleted($selectedCompanyId);
            } else{
                return $this->getNonGroupCompanyOrganizationStructure($selectedCompanyId);
            }
            
        }
    }

    public function getNonGroupCompanyOrganizationStructure($companySystemID, $dataReturn = false)
    {
         $orgStructure = Company::withcount(['segments'])
                             ->with(['segments' => function ($q) {
                                $q->withoutGlobalScope('final_level')
                                  ->where(function($query) {
                                        $query->whereNull('masterID')
                                              ->orWhere('masterID', 0);
                                    })
                                  ->where('isDeleted', 0)
                                  ->withcount(['sub_levels' => function($query) {
                                        $query->where('isDeleted', 0);
                                  }])
                                  ->with(['sub_levels' => function($query) {
                                        $query->where('isDeleted', 0);
                                  }]);
                             }])
                             ->find($companySystemID);

        if ($dataReturn) {
            return $orgStructure;
        }

        if (empty($orgStructure)) {
            return $this->sendError('Warehouse not found');
        }

        return $this->sendResponse(['orgData' => $orgStructure, 'isGroup' => false], 'Organization Levels retrieved successfully');
    }

    public function getNonGroupCompanyOrganizationStructurewithDeleted($companySystemID, $dataReturn = false)
    {
         $orgStructure = Company::withcount(['segments'])
                             ->with(['segments' => function ($q) {
                                $q->withoutGlobalScope('final_level')
                                  ->withoutGlobalScope('deleted_status')
                                  ->where(function($query) {
                                    $query->whereNull('masterID')
                                          ->orWhere('masterID', 0);
                                                })
                                            ->withcount(['sub_level_deleted' => function($query) {
                                                    
                                            }])
                                            ->with(['sub_level_deleted' => function($query) {
                                                    
                                            }]);
                                        }])
                                        ->find($companySystemID);

        if ($dataReturn) {
            return $orgStructure;
        }

        if (empty($orgStructure)) {
            return $this->sendError('Warehouse not found');
        }

        return $this->sendResponse(['orgData' => $orgStructure, 'isGroup' => false], 'Organization Levels retrieved successfully with deleted segments');
    }

    public function getAllSegmentForApproval(Request $request) {

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request->selectedCompanyID;

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companyID = \Helper::getGroupCompany($companyId);
        } else {
            $companyID = [$companyId];
        }

        $empID = \Helper::getEmployeeSystemID();
        $values = implode(',', array_map(function($value)
        {
            return trim($value, ',');
        }, $companyID));

        $search = $request->input('search.value');
        $filter='';
        if($search){
            $search = str_replace("\\", "\\\\", $search);
            $filter = " AND (( serviceline.ServiceLineCode LIKE '%{$search}%') OR ( serviceline.ServiceLineDes LIKE '%{$search}%'))";
        }

        $sql = "
                SELECT 
                    serviceline.*, 
                    parentSegment.ServiceLineCode AS parentSegmentCode,
                    employeesdepartments.approvalDeligated, 
                    erp_documentapproved.documentApprovedID, 
                    rollLevelOrder, 
                    approvalLevelID, 
                    documentSystemCode 
                FROM erp_documentapproved
                INNER JOIN employeesdepartments 
                    ON erp_documentapproved.approvalGroupID = employeesdepartments.employeeGroupID 
                    AND erp_documentapproved.documentSystemID = employeesdepartments.documentSystemID
                    AND erp_documentapproved.companySystemID = employeesdepartments.companySystemID
                INNER JOIN serviceline 
                    ON serviceline.serviceLineSystemID = erp_documentapproved.documentSystemCode 
                    AND erp_documentapproved.rollLevelOrder = serviceline.RollLevForApp_curr
                LEFT JOIN serviceline AS parentSegment 
                    ON serviceline.masterID = parentSegment.serviceLineSystemID
                WHERE serviceline.approved_yn = 0 
                    {$filter}
                    AND serviceline.confirmed_yn = 1
                    AND employeesdepartments.documentSystemID = 132 
                    AND erp_documentapproved.approvedYN = 0
                    AND erp_documentapproved.rejectedYN = 0
                    AND erp_documentapproved.documentSystemID = 132
                    AND employeesdepartments.isActive = 1
                    AND employeesdepartments.employeeSystemID = $empID
                    AND employeesdepartments.removedYN = 0
                    AND employeesdepartments.companySystemID IN ($values)
                GROUP BY serviceline.serviceLineSystemID 
                ORDER BY documentApprovedID";

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        $segments = DB::select($sql);
        if ($isEmployeeDischarched == 'true') {
            $segments = [];
        }

        $data['search']['value'] = '';
        $request->merge($data);
        $request->request->remove('search.value');

        return \DataTables::of($segments)
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getSegmentMasterAudit(Request $request)
    {
        $id = $request->get('id');

        $segmentMaster = $this->segmentMasterRepository->withoutGlobalScope('final_level')->with(['created_by', 'confirmed_by', 'modified_by', 'approved_by' => function ($query) {
                $query->with('employee')
                    ->where('documentSystemID', 132);
            }])
            ->find($id);

        if (empty($segmentMaster)) {
            return $this->sendError('Segment Master not found');
        }

        return $this->sendResponse($segmentMaster->toArray(), 'Segment Master retrieved successfully');
    }

    public function rejectSegmentMaster(Request $request)
    {
        $reject = Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }
    }

    public function assignedCompaniesBySegment(Request $request)
    {
        $segmentId = $request->segmentId;
        $assignedCompanies = SegmentAssigned::with('company')
            ->where('serviceLineSystemID', $segmentId)
            ->get();

        return $this->sendResponse($assignedCompanies, 'Segment assigned companies retrieved successfully.');
    }

    public function exportSegmentMaster(Request $request) {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input,array('companyId'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $segmentId = $input['serviceLineSystemID'];
        $isActive = $input['isActive'];
        $approvalStatus = $input['approved_yn'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if($isGroup){
            $childCompanies = \Helper::getGroupCompany($companyId);
        }else{
            $childCompanies = [$companyId];
        }

        $segmentMasters = SegmentMaster::withoutGlobalScope('final_level')
            ->whereIn('companySystemID',$childCompanies)
            ->with(['company']);

        if(isset($segmentId) && $segmentId !== null && $segmentId != 0)  {
            $allChild = SegmentMaster::getAllChildSegmentIds($segmentId);
            $allSegmentIds = array_merge([$segmentId], $allChild);

            $segmentMasters->whereIn('serviceLineSystemID', $allSegmentIds);
        }

        if(isset($isActive) && !is_null($isActive)) {
            $segmentMasters->where('isActive', $isActive);
        }

        if(isset($approvalStatus) && !is_null($approvalStatus)) {
            if ($approvalStatus == 2) {
                $segmentMasters->where('approved_yn', 1);
            } else {
                $segmentMasters->where('confirmed_yn', $approvalStatus);
            }
        }

        $search = $request->input('search.value');
        if($search){
            $search = str_replace("\\", "\\\\", $search);
            $segmentMasters =   $segmentMasters->where(function ($query) use($search) {
                $query->where('ServiceLineCode','LIKE',"%{$search}%")
                    ->orWhere('ServiceLineDes', 'LIKE', "%{$search}%");
            });
        }

        $segmentMasters = $segmentMasters->orderBy('serviceLineSystemID','desc')->get();

        $data = array();
        $x = 0;
        foreach ($segmentMasters as $val) {
            $x++;
            $data[$x]['Segment Code'] = $val->ServiceLineCode;
            $data[$x]['Segment Description'] = $val->ServiceLineDes;
            $data[$x]['Active Status'] = ($val->isActive == 1) ? 'Yes' : 'No';
            $data[$x]['Type'] = ($val->isFinalLevel == 1) ? 'Final' : 'Parent';
            $data[$x]['Is Public'] = ($val->isPublic == 1) ? 'Yes' : 'No';

            if ($val->confirmed_yn == 1 && $val->approved_yn == 1) {
                $data[$x]['status'] = 'Fully Approved';
            } elseif ($val->confirmed_yn == 1 && $val->approved_yn == 0) {
                $data[$x]['status'] = 'Not Approved';
            } elseif ($val->confirmed_yn == 0 && $val->isActive == 1) {
                $data[$x]['status'] = 'Active only';
            } else {
                $data[$x]['status'] = 'Not Active';
            }
        }

        $companyMaster = Company::find(isset($request->companyId)?$request->companyId:null);
        $companyCode = $companyMaster->CompanyID ?? 'common';
        $detail_array = array(
            'company_code'=>$companyCode,
        );

        $fileName = 'segment_master';
        $path = 'system/segment_master/excel/';
        $type = 'xls';
        $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);

        if($basePath == '')
        {
            return $this->sendError('Unable to export excel');
        }
        else
        {
            return $this->sendResponse($basePath, trans('custom.success_export'));
        }
    }

    public function segmentReopen(Request $request) {
        $reopen = ReopenDocument::reopenDocument($request);
        if (!$reopen["success"]) {
            return $this->sendError($reopen["message"]);
        } else {
            return $this->sendResponse(array(), $reopen["message"]);
        }
    }

    public function segmentReferBack(Request $request) {
        $input = $request->all();
        $id = $input['id'];

        $segment = SegmentMaster::withoutGlobalScope('final_level')->find($id);

        if (empty($segment)) {
            return $this->sendError('Segment master not found');
        }

        if ($segment->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this segment');
        }

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $segment->companySystemID)
            ->where('documentSystemID', $segment->documentSystemID)
            ->get();

        if ($fetchDocumentApproved->isEmpty()) {
            return $this->sendError('Approval records not found');
        }

        foreach ($fetchDocumentApproved as $DocumentApproved) {
            $DocumentApproved['refTimes'] = $segment->timesReferred;
        }

        $documentApprovedArray = $fetchDocumentApproved->toArray();
        DocumentReferedHistory::insert($documentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $segment->companySystemID)
            ->where('documentSystemID', $segment->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $data['confirmed_by_emp_system_id'] = null;
            $data['confirmed_by_emp_id'] = null;
            $data['confirmed_by_name'] = null;
            $data['confirmed_date'] = null;
            $data['confirmed_yn'] = 0;
            $data['refferedBackYN'] = 0;
            $data['RollLevForApp_curr'] = 1;

           SegmentMaster::withoutGlobalScope('final_level')->where('serviceLineSystemID', $id)->update($data);
        }

        return $this->sendResponse($segment->toArray(), 'Segment Master Amend successfully');

    }

    public function segmentsForPoAnalysisReport(Request $request)
    {
        $companySystemID = $request['companySystemID'];
        $serviceLines = SegmentMaster::where('companySystemID', $companySystemID)->where('refferedBackYN', 0)->approved()->withAssigned($companySystemID)->get();
        return $this->sendResponse($serviceLines, 'Segments retrieved successfully');
    }
}
