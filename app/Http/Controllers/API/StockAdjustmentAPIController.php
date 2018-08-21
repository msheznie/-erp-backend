<?php
/**
 * =============================================
 * -- File Name : StockAdjustmentAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Stock Adjustment
 * -- Author : Mohamed Fayas
 * -- Create date : 20 - August 2018
 * -- Description : This file contains the all CRUD for Stock Adjustment
 * -- REVISION HISTORY
 * -- Date: 21 - August 2018 By: Fayas Description: Added new functions named as getAllStockAdjustmentsByCompany(),getStockAdjustmentFormData(),
 *                        getStockAdjustmentAudit()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockAdjustmentAPIRequest;
use App\Http\Requests\API\UpdateStockAdjustmentAPIRequest;
use App\Models\Company;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\DocumentMaster;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\StockAdjustment;
use App\Models\Unit;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\StockAdjustmentRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StockAdjustmentController
 * @package App\Http\Controllers\API
 */

class StockAdjustmentAPIController extends AppBaseController
{
    /** @var  StockAdjustmentRepository */
    private $stockAdjustmentRepository;

    public function __construct(StockAdjustmentRepository $stockAdjustmentRepo)
    {
        $this->stockAdjustmentRepository = $stockAdjustmentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockAdjustments",
     *      summary="Get a listing of the StockAdjustments.",
     *      tags={"StockAdjustment"},
     *      description="Get all StockAdjustments",
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
     *                  @SWG\Items(ref="#/definitions/StockAdjustment")
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
        $this->stockAdjustmentRepository->pushCriteria(new RequestCriteria($request));
        $this->stockAdjustmentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockAdjustments = $this->stockAdjustmentRepository->all();

        return $this->sendResponse($stockAdjustments->toArray(), 'Stock Adjustments retrieved successfully');
    }

