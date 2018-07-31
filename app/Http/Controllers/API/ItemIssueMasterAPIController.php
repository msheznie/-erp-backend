<?php
/**
 * =============================================
 * -- File Name : ItemIssueMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Item Issue Master
 * -- Author : Mohamed Fayas
 * -- Create date : 20 - June 2018
 * -- Description : This file contains the all CRUD for Item Issue Master
 * -- REVISION HISTORY
 * -- Date: 20-June 2018 By: Fayas Description: Added new functions named as getAllMaterielIssuesByCompany(),getMaterielIssueFormData()
 * -- Date: 22-June 2018 By: Fayas Description: Added new functions named as getAllMaterielRequestNotSelectedForIssueByCompany()
 * -- Date: 27-June 2018 By: Fayas Description: Added new functions named as getMaterielIssueAudit()
 * -- Date: 28-June 2018 By: Fayas Description: Added new functions named as getMaterielIssueApprovalByUser(),getMaterielIssueApprovedByUser()
 * -- Date: 26-July 2018 By: Fayas Description: Added new functions named as printItemIssue()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemIssueMasterAPIRequest;
use App\Http\Requests\API\UpdateItemIssueMasterAPIRequest;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\DocumentMaster;
use App\Models\ItemIssueDetails;
use App\Models\ItemIssueMaster;
use App\Models\ItemIssueType;
use App\Models\MaterielRequest;
use App\Models\MaterielRequestDetails;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\SupplierMaster;
use App\Models\Unit;
use App\Models\UnitConversion;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\ItemIssueMasterRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemIssueMasterController
 * @package App\Http\Controllers\API
 */
class ItemIssueMasterAPIController extends AppBaseController
{
    /** @var  ItemIssueMasterRepository */
    private $itemIssueMasterRepository;

    public function __construct(ItemIssueMasterRepository $itemIssueMasterRepo)
    {
        $this->itemIssueMasterRepository = $itemIssueMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemIssueMasters",
     *      summary="Get a listing of the ItemIssueMasters.",
     *      tags={"ItemIssueMaster"},
     *      description="Get all ItemIssueMasters",
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
     *                  @SWG\Items(ref="#/definitions/ItemIssueMaster")
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
        $this->itemIssueMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->itemIssueMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemIssueMasters = $this->itemIssueMasterRepository->all();

        return $this->sendResponse($itemIssueMasters->toArray(), 'Item Issue Masters retrieved successfully');
    }

    /**
     * @param CreateItemIssueMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemIssueMasters",
     *      summary="Store a newly created ItemIssueMaster in storage",
     *      tags={"ItemIssueMaster"},
     *      description="Store ItemIssueMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemIssueMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemIssueMaster")
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
     *                  ref="#/definitions/ItemIssueMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemIssueMasterAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        $input['createdPCid'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $companyFinancePeriod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();

        if ($companyFinancePeriod) {
            $input['FYBiggin'] = $companyFinancePeriod->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod->dateTo;
        }

        if (isset($input['issueDate'])) {
            if ($input['issueDate']) {
                $input['issueDate'] = new Carbon($input['issueDate']);
            }
        }

        $documentDate = $input['issueDate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];
        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('Issue Date not between Financial period !', 500);
        }

        $input['documentSystemID'] = 8;
        $input['documentID'] = 'MI';

        $lastSerial = ItemIssueMaster::where('companySystemID', $input['companySystemID'])
                                    ->orderBy('itemIssueAutoID', 'desc')
                                    ->first();

        $lastSerialNumber = 0;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }


        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $warehouse = WarehouseMaster::where('wareHouseSystemCode', $input['wareHouseFrom'])->first();
        if ($warehouse) {
            $input['wareHouseFromCode'] = $warehouse->wareHouseCode;
            $input['wareHouseFromDes'] = $warehouse->wareHouseDescription;
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $input['serialNo'] = $lastSerialNumber;
        $input['RollLevForApp_curr'] = 1;

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        $companyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();
        if ($companyFinanceYear) {
            $startYear = $companyFinanceYear['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];
        } else {
            $finYear = date("Y");
        }

        if ($documentMaster) {
            $itemIssueCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['itemIssueCode'] = $itemIssueCode;
        }

        $itemIssueMasters = $this->itemIssueMasterRepository->create($input);

        return $this->sendResponse($itemIssueMasters->toArray(), 'Item Issue Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemIssueMasters/{id}",
     *      summary="Display the specified ItemIssueMaster",
     *      tags={"ItemIssueMaster"},
     *      description="Get ItemIssueMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueMaster",
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
     *                  ref="#/definitions/ItemIssueMaster"
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
        /** @var ItemIssueMaster $itemIssueMaster */
        $itemIssueMaster = $this->itemIssueMasterRepository->with(['confirmed_by','created_by'])->findWithoutFail($id);

