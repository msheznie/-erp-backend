<?php
/**
 * =============================================
 * -- File Name : StockReceiveAPIController.php
 * -- Project Name : ERP
 * -- Module Name : Stock Receive
 * -- Author : Mohamed Fayas
 * -- Create date : 23 - July 2018
 * -- Description : This file contains the all CRUD for Stock Receive
 * -- REVISION HISTORY
 * -- Date: 23-July 2018 By: Fayas Description: Added new functions named as getAllStockReceiveByCompany(),getStockReceiveFormData(),stockReceiveAudit()
 * -- Date: 24-July 2018 By: Fayas Description: Added new functions named as srPullFromTransferPreCheck()
 * -- Date: 25-July 2018 By: Fayas Description: Added new functions named as getStockReceiveApproval(),getApprovedSRForCurrentUser()
 * -- Date: 30-July 2018 By: Fayas Description: Added new functions named as printStockReceive()
 * -- Date: 28-August 2018 By: Fayas Description: Added new functions named as stockReceiveReopen()
 * -- Date: 03-December 2018 By: Fayas Description: Added new functions named as stockReceiveReferBack()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockReceiveAPIRequest;
use App\Http\Requests\API\UpdateStockReceiveAPIRequest;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinancePeriod;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\CompanyFinanceYear;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\ItemAssigned;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\StockReceive;
use App\Models\StockReceiveDetails;
use App\Models\StockReceiveDetailsRefferedBack;
use App\Models\StockReceiveRefferedBack;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\StockReceiveRepository;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StockReceiveController
 * @package App\Http\Controllers\API
 */
class StockReceiveAPIController extends AppBaseController
{
    /** @var  StockReceiveRepository */
    private $stockReceiveRepository;

    public function __construct(StockReceiveRepository $stockReceiveRepo)
    {
        $this->stockReceiveRepository = $stockReceiveRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockReceives",
     *      summary="Get a listing of the StockReceives.",
     *      tags={"StockReceive"},
     *      description="Get all StockReceives",
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
     *                  @SWG\Items(ref="#/definitions/StockReceive")
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
        $this->stockReceiveRepository->pushCriteria(new RequestCriteria($request));
        $this->stockReceiveRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockReceives = $this->stockReceiveRepository->all();

        return $this->sendResponse($stockReceives->toArray(), trans('custom.stock_receives_retrieved_successfully'));
    }