    /**
     * @param CreateStockAdjustmentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockAdjustments",
     *      summary="Store a newly created StockAdjustment in storage",
     *      tags={"StockAdjustment"},
     *      description="Store StockAdjustment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockAdjustment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockAdjustment")
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
     *                  ref="#/definitions/StockAdjustment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockAdjustmentAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        $input['createdPCid'] = gethostname();
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
        } else {
            $input['FYBiggin'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod["message"]->dateTo;
        }
        unset($inputParam);

        if (isset($input['stockAdjustmentDate'])) {
            if ($input['stockAdjustmentDate']) {
                $input['stockAdjustmentDate'] = new Carbon($input['stockAdjustmentDate']);
            }
        }

        $documentDate = $input['stockAdjustmentDate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];
        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('Document date is not within the selected financial period !', 500);
        }

        $input['documentSystemID'] = 7;
        $input['documentID'] = 'SA';

        $lastSerial = StockAdjustment::where('companySystemID', $input['companySystemID'])
                                    ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                                    ->orderBy('stockAdjustmentAutoID', 'desc')
                                    ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }


        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }else{
            return $this->sendError('Service Line not found',500);
        }

        $warehouse = WarehouseMaster::where('wareHouseSystemCode', $input['location'])->first();
        if (empty($warehouse)) {
            return $this->sendError('Location not found',500);
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
            $stockAdjustmentCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['stockAdjustmentCode'] = $stockAdjustmentCode;
        }

        $stockAdjustments = $this->stockAdjustmentRepository->create($input);

        return $this->sendResponse($stockAdjustments->toArray(), 'Stock Adjustment saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockAdjustments/{id}",
     *      summary="Display the specified StockAdjustment",
     *      tags={"StockAdjustment"},
     *      description="Get StockAdjustment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockAdjustment",
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
     *                  ref="#/definitions/StockAdjustment"
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
        /** @var StockAdjustment $stockAdjustment */
        $stockAdjustment = $this->stockAdjustmentRepository->with(['confirmed_by', 'created_by', 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }])->findWithoutFail($id);

        if (empty($stockAdjustment)) {
            return $this->sendError('Stock Adjustment not found');
        }

        return $this->sendResponse($stockAdjustment->toArray(), 'Stock Adjustment retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateStockAdjustmentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockAdjustments/{id}",
     *      summary="Update the specified StockAdjustment in storage",
     *      tags={"StockAdjustment"},
     *      description="Update StockAdjustment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockAdjustment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockAdjustment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockAdjustment")
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
     *                  ref="#/definitions/StockAdjustment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockAdjustmentAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmedByName', 'finance_period_by', 'finance_year_by',
            'confirmedByEmpID', 'confirmedDate', 'confirmed_by', 'confirmedByEmpSystemID']);

        $input = $this->convertArrayToValue($input);
        $wareHouseError = array('type' => 'wareHouse');
        $serviceLineError = array('type' => 'serviceLine');


        /** @var StockAdjustment $stockAdjustment */
        $stockAdjustment = $this->stockAdjustmentRepository->findWithoutFail($id);

        if (empty($stockAdjustment)) {
            return $this->sendError('Stock Adjustment not found');
        }

        if ($input['serviceLineSystemID']) {
            $checkDepartmentActive = SegmentMaster::find($input['serviceLineSystemID']);
            if (empty($checkDepartmentActive)) {
                return $this->sendError('Service Line not found');
            }

            if ($checkDepartmentActive->isActive == 0) {
                $this->stockAdjustmentRepository->update(["serviceLineSystemID" => null,"serviceLineCode" => null],$id);
                return $this->sendError('Please select a active service line', 500,$serviceLineError);
            }
        }

        if ($input['location']) {
            $checkWareHouseActive = WarehouseMaster::find($input['location']);
            if (empty($checkWareHouseActive)) {
                return $this->sendError('Location not found', 500, $wareHouseError);
            }

            if ($checkWareHouseActive->isActive == 0) {
                $this->stockAdjustmentRepository->update(["location" => null],$id);
                return $this->sendError('Please select a active location', 500, $wareHouseError);
            }
        }

        if (isset($input['stockAdjustmentDate'])) {
            if ($input['stockAdjustmentDate']) {
                $input['stockAdjustmentDate'] = new Carbon($input['stockAdjustmentDate']);
            }
        }

        $employee = \Helper::getEmployeeInfo();

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;

        $stockAdjustment = $this->stockAdjustmentRepository->update($input, $id);

        return $this->sendResponse($stockAdjustment->toArray(), 'StockAdjustment updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockAdjustments/{id}",
     *      summary="Remove the specified StockAdjustment from storage",
     *      tags={"StockAdjustment"},
     *      description="Delete StockAdjustment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockAdjustment",
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
        /** @var StockAdjustment $stockAdjustment */
        $stockAdjustment = $this->stockAdjustmentRepository->findWithoutFail($id);

        if (empty($stockAdjustment)) {
            return $this->sendError('Stock Adjustment not found');
        }

        $stockAdjustment->delete();

        return $this->sendResponse($id, 'Stock Adjustment deleted successfully');
    }


    /**
     * get All Stock Adjustments By Company
     * POST /getAllStockAdjustmentsByCompany
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getAllStockAdjustmentsByCompany(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'location', 'month', 'year'));

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

        $stockAdjustments = StockAdjustment::whereIn('companySystemID', $subCompanies)
            ->with(['created_by', 'warehouse_by', 'segment_by']);


        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $stockAdjustments->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $stockAdjustments->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $stockAdjustments->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('location', $input)) {
            if ($input['location'] && !is_null($input['location'])) {
                $stockAdjustments->where('location', $input['location']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $stockAdjustments->whereMonth('stockAdjustmentDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $stockAdjustments->whereYear('stockAdjustmentDate', '=', $input['year']);
            }
        }


        $stockAdjustments = $stockAdjustments->select(
            ['erp_stockadjustment.stockAdjustmentAutoID',
                'erp_stockadjustment.stockAdjustmentCode',
                'erp_stockadjustment.comment',
                'erp_stockadjustment.stockAdjustmentDate',
                'erp_stockadjustment.confirmedYN',
                'erp_stockadjustment.approved',
                'erp_stockadjustment.serviceLineSystemID',
                'erp_stockadjustment.documentSystemID',
                'erp_stockadjustment.confirmedByEmpSystemID',
                'erp_stockadjustment.createdUserSystemID',
                'erp_stockadjustment.confirmedDate',
                'erp_stockadjustment.createdDateTime',
                'erp_stockadjustment.refNo',
                'erp_stockadjustment.location'
            ]);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $stockAdjustments = $stockAdjustments->where(function ($query) use ($search) {
                $query->where('stockAdjustmentCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($stockAdjustments)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('stockAdjustmentAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * get Stock Adjustment Form Data
     * Get /getStockAdjustmentFormData
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getStockAdjustmentFormData(Request $request)
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

        $years = StockAdjustment::select(DB::raw("YEAR(createdDateTime) as year"))
                                ->whereNotNull('createdDateTime')
                                ->groupby('year')
                                ->orderby('year', 'desc')
                                ->get();

        $wareHouseLocation = WarehouseMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $wareHouseLocation = $wareHouseLocation->where('isActive', 1);
        }
        $wareHouseLocation = $wareHouseLocation->get();

        $companyPolicy = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 22)
            ->first();

        $typeId = [];

        if (!empty($companyPolicy)) {
            if ($companyPolicy->isYesNO == 0) {
                $typeId = [2];
            } else if ($companyPolicy->isYesNO == 1) {
                $typeId = [1];
            }
        }

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
            'companyFinanceYear' => $companyFinanceYear,
            'contracts' => $contracts,
            'units' => $units
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    /**
     * Display the specified Stock Adjustment Audit.
     * GET|HEAD /getStockAdjustmentAudit
     *
     * @param  int $id
     *
     * @return Response
     */
    public function getStockAdjustmentAudit(Request $request)
    {
        $id = $request->get('id');
        $stockAdjustment = $this->stockAdjustmentRepository->getAudit($id);

        if (empty($stockAdjustment)) {
            return $this->sendError('Stock Adjustment not found');
        }

        $stockAdjustment->docRefNo = \Helper::getCompanyDocRefNo($stockAdjustment->companySystemID, $stockAdjustment->documentSystemID);

        return $this->sendResponse($stockAdjustment->toArray(), 'Stock Adjustment retrieved successfully');
    }

}
