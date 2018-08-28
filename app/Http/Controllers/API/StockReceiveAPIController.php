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
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockReceiveAPIRequest;
use App\Http\Requests\API\UpdateStockReceiveAPIRequest;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\EmployeesDepartment;
use App\Models\ItemAssigned;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\StockReceive;
use App\Models\StockReceiveDetails;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\StockReceiveRepository;
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

        return $this->sendResponse($stockReceives->toArray(), 'Stock Receives retrieved successfully');
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
        $input = $request->all();

        $input = $this->convertArrayToValue($input);
        $employee = \Helper::getEmployeeInfo();
        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

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
            return $this->sendError('Receive date is not within the selected financial period !', 500);
        }

        $lastSerial = StockReceive::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('stockReceiveAutoID', 'desc')
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
            return $this->sendError('Selected segment is not active. Please select an active segment', 500);
        }

        if ($input['locationFrom'] == $input['locationTo']) {
            return $this->sendError('Location From and Location To  cannot be same', 500);
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

        return $this->sendResponse($stockReceives->toArray(), 'Stock Receive saved successfully');
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
        }])->findWithoutFail($id);

        if (empty($stockReceive)) {
            return $this->sendError('Stock Receive not found');
        }

        return $this->sendResponse($stockReceive->toArray(), 'Stock Receive retrieved successfully');
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
        $input = array_except($input, ['created_by', 'confirmed_by', 'segment_by','finance_period_by','finance_year_by']);
        $input = $this->convertArrayToValue($input);
        /** @var StockReceive $stockReceive */
        $stockReceive = $this->stockReceiveRepository->findWithoutFail($id);

        if (empty($stockReceive)) {
            return $this->sendError('Stock Receive not found');
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
                return $this->sendError('Selected segment is not active. Please select an active segment');
            }

            if ($input['locationFrom'] == $input['locationTo']) {
                return $this->sendError('Location From and Location To  cannot be same');
            }

            if ($segment) {
                $input['serviceLineCode'] = $segment->ServiceLineCode;
            }
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
                return $this->sendError('Receive date is not within the selected financial period !');
            }

            $stockReceiveDetailExist = StockReceiveDetails::where('stockReceiveAutoID', $id)
                ->count();

            if ($stockReceiveDetailExist == 0) {
                return $this->sendError('Stock Receive document cannot confirm without details');
            }

            $checkQuantity = StockReceiveDetails::where('stockReceiveAutoID', $id)
                ->where('qty', '<', 1)
                ->count();

            if ($checkQuantity > 0) {
                return $this->sendError('Every item should have at least one minimum Qty', 500);
            }

            $validator = \Validator::make($input, [
                'locationFrom' => 'required|numeric|min:1',
                'locationTo' => 'required|numeric|min:1',
                'companyFinancePeriodID' => 'required|numeric|min:1',
                'companyFinanceYearID' => 'required|numeric|min:1',
                'receivedDate' => 'required',
                'companyToSystemID' => 'required|numeric|min:1',
                'companyFromSystemID' => 'required|numeric|min:1',
                'serviceLineSystemID' => 'required|numeric|min:1',
                'refNo' => 'required',
                'comment' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $stockReceiveDetails = StockReceiveDetails::where('stockReceiveAutoID', $id)->get();


            if ($stockReceive->interCompanyTransferYN == -1) {
                $notAssignItems = "";
                $count = 0;
                foreach ($stockReceiveDetails as $srDetail) {
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
                        return $this->sendError("Some items are not assigned to " . $stockReceive->companyID . ". Please assign and try again", 500);
                    }
                }
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

        return $this->sendResponse($stockReceive->toArray(), 'StockReceive updated successfully');
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
            return $this->sendError('Stock Receive not found');
        }

        $stockReceive->delete();

        return $this->sendResponse($id, 'Stock Receive deleted successfully');
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

        $stockReceive = StockReceive::where('companySystemID', $input['companyId'])
            ->where('documentSystemID', $input['documentId'])
            ->with(['created_by', 'segment_by']);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $stockReceive->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('locationFrom', $input)) {
            if ($input['locationFrom'] && !is_null($input['locationFrom'])) {
                $stockReceive->where('locationFrom', $input['locationFrom']);
            }
        }

        if (array_key_exists('locationTo', $input)) {
            if ($input['locationTo'] && !is_null($input['locationTo'])) {
                $stockReceive->where('locationTo', $input['locationTo']);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $stockReceive->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $stockReceive->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('interCompanyTransferYN', $input)) {
            if (($input['interCompanyTransferYN'] == 0 || $input['interCompanyTransferYN'] == -1) && !is_null($input['interCompanyTransferYN'])) {
                $stockReceive->where('interCompanyTransferYN', $input['interCompanyTransferYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $stockReceive->whereMonth('receivedDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $stockReceive->whereYear('receivedDate', '=', $input['year']);
            }
        }

        $stockReceive = $stockReceive->select(
            ['stockReceiveAutoID',
                'stockReceiveCode',
                'documentSystemID',
                'refNo',
                'createdDateTime',
                'createdUserSystemID',
                'comment',
                'receivedDate',
                'serviceLineSystemID',
                'confirmedDate',
                'approvedDate',
                'timesReferred',
                'confirmedYN',
                'approved',
                'approvedDate'
            ]);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $stockReceive = $stockReceive->where(function ($query) use ($search) {
                $query->where('stockReceiveCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%")
                    ->orWhere('refNo', 'LIKE', "%{$search}%");
            });
        }

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

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function stockReceiveAudit(Request $request)
    {
        $id = $request->get('id');
        $stockReceive = $this->stockReceiveRepository->getAudit($id);

        if (empty($stockReceive)) {
            return $this->sendError('Materiel Return not found');
        }

        $stockReceive->docRefNo = \Helper::getCompanyDocRefNo($stockReceive->companySystemID, $stockReceive->documentSystemID);

        return $this->sendResponse($stockReceive->toArray(), 'Stock Receive retrieved successfully');
    }

    public function printStockReceive(Request $request)
    {
        $id = $request->get('id');
        $stockReceive = $this->stockReceiveRepository->getAudit($id);

        if (empty($stockReceive)) {
            return $this->sendError('Stock Receive not found');
        }

        $stockReceive->docRefNo = \Helper::getCompanyDocRefNo($stockReceive->companySystemID, $stockReceive->documentSystemID);

        $array = array('entity' => $stockReceive);
        $time = strtotime("now");
        $fileName = 'stock_receive' . $id . '_' . $time . '.pdf';
        $html = view('print.stock_receive', $array);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream($fileName);
    }


    public function srPullFromTransferPreCheck(Request $request)
    {

        $input = $request->all();

        $id = $input['stockReceiveAutoID'];

        $stockReceive = StockReceive::find($id);

        if (empty($stockReceive)) {
            return $this->sendError('Stock Receive not found');
        }

        $validator = \Validator::make($stockReceive->toArray(), [
            'locationFrom' => 'required|numeric|min:1',
            'locationTo' => 'required|numeric|min:1',
            'companyFinancePeriodID' => 'required|numeric|min:1',
            'companyFinanceYearID' => 'required|numeric|min:1',
            'companyToSystemID' => 'required|numeric|min:1',
            'companyFromSystemID' => 'required|numeric|min:1',
            'serviceLineSystemID' => 'required|numeric|min:1',
            //'refNo' => 'required',
            //'comment' => 'required'
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
            return $this->sendError('Selected Department is not active. Please select an active segment', 500);
        }

        return $this->sendResponse($id, 'success');
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
                ->where('employeesdepartments.employeeSystemID', $empID);
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
            return $this->sendError('Stock Receive not found');
        }

        if ($stockTransfer->approved == -1) {
            return $this->sendError('You cannot reopen this Stock Receive it is already fully approved');
        }

        if ($stockTransfer->RollLevForApp_curr > 1) {
            return $this->sendError('You cannot reopen this Stock Receive it is already partially approved');
        }

        if ($stockTransfer->confirmedYN == 0) {
            return $this->sendError('You cannot reopen this Stock Receive, it is not confirmed');
        }

        $updateInput = ['confirmedYN' => 0,'confirmedByEmpSystemID' => null,'confirmedByEmpID' => null,
            'confirmedByName' => null, 'confirmedDate' => null,'RollLevForApp_curr' => 1];

        $this->stockReceiveRepository->update($updateInput,$id);

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $stockTransfer->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $stockTransfer->stockTransferCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $stockTransfer->stockTransferCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

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

        $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $stockTransfer->companySystemID)
            ->where('documentSystemID', $stockTransfer->documentSystemID)
            ->delete();

        return $this->sendResponse($stockTransfer->toArray(), 'Stock Receive reopened successfully');
    }



}