    /**
     * @param CreateStockReceiveAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockReceives",
     *      summary="Store a newly created StockReceive in storage",
     *      tags={"StockReceive"},
     *      description="Store StockReceive",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockReceive that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockReceive")
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
     *                  ref="#/definitions/StockReceive"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockReceiveAPIRequest $request)
    {
        DB::beginTransaction();
        $input = $request->all();

        $input = $this->convertArrayToValue($input);
        $employee = \Helper::getEmployeeInfo();
        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            DB::rollBack();
            return $this->sendError($companyFinanceYear["message"], 500);
        }

        $inputParam = $input;
        $inputParam["departmentSystemID"] = 10;
        $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
        if (!$companyFinancePeriod["success"]) {
            DB::rollBack();
            return $this->sendError($companyFinancePeriod["message"], 500);
        } else{
            $input['FYBiggin'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod["message"]->dateTo;
        }
        unset($inputParam);

        $validator = \Validator::make($input, [
            'locationFrom' => 'required|numeric|min:1',
            'locationTo' => 'required|numeric|min:1',
            'companyFinancePeriodID' => 'required|numeric|min:1',
            'companyFinanceYearID' => 'required|numeric|min:1',
            'receivedDate' => 'required|date|before_or_equal:today',
            'companyToSystemID' => 'required|numeric|min:1',
            'companyFromSystemID' => 'required|numeric|min:1',
            'serviceLineSystemID' => 'required|numeric|min:1',
            'refNo' => 'required',
            'comment' => 'required'
        ]);

        if ($validator->fails()) {
            DB::rollBack();
            return $this->sendError($validator->messages(), 422);
        }

        if (isset($input['receivedDate'])) {
            if ($input['receivedDate']) {
                $input['receivedDate'] = new Carbon($input['receivedDate']);
            }
        }

        $documentDate = $input['receivedDate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            DB::rollBack();
            return $this->sendError(trans('custom.receive_date_not_within_financial_period'), 500);
        }

        $warehouse = WarehouseMaster::where("wareHouseSystemCode", $input['locationTo'])
                                    ->first();

        if (!$warehouse) {
            DB::rollBack();
            return $this->sendError(trans('custom.location_to_not_found_1'), 500);
        }

        if ($warehouse->manufacturingYN == 1) {
            if (is_null($warehouse->WIPGLCode)) {
                DB::rollBack();
                return $this->sendError(trans('custom.please_assigned_wip_glcode_warehouse'), 500);
            } else {
                $checkGLIsAssigned = ChartOfAccountsAssigned::checkCOAAssignedStatus($warehouse->WIPGLCode, $input['companyToSystemID']);
                if (!$checkGLIsAssigned) {
                    DB::rollBack();
                    return $this->sendError(trans('custom.assigned_wip_gl_code_not_assigned_to_company'), 500);
                }
            }
        }


        $lastSerial = StockReceive::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->lockForUpdate()
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        //checking selected segment is active
        $segments = SegmentMaster::where("serviceLineSystemID", $input['serviceLineSystemID'])
            ->where('companySystemID', $input['companySystemID'])
            ->where('isActive', 1)
            ->first();

        if (empty($segments)) {
            DB::rollBack();
            return $this->sendError(trans('custom.selected_segment_not_active'), 500);
        }

        if ($input['locationFrom'] == $input['locationTo']) {
            DB::rollBack();
            return $this->sendError(trans('custom.location_from_and_location_to_cannot_be_same'), 500);
        }

        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $companyFrom = Company::where('companySystemID', $input['companyFromSystemID'])->first();
        if ($companyFrom) {
            $input['companyFrom'] = $companyFrom->CompanyID;
        }

        $companyTo = Company::where('companySystemID', $input['companyToSystemID'])->first();
        if ($companyTo) {
            $input['companyTo'] = $companyTo->CompanyID;
        }

        $input['serialNo'] = $lastSerialNumber;

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();
        if ($documentMaster) {
            $input['documentID'] = $documentMaster->documentID;
        }

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

        if ($input['interCompanyTransferYN']) {
            $input['interCompanyTransferYN'] = -1;
        } else {
            $input['interCompanyTransferYN'] = 0;
        }

        if ($documentMaster) {
            $stockTransferCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['stockReceiveCode'] = $stockTransferCode;
        }

        $stockReceives = $this->stockReceiveRepository->create($input);

        DB::commit();
        return $this->sendResponse($stockReceives->toArray(), trans('custom.stock_receive_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockReceives/{id}",
     *      summary="Display the specified StockReceive",
     *      tags={"StockReceive"},
     *      description="Get StockReceive",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockReceive",
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
     *                  ref="#/definitions/StockReceive"
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
        /** @var StockReceive $stockReceive */
        $stockReceive = $this->stockReceiveRepository->with(['confirmed_by', 'segment_by','finance_period_by'=> function($query){
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        },'finance_year_by'=> function($query){
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        },'location_to_by','location_from_by','company_from','company_to'])->findWithoutFail($id);

        if (empty($stockReceive)) {
            return $this->sendError(trans('custom.stock_receive_not_found'));
        }