        if (empty($itemIssueMaster)) {
            return $this->sendError('Item Issue Master not found');
        }

        return $this->sendResponse($itemIssueMaster->toArray(), 'Item Issue Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateItemIssueMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemIssueMasters/{id}",
     *      summary="Update the specified ItemIssueMaster in storage",
     *      tags={"ItemIssueMaster"},
     *      description="Update ItemIssueMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemIssueMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemIssueMaster")
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
     *                  ref="#/definitions/ItemIssueMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemIssueMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by','confirmedByName',
            'confirmedByEmpID','confirmedDate','confirmed_by','confirmedByEmpSystemID']);

        $input = $this->convertArrayToValue($input);

        /** @var ItemIssueMaster $itemIssueMaster */
        $itemIssueMaster = $this->itemIssueMasterRepository->findWithoutFail($id);

        if (empty($itemIssueMaster)) {
            return $this->sendError('Item Issue Master not found');
        }

        if($itemIssueMaster->serviceLineSystemID != $input['serviceLineSystemID']){
            $checkDepartmentActive = SegmentMaster::find($input['serviceLineSystemID']);
            if (empty($checkDepartmentActive)) {
                return $this->sendError('Department not found');
            }

            if($checkDepartmentActive->isActive == 0){
                return $this->sendError('Selected Department is not active please select different Department',500);
            }
        }

        if($itemIssueMaster->wareHouseFrom != $input['wareHouseFrom']){
            $checkWareHouseActive = WarehouseMaster::find($input['wareHouseFrom']);
            if (empty($checkWareHouseActive)) {
                return $this->sendError('WareHouse not found');
            }

            if($checkWareHouseActive->isActive == 0){
                return $this->sendError('Selected WareHouse is not active please select different WareHouse',500);
            }
        }

        $companyFinancePeriod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();

        if ($companyFinancePeriod) {
            $input['FYBiggin'] = $companyFinancePeriod->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod->dateTo;
        }

        if (isset($input['issueDate'])) {
            if ($input['issueDate']) {
                $input['issueDate'] = new Carbon($input['issueDate']);
            }
        }

        $documentDate = $input['issueDate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];
        if (($documentDate > $monthBegin) && ($documentDate < $monthEnd)) {
        } else {
            return $this->sendError('Issue Date not between Financial period !', 500);
        }

        if ($input['issueType'] == 2) {
            if (isset($input['reqDocID'])) {
                if ($input['reqDocID']) {

                    $materielRequest = MaterielRequest::where('RequestID', $input['reqDocID'])->with(['created_by'])->first();

                    if (!empty($materielRequest)) {

                       if($input['reqDocID'] != $itemIssueMaster->reqDocID){
                            if($materielRequest->selectedForIssue == -1){
                                return $this->sendError('This Request already selected. Please check again!', 500);
                            }
                       }

                        $input['reqByID'] = $materielRequest->createdUserID;
                        $input['reqDate'] = $materielRequest->RequestedDate;
                        $input['reqComment'] = $materielRequest->comments;

                        if (!empty($materielRequest->created_by)) {
                            $input['reqByName'] = $materielRequest->created_by->empName;
                        }
                    }

                }
            }
        } else {
            $input['reqDocID'] = null;
            $input['reqDate'] = null;
            $input['reqComment'] = null;
            $input['reqByName'] = null;
        }


        if ($itemIssueMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {


            $checkItems = ItemIssueDetails::where('itemIssueAutoID', $id)
                                                ->count();
            if ($checkItems == 0) {
                return $this->sendError('Every issue should have at least one item', 500);
            }

            $checkQuantity = ItemIssueDetails::where('itemIssueAutoID', $id)
                                    ->where(function ($q){
                                        $q->where('qtyIssued', '<=', 0)
                                           ->orWhereNull('qtyIssued');
                                    })
                                    ->count();
            if ($checkQuantity > 0) {
                return $this->sendError('Every Item should have at least one minimum Qty Requested', 500);
            }


            $amount = ItemIssueDetails::where('itemIssueAutoID', $id)
                                        ->sum('issueCostRptTotal');
            $input['RollLevForApp_curr'] = 1;
            $params = array('autoID' => $id,
                'company' => $itemIssueMaster->companySystemID,
                'document' => $itemIssueMaster->documentSystemID,
                'segment' => $input['serviceLineSystemID'],
                'category' => 0,
                'amount' => $amount
            );

            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }

        $employee = \Helper::getEmployeeInfo();

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;


        $itemIssueMaster = $this->itemIssueMasterRepository->update($input, $id);

        return $this->sendResponse($itemIssueMaster->toArray(), 'Materiel Issue updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemIssueMasters/{id}",
     *      summary="Remove the specified ItemIssueMaster from storage",
     *      tags={"ItemIssueMaster"},
     *      description="Delete ItemIssueMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueMaster",
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
        /** @var ItemIssueMaster $itemIssueMaster */
        $itemIssueMaster = $this->itemIssueMasterRepository->findWithoutFail($id);

        if (empty($itemIssueMaster)) {
            return $this->sendError('Item Issue Master not found');
        }

        $itemIssueMaster->delete();

        return $this->sendResponse($id, 'Item Issue Master deleted successfully');
    }

    /**
     * get All Materiel Issues By Company
     * POST /getAllMaterielIssuesByCompany
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getAllMaterielIssuesByCompany(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'wareHouseFrom', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $itemIssueMaster = ItemIssueMaster::whereIn('companySystemID', $subCompanies)
            ->with(['created_by', 'warehouse_by', 'segment_by', 'customer_by']);


        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $itemIssueMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $itemIssueMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $itemIssueMaster->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('wareHouseFrom', $input)) {
            if ($input['wareHouseFrom'] && !is_null($input['wareHouseFrom'])) {
                $itemIssueMaster->where('wareHouseFrom', $input['wareHouseFrom']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $itemIssueMaster->whereMonth('issueDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $itemIssueMaster->whereYear('issueDate', '=', $input['year']);
            }
        }


        $itemIssueMaster = $itemIssueMaster->select(
            ['erp_itemissuemaster.itemIssueAutoID',
                'erp_itemissuemaster.itemIssueCode',
                'erp_itemissuemaster.comment',
                'erp_itemissuemaster.issueDate',
                'erp_itemissuemaster.customerSystemID',
                'erp_itemissuemaster.confirmedYN',
                'erp_itemissuemaster.approved',
                'erp_itemissuemaster.serviceLineSystemID',
                'erp_itemissuemaster.documentSystemID',
                'erp_itemissuemaster.confirmedByEmpSystemID',
                'erp_itemissuemaster.createdUserSystemID',
                'erp_itemissuemaster.confirmedDate',
                'erp_itemissuemaster.approvedDate',
                'erp_itemissuemaster.createdDateTime',
                'erp_itemissuemaster.issueRefNo',
                'erp_itemissuemaster.wareHouseFrom'
            ]);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $itemIssueMaster = $itemIssueMaster->where(function ($query) use ($search) {
                $query->where('itemIssueCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($itemIssueMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('itemIssueAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * get Materiel Issue Approved By User
     * POST /getMaterielIssueApprovedByUser
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getMaterielIssueApprovedByUser(Request $request)
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

        $search = $request->input('search.value');
        $itemIssueMaster = DB::table('erp_documentapproved')
            ->select(
                'erp_itemissuemaster.*',
                'employees.empName As created_emp',
                'serviceline.ServiceLineDes As MIServiceLineDes',
                'warehousemaster.wareHouseDescription As MIWareHouseDescription',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_itemissuemaster', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'itemIssueAutoID')
                    ->where('erp_itemissuemaster.companySystemID', $companyId)
                    ->where('erp_itemissuemaster.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('warehousemaster', 'wareHouseFrom', 'warehousemaster.wareHouseSystemCode')
            ->leftJoin('serviceline', 'erp_itemissuemaster.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [8])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $itemIssueMaster->where('erp_itemissuemaster.serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('wareHouseFrom', $input)) {
            if ($input['wareHouseFrom'] && !is_null($input['wareHouseFrom'])) {
                $itemIssueMaster->where('erp_itemissuemaster.wareHouseFrom', $input['wareHouseFrom']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $itemIssueMaster->whereMonth('erp_itemissuemaster.issueDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $itemIssueMaster->whereYear('erp_itemissuemaster.issueDate', '=', $input['year']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $itemIssueMaster = $itemIssueMaster->where(function ($query) use ($search) {
                $query->where('itemIssueCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($itemIssueMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('itemIssueAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * get Materiel Issue Approval By User
     * POST /getMaterielIssueApprovalByUser
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getMaterielIssueApprovalByUser(Request $request)
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

        $search = $request->input('search.value');
        $itemIssueMaster = DB::table('erp_documentapproved')
            ->select(
                'erp_itemissuemaster.*',
                'employees.empName As created_emp',
                'serviceline.ServiceLineDes As MIServiceLineDes',
                'warehousemaster.wareHouseDescription As MIWareHouseDescription',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyId)
                                                                ->where('documentSystemID', 1)
                                                                ->first();

                if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                    //$query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
                }

                $query->whereIn('employeesdepartments.documentSystemID', [8])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID);
            })
            ->join('erp_itemissuemaster', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'itemIssueAutoID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_itemissuemaster.companySystemID', $companyId)
                    ->where('erp_itemissuemaster.approved', 0)
                    ->where('erp_itemissuemaster.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('warehousemaster', 'wareHouseFrom', 'warehousemaster.wareHouseSystemCode')
            ->leftJoin('serviceline', 'erp_itemissuemaster.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [8])
            ->where('erp_documentapproved.companySystemID', $companyId);


        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $itemIssueMaster->where('erp_itemissuemaster.serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('wareHouseFrom', $input)) {
            if ($input['wareHouseFrom'] && !is_null($input['wareHouseFrom'])) {
                $itemIssueMaster->where('erp_itemissuemaster.wareHouseFrom', $input['wareHouseFrom']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $itemIssueMaster->whereMonth('erp_itemissuemaster.issueDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $itemIssueMaster->whereYear('erp_itemissuemaster.issueDate', '=', $input['year']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $itemIssueMaster = $itemIssueMaster->where(function ($query) use ($search) {
                $query->where('itemIssueCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($itemIssueMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('itemIssueAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }


    /**
     * get Materiel Issue Form Data
     * Get /getMaterielIssueFormData
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getMaterielIssueFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $segments = SegmentMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $segments = $segments->where('isActive', 1);
        }
        $segments = $segments->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = ItemIssueMaster::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $wareHouseLocation = WarehouseMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $wareHouseLocation = $wareHouseLocation->where('isActive', 1);
        }
        $wareHouseLocation = $wareHouseLocation->get();

        $companyPolicy = CompanyPolicyMaster::where('companySystemID',$companyId)
                                            ->where('companyPolicyCategoryID',22)
                                            ->first();

        $typeId = [];

        if(!empty($companyPolicy)){
            if($companyPolicy->isYesNO == 0){
                $typeId = [2];
            }else if($companyPolicy->isYesNO == 1){
                $typeId = [1];
            }
        }

        $types = ItemIssueType::whereIn('itemIssueTypeID',$typeId)->get();

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);

        $contracts = "";

        $units = Unit::all();

        $output = array(
            'segments' => $segments,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'wareHouseLocation' => $wareHouseLocation,
            'financialYears' => $financialYears,
            'types' => $types,
            'companyFinanceYear' => $companyFinanceYear,
            'contracts' => $contracts,
            'units' => $units
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    /**
     * get All Materiel Request Not Selected For Issue By Company
     * GET /getAllMaterielRequestNotSelectedForIssueByCompany
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getAllMaterielRequestNotSelectedForIssueByCompany(Request $request)
    {

        $input = $request->all();

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $materielRequests = MaterielRequest::whereIn('companySystemID', $subCompanies)
            //->where("selectedForIssue", 0);
            ->where("approved", -1);

        $materielRequests = $materielRequests->select(['RequestID', 'RequestCode']);

        $search = $input['search'];

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $materielRequests = $materielRequests->where(function ($query) use ($search) {
                $query->where('itemIssueCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        $materielRequests = $materielRequests->get();
        return $this->sendResponse($materielRequests->toArray(), 'Materiel Issue updated successfully');
    }

    /**
     * Display the specified Materiel Issue Audit.
     * GET|HEAD /getMaterielIssueAudit
     *
     * @param  int $id
     *
     * @return Response
     */
    public function getMaterielIssueAudit(Request $request)
    {
        $id = $request->get('id');
        $materielRequest = $this->itemIssueMasterRepository->getAudit($id);

        if (empty($materielRequest)) {
            return $this->sendError('Materiel Issue not found');
        }

        $materielRequest->docRefNo = \Helper::getCompanyDocRefNo($materielRequest->companySystemID,$materielRequest->documentSystemID);

        return $this->sendResponse($materielRequest->toArray(), 'Materiel Issue retrieved successfully');
    }

    public function printItemIssue(Request $request)
    {
        $id = $request->get('id');
        $materielRequest = $this->itemIssueMasterRepository->getAudit($id);

        if (empty($materielRequest)) {
            return $this->sendError('Materiel Issue not found');
        }

        $materielRequest->docRefNo = \Helper::getCompanyDocRefNo($materielRequest->companySystemID,$materielRequest->documentSystemID);

        $array = array('entity' => $materielRequest);
        $time = strtotime("now");
        $fileName = 'item_issue_' . $id . '_' . $time . '.pdf';
        $html = view('print.item_issue', $array);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream($fileName);
    }

}
