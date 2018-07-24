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
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockReceiveAPIRequest;
use App\Http\Requests\API\UpdateStockReceiveAPIRequest;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\DocumentMaster;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\StockReceive;
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

        $companyFinancePeriod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();

        if ($companyFinancePeriod) {
            $input['FYBiggin'] = $companyFinancePeriod->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod->dateTo;
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
            return $this->sendError('Receive Date not between Financial period !');
        }

        $lastSerial = StockReceive::where('companySystemID', $input['companySystemID'])
                                    ->orderBy('stockReceiveAutoID', 'desc')
                                    ->first();

        $lastSerialNumber = 0;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        //checking selected segment is active
        $segments = SegmentMaster::where("serviceLineSystemID", $input['serviceLineSystemID'])
            ->where('companySystemID', $input['companySystemID'])
            ->where('isActive', 1)
            ->first();

        if (empty($segments)) {
            return $this->sendError('Selected segment is not active. Please select an active segment',500);
        }

        if ($input['locationFrom'] == $input['locationTo']) {
            return $this->sendError('Location From and Location To  cannot me same',500);
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
        $stockReceive = $this->stockReceiveRepository->findWithoutFail($id);

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

        /** @var StockReceive $stockReceive */
        $stockReceive = $this->stockReceiveRepository->findWithoutFail($id);

        if (empty($stockReceive)) {
            return $this->sendError('Stock Receive not found');
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
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'locationFrom','locationTo','confirmedYN', 'approved',
            'grvRecieved', 'month', 'year', 'invoicedBooked'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $stockReceive = StockReceive::where('companySystemID', $input['companyId'])
                                    ->where('documentSystemID', $input['documentId'])
                                    ->with(['created_by','segment_by']);

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

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear"));
        $companyFinanceYear = $companyFinanceYear->where('companySystemID', $companyId);
        if (isset($request['type']) && $request['type'] == 'add') {
            $companyFinanceYear = $companyFinanceYear->where('isActive', -1);
        }
        $companyFinanceYear = $companyFinanceYear->get();

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

        $stockReceive = $this->stockReceiveRepository->with(['created_by', 'confirmed_by',
                                                               'modified_by', 'approved_by' => function ($query) {
                                                                    $query->with('employee')
                                                                        ->where('documentSystemID', 10);
                                                                }])->findWithoutFail($id);

        if (empty($stockReceive)) {
            return $this->sendError('Stock Receive not found');
        }

        return $this->sendResponse($stockReceive->toArray(), 'Stock Receive retrieved successfully');
    }

    public function srPullFromTransferPreCheck(Request $request)
    {

        $input = $request->all();

        $id = $input['stockReceiveAutoID'];

        $stockReceive = StockReceive::find($id);

        if (empty($stockReceive)) {
            return $this->sendError('Stock Receive not found');
        }

        //checking segment is active

        $segments = SegmentMaster::where("serviceLineSystemID", $stockReceive->serviceLineSystemID)
                                    ->where('companySystemID', $input['companySystemID'])
                                    ->where('isActive', 1)
                                    ->first();

        if (empty($segments)) {
            return $this->sendError('Selected Department is not active. Please select an active segment',500);
        }

        return $this->sendResponse($id, 'success');
    }

}