        return $this->sendResponse($stockReceive->toArray(), trans('custom.stock_receive_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateStockReceiveAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockReceives/{id}",
     *      summary="Update the specified StockReceive in storage",
     *      tags={"StockReceive"},
     *      description="Update StockReceive",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockReceive",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockReceive that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockReceive")
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
     *                  ref="#/definitions/StockReceive"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockReceiveAPIRequest $request)
    {
        $input = $request->all();
        $wareHouseFromError = array('type' => 'locationFrom');
        $wareHouseToError   = array('type' => 'locationTo');
        $serviceLineError   = array('type' => 'serviceLine');
        $input = array_except($input, ['created_by', 'confirmed_by', 'segment_by','finance_period_by','finance_year_by','location_to_by','location_from_by','company_from','company_to']);
        $input = $this->convertArrayToValue($input);
        /** @var StockReceive $stockReceive */
        $stockReceive = $this->stockReceiveRepository->findWithoutFail($id);

        if (empty($stockReceive)) {
            return $this->sendError(trans('custom.stock_receive_not_found'));
        }

        $employee = \Helper::getEmployeeInfo();
        $item['modifiedPc'] = gethostname();
        $item['modifiedUser'] = $employee->empID;
        $item['modifiedUserSystemID'] = $employee->employeeSystemID;

        if (isset($input['receivedDate'])) {
            if ($input['receivedDate']) {
                $input['receivedDate'] = new Carbon($input['receivedDate']);
            }
        }
        if (isset($input['serviceLineSystemID'])) {
            //checking selected segment is active
            $segment = SegmentMaster::where("serviceLineSystemID", $input['serviceLineSystemID'])
                ->where('companySystemID', $input['companySystemID'])
                ->where('isActive', 1)
                ->first();

            if (empty($segment)) {
                $this->stockReceiveRepository->update(['serviceLineSystemID' => null,'serviceLineCode' => null],$id);
                return $this->sendError(trans('custom.selected_segment_not_active'),500,$serviceLineError);
            }

            if ($segment) {
                $input['serviceLineCode'] = $segment->ServiceLineCode;
            }
        }

        if (isset($input['locationFrom'])) {
            $checkWareHouseActiveFrom = WarehouseMaster::find($input['locationFrom']);
            if (empty($checkWareHouseActiveFrom)) {
                return $this->sendError(trans('custom.location_from_not_found'), 500, $wareHouseFromError);
            }

            if ($checkWareHouseActiveFrom->isActive == 0) {
                $this->stockReceiveRepository->update(['locationFrom' => null],$id);
                return $this->sendError(trans('custom.selected_location_from_not_active'), 500, $wareHouseFromError);
            }
        }

        if (isset($input['locationTo'])) {
            $checkWareHouseActiveTo = WarehouseMaster::find($input['locationTo']);
            if (empty($checkWareHouseActiveTo)) {
                return $this->sendError(trans('custom.location_to_not_found'), 500, $wareHouseToError);
            }

            if ($checkWareHouseActiveTo->isActive == 0) {
                $this->stockReceiveRepository->update(['locationTo' => null],$id);
                return $this->sendError(trans('custom.selected_location_to_not_active'), 500, $wareHouseToError);
            }

            if ($checkWareHouseActiveTo->manufacturingYN == 1) {
                if (is_null($checkWareHouseActiveTo->WIPGLCode)) {
                    return $this->sendError(trans('custom.please_assigned_wip_glcode_warehouse'), 500);
                } else {
                    $checkGLIsAssigned = ChartOfAccountsAssigned::checkCOAAssignedStatus($checkWareHouseActiveTo->WIPGLCode, $input['companyToSystemID']);
                    if (!$checkGLIsAssigned) {
                        return $this->sendError(trans('custom.assigned_wip_gl_code_not_assigned_to_company'), 500);
                    }
                }
            }
        }

        if ($input['locationFrom'] == $input['locationTo']) {
            $this->stockReceiveRepository->update(['locationTo' => null], $id);
            return $this->sendError(trans('custom.location_from_and_location_to_cannot_be_same'),500,$wareHouseToError);
        }

        if (isset($input['companyFromSystemID'])) {
            $companyFrom = Company::where('companySystemID', $input['companyFromSystemID'])->first();
            if ($companyFrom) {
                $input['companyFrom'] = $companyFrom->CompanyID;
            }
        }
        if ($input['interCompanyTransferYN']) {
            $input['interCompanyTransferYN'] = -1;
        } else {
            $input['interCompanyTransferYN'] = 0;
        }

        if ($stockReceive->confirmedYN == 0 && $input['confirmedYN'] == 1) {


            $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
            if (!$companyFinanceYear["success"]) {
                return $this->sendError($companyFinanceYear["message"], 500);
            }

            $inputParam = $input;
            $inputParam["departmentSystemID"] = 10;
            $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
            if (!$companyFinancePeriod["success"]) {
                return $this->sendError($companyFinancePeriod["message"], 500);
            } else{
                $input['FYBiggin'] = $companyFinancePeriod["message"]->dateFrom;
                $input['FYEnd'] = $companyFinancePeriod["message"]->dateTo;
            }

            unset($inputParam);
            $documentDate = $input['receivedDate'];
            $monthBegin = $input['FYBiggin'];
            $monthEnd = $input['FYEnd'];

            if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
            } else {
                return $this->sendError(trans('custom.receive_date_not_within_financial_period'),500);
            }

            $stockReceiveDetailExist = StockReceiveDetails::where('stockReceiveAutoID', $id)
                ->count();

            if ($stockReceiveDetailExist == 0) {
                return $this->sendError(trans('custom.stock_receive_document_cannot_confirm_without_deta'),500);
            }

            $checkQuantity = StockReceiveDetails::where('stockReceiveAutoID', $id)
                ->where('qty', '<=', 0)
                ->count();

            if ($checkQuantity > 0) {
                return $this->sendError(trans('custom.every_item_should_have_minimum_qty'), 500);
            }

            $validator = \Validator::make($input, [
                'locationFrom' => 'required|numeric|min:1',
                'locationTo' => 'required|numeric|min:1',
                'companyFinancePeriodID' => 'required|numeric|min:1',
                'companyFinanceYearID' => 'required|numeric|min:1',
                'receivedDate' => 'required|date|before_or_equal:today',
                'companyToSystemID' => 'required|numeric|min:1',
                'companyFromSystemID' => 'required|numeric|min:1',
                'serviceLineSystemID' => 'required|numeric|min:1',
                'refNo' => 'required',
                'comment' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            if ($input['companyFromSystemID'] == $input['companyToSystemID'] && $input['interCompanyTransferYN'] == -1) {
                return $this->sendError(trans('custom.receive_document_inter_company_same'), 500);
            }

            $stockReceiveDetails = StockReceiveDetails::where('stockReceiveAutoID', $id)->with(['transfer'])->get();

            if ($stockReceive->interCompanyTransferYN == -1) {
                $notAssignItems = "";
                $count = 0;
                foreach ($stockReceiveDetails as $srDetail) {

                    // check transfer date less than receive date
                    if(!empty($srDetail->transfer)){
                        $transferDate = Carbon::parse($srDetail->transfer->tranferDate)->format('Y-m-d');
                        $documentDate = Carbon::parse($documentDate)->format('Y-m-d');
                        if($transferDate>$documentDate){
                            return $this->sendError(trans('custom.receive_date_cannot_be_less_than_transfer_date'), 500);
                        }
                    }

                    $itemAssign = ItemAssigned::where("companySystemID", $stockReceive->companySystemID)
                        ->where("itemCodeSystem", $srDetail->itemCodeSystem)
                        ->first();

                    if (empty($itemAssign)) {
                        if ($count == 0) {
                            $notAssignItems = $srDetail->itemPrimaryCode;
                        } else {
                            $notAssignItems = $notAssignItems . ", " . $srDetail->itemPrimaryCode;
                        }

                        $count++;
                    }
                }

                if ($count > 0) {
                    if ($count < 5) {
                        $notAssignItems = $notAssignItems . " are not assigned to " . $stockReceive->companyID . ". Please assign and try again";
                        return $this->sendError($notAssignItems, 500);
                    } else {
                        return $this->sendError(trans('custom.some_items_not_assigned_to_company', ['company' => $stockReceive->companyID]), 500);
                    }
                }
            }

            $checkPlAccount = ($stockReceive->interCompanyTransferYN == -1) ? SystemGlCodeScenarioDetail::getGlByScenario($stockReceive->companySystemID, $stockReceive->documentSystemID, "stock-transfer-pl-account-for-inter-company-transfer") : SystemGlCodeScenarioDetail::getGlByScenario($stockReceive->companySystemID, $stockReceive->documentSystemID, "stock-transfer-pl-account");

            if (is_null($checkPlAccount)) {
                return $this->sendError(trans('custom.please_configure_pl_account_stock_receive'), 500);
            }


            unset($input['confirmedYN']);
            unset($input['confirmedByEmpSystemID']);
            unset($input['confirmedByEmpID']);
            unset($input['confirmedByName']);
            unset($input['confirmedDate']);

            $params = array('autoID' => $id, 'company' => $input["companySystemID"], 'document' => $input["documentSystemID"], 'segment' => $input["serviceLineSystemID"], 'category' => '', 'amount' => 0);
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"]);
            }
        }

        $stockReceive = $this->stockReceiveRepository->update($input, $id);

        return $this->sendReponseWithDetails($stockReceive->toArray(), trans('custom.stock_receive_updated_successfully'),1, isset($confirm['data']) ? $confirm['data'] : null);
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockReceives/{id}",
     *      summary="Remove the specified StockReceive from storage",
     *      tags={"StockReceive"},
     *      description="Delete StockReceive",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockReceive",
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
        /** @var StockReceive $stockReceive */
        $stockReceive = $this->stockReceiveRepository->findWithoutFail($id);

        if (empty($stockReceive)) {
            return $this->sendError(trans('custom.stock_receive_not_found'));
        }

        $stockReceive->delete();

        return $this->sendResponse($id, trans('custom.stock_receive_deleted_successfully'));
    }

    public function getAllStockReceiveByCompany(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'locationFrom', 'locationTo', 'confirmedYN', 'approved',
            'grvRecieved', 'month', 'year', 'invoicedBooked'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');
        $serviceLineSystemID = $request['serviceLineSystemID'];
        $serviceLineSystemID = (array)$serviceLineSystemID;
        $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');
        $stockReceive = $this->stockReceiveRepository->stockReceiveListQuery($request, $input, $search, $serviceLineSystemID);

        $policy = 0;

        return \DataTables::eloquent($stockReceive)
            ->addColumn('Actions', $policy)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('stockReceiveAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }


    public function getStockReceiveFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $segments = SegmentMaster::where("companySystemID", $companyId)->approved()->withAssigned($companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $segments = $segments->where('isActive', 1);
        }
        $segments = $segments->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = StockReceive::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $wareHouseLocation = WarehouseMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $wareHouseLocation = $wareHouseLocation->where('isActive', 1);
        }
        $wareHouseLocation = $wareHouseLocation->get();


        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);

        $companies = \Helper::allCompanies();

        $output = array('segments' => $segments,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'wareHouseLocation' => $wareHouseLocation,
            'financialYears' => $financialYears,
            'companyFinanceYear' => $companyFinanceYear,
            'companies' => $companies
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function stockReceiveAudit(Request $request)
    {
        $id = $request->get('id');
        $stockReceive = $this->stockReceiveRepository->getAudit($id);

        if (empty($stockReceive)) {
            return $this->sendError(trans('custom.materiel_return_not_found_1'));
        }

        $stockReceive->docRefNo = \Helper::getCompanyDocRefNo($stockReceive->companySystemID, $stockReceive->documentSystemID);

        return $this->sendResponse($stockReceive->toArray(), trans('custom.stock_receive_retrieved_successfully'));
    }

    public function printStockReceive(Request $request)
    {
        $id = $request->get('id');
        $stockReceive = $this->stockReceiveRepository->getAudit($id);

        if (empty($stockReceive)) {
            return $this->sendError(trans('custom.stock_receive_not_found'));
        }

        $stockReceive->docRefNo = \Helper::getCompanyDocRefNo($stockReceive->companySystemID, $stockReceive->documentSystemID);

        $array = array('entity' => $stockReceive);
        $time = strtotime("now");
        $fileName = 'stock_receive_' . $id . '_' . $time . '.pdf';
        $html = view('print.stock_receive', $array);
        $htmlFooter = view('print.stock_receive_footer', $array);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-L', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
        $mpdf->AddPage('L');
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->WriteHTML($html);
        return $mpdf->Output($fileName, 'I');
    }


    public function srPullFromTransferPreCheck(Request $request)
    {

        $input = $request->all();

        $id = $input['stockReceiveAutoID'];

        $stockReceive = StockReceive::find($id);

        if (empty($stockReceive)) {
            return $this->sendError(trans('custom.stock_receive_not_found'));
        }

        $validator = \Validator::make($stockReceive->toArray(), [
            'locationFrom' => 'required|numeric|min:1',
            'locationTo' => 'required|numeric|min:1',
            'companyToSystemID' => 'required|numeric|min:1',
            'companyFromSystemID' => 'required|numeric|min:1',
            'serviceLineSystemID' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        //checking segment is active

        $segments = SegmentMaster::where("serviceLineSystemID", $stockReceive->serviceLineSystemID)
            ->where('companySystemID', $input['companySystemID'])
            ->where('isActive', 1)
            ->first();

        if (empty($segments)) {
            return $this->sendError(trans('custom.selected_department_not_active'), 500);
        }

        $checkWareHouseActiveFrom = WarehouseMaster::find($stockReceive->locationFrom);
        if (empty($checkWareHouseActiveFrom)) {
            return $this->sendError(trans('custom.location_from_not_found'), 500);
        }

        if ($checkWareHouseActiveFrom->isActive == 0) {
            return $this->sendError(trans('custom.selected_location_from_not_active'), 500);
        }

        $checkWareHouseActiveTo = WarehouseMaster::find($stockReceive->locationTo);
        if (empty($checkWareHouseActiveTo)) {
            return $this->sendError(trans('custom.location_to_not_found'), 500);
        }

        if ($checkWareHouseActiveTo->isActive == 0) {
            return $this->sendError(trans('custom.selected_location_to_not_active'), 500);
        }

        return $this->sendResponse($id, trans('custom.success'));
    }

    public function getApprovedSRForCurrentUser(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

        $stockTransferMasters = DB::table('erp_documentapproved')->select(
            'erp_stockreceive.stockReceiveAutoID',
            'erp_stockreceive.stockReceiveCode',
            'erp_stockreceive.documentSystemID',
            'erp_stockreceive.refNo',
            'erp_stockreceive.receivedDate',
            'erp_stockreceive.comment',
            'erp_stockreceive.serviceLineCode',
            'erp_stockreceive.createdDateTime',
            'erp_stockreceive.confirmedDate',
            'erp_stockreceive.postedDate',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user',
            'serviceline.ServiceLineDes as serviceLineDescription'
        )->join('erp_stockreceive', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'stockReceiveAutoID')
                ->where('erp_stockreceive.companySystemID', $companyID)
                ->where('erp_stockreceive.approved', -1)
                ->where('erp_stockreceive.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', -1)
            ->join('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->join('serviceline', 'erp_stockreceive.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.documentSystemID', 10)
            ->where('erp_documentapproved.companySystemID', $companyID)->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $stockTransferMasters = $stockTransferMasters->where(function ($query) use ($search) {
                $query->where('stockReceiveCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($stockTransferMasters)
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

    public function getStockReceiveApproval(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

        $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyID)
            ->where('documentSystemID', 10)
            ->first();

        $stockTransferMasters = DB::table('erp_documentapproved')->select(
            'employeesdepartments.approvalDeligated',
            'erp_stockreceive.stockReceiveAutoID',
            'erp_stockreceive.stockReceiveCode',
            'erp_stockreceive.documentSystemID',
            'erp_stockreceive.refNo',
            'erp_stockreceive.receivedDate',
            'erp_stockreceive.comment',
            'erp_stockreceive.serviceLineCode',
            'erp_stockreceive.createdDateTime',
            'erp_stockreceive.confirmedDate',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user',
            'serviceline.ServiceLineDes as serviceLineDescription'
        )->join('employeesdepartments', function ($query) use ($companyID, $empID, $serviceLinePolicy) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
            if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                // $query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
            }
            $query->where('employeesdepartments.documentSystemID', 10)
                ->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
        })->join('erp_stockreceive', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'stockReceiveAutoID')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('erp_stockreceive.companySystemID', $companyID)
                ->where('erp_stockreceive.approved', 0)
                ->where('erp_stockreceive.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', 0)
            ->join('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftjoin('serviceline', 'erp_stockreceive.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 10)
            ->where('erp_documentapproved.companySystemID', $companyID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $stockTransferMasters = $stockTransferMasters->where(function ($query) use ($search) {
                $query->where('stockReceiveCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $stockTransferMasters = [];
        }

        return \DataTables::of($stockTransferMasters)
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

    public function stockReceiveReopen(Request $request)
    {
        $input = $request->all();

        $id = $input['stockReceiveAutoID'];
        $stockTransfer = $this->stockReceiveRepository->findWithoutFail($id);
        $emails = array();
        if (empty($stockTransfer)) {
            return $this->sendError(trans('custom.stock_receive_not_found'));
        }

        if ($stockTransfer->approved == -1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_stock_receive_it_is_already'));
        }

        if ($stockTransfer->RollLevForApp_curr > 1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_stock_receive_it_is_already_1'));
        }

        if ($stockTransfer->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_stock_receive_it_is_not_con'));
        }

        $updateInput = ['confirmedYN' => 0,'confirmedByEmpSystemID' => null,'confirmedByEmpID' => null,
            'confirmedByName' => null, 'confirmedDate' => null,'RollLevForApp_curr' => 1];

        $this->stockReceiveRepository->update($updateInput,$id);

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $stockTransfer->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $stockTransfer->stockTransferCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $stockTransfer->stockTransferCode;

        $subject = trans('email.is_reopened_subject', ['attribute' => $cancelDocNameSubject]);

        $body = trans('email.is_reopened_body', [
            'attribute' => $cancelDocNameBody,
            'empID' => $employee->empID,
            'empName' => $employee->empFullName,
            'reopenComments' => $input['reopenComments']
        ]);

        $documentApproval = DocumentApproved::where('companySystemID', $stockTransfer->companySystemID)
            ->where('documentSystemCode', $stockTransfer->stockReceiveAutoID)
            ->where('documentSystemID', $stockTransfer->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $stockTransfer->companySystemID)
                    ->where('documentSystemID', $stockTransfer->documentSystemID)
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

        DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $stockTransfer->companySystemID)
            ->where('documentSystemID', $stockTransfer->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($stockTransfer->documentSystemID,$id,$input['reopenComments'],'Reopened');

        return $this->sendResponse($stockTransfer->toArray(), trans('custom.stock_receive_reopened_successfully'));
    }

    public function stockReceiveReferBack(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];

        $stockReceive = $this->stockReceiveRepository->find($id);
        if (empty($stockReceive)) {
            return $this->sendError(trans('custom.stock_receive_not_found'));
        }

        if ($stockReceive->refferedBackYN != -1) {
            return $this->sendError(trans('custom.you_cannot_refer_back_this_stock_receive'));
        }

        $stockReceiveArray = $stockReceive->toArray();

        $storeSRHistory = StockReceiveRefferedBack::insert($stockReceiveArray);

        $fetchDetails = StockReceiveDetails::where('stockReceiveAutoID', $id)
            ->get();

        if (!empty($fetchDetails)) {
            foreach ($fetchDetails as $detail) {
                $detail['timesReferred'] = $stockReceive->timesReferred;
            }
        }

        $stockReceiveDetailArray = $fetchDetails->toArray();

        $storeSRDetailHistory = StockReceiveDetailsRefferedBack::insert($stockReceiveDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $stockReceive->companySystemID)
            ->where('documentSystemID', $stockReceive->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $stockReceive->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentRefereedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $stockReceive->companySystemID)
            ->where('documentSystemID', $stockReceive->documentSystemID)
            ->delete();

        if ($deleteApproval) {

            $updateArray = ['refferedBackYN' => 0,'confirmedYN' => 0,'confirmedByEmpSystemID' => null,
                'confirmedByEmpID' => null,'confirmedByName' => null,'confirmedDate' => null,'RollLevForApp_curr' => 1];

            $this->stockReceiveRepository->update($updateArray,$id);
        }

        return $this->sendResponse($stockReceive->toArray(), trans('custom.stock_transfer_amend_successfully'));
    }

}
