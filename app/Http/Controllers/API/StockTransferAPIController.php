<?php
/**
 * =============================================
 * -- File Name : StockTransferAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Stock Transfer
 * -- Author : Mohamed Nazir
 * -- Create date : 13-July 2018
 * -- Description : This file contains the all CRUD for Stock Transfer
 * -- REVISION HISTORY
 * -- Date: 13-July 2018 By: Nazir Description: Added new functions named as getStockTransferMasterView() For load Master View
 * -- Date: 24-July 2018 By: Fayas Description: Added new functions named as getStockTransferForReceive(),getStockTransferDetailsByMaster()
 * -- Date: 30-July 2018 By: Fayas Description: Added new functions named as printStockTransfer()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockTransferAPIRequest;
use App\Http\Requests\API\UpdateStockTransferAPIRequest;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\DocumentMaster;
use App\Models\ItemAssigned;
use App\Models\Months;
use App\Models\Company;
use App\Models\SegmentMaster;
use App\Models\StockReceive;
use App\Models\StockReceiveDetails;
use App\Models\StockTransfer;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Models\StockTransferDetails;
use App\Repositories\StockTransferRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Response;

/**
 * Class StockTransferController
 * @package App\Http\Controllers\API
 */

class StockTransferAPIController extends AppBaseController
{
    /** @var  StockTransferRepository */
    private $stockTransferRepository;
    private $userRepository;

    public function __construct(StockTransferRepository $stockTransferRepo, UserRepository $userRepo)
    {
        $this->stockTransferRepository = $stockTransferRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockTransfers",
     *      summary="Get a listing of the StockTransfers.",
     *      tags={"StockTransfer"},
     *      description="Get all StockTransfers",
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
     *                  @SWG\Items(ref="#/definitions/StockTransfer")
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
        $this->stockTransferRepository->pushCriteria(new RequestCriteria($request));
        $this->stockTransferRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockTransfers = $this->stockTransferRepository->all();

        return $this->sendResponse($stockTransfers->toArray(), 'Stock Transfers retrieved successfully');
    }

    /**
     * @param CreateStockTransferAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockTransfers",
     *      summary="Store a newly created StockTransfer in storage",
     *      tags={"StockTransfer"},
     *      description="Store StockTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockTransfer that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockTransfer")
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
     *                  ref="#/definitions/StockTransfer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockTransferAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $companyFinancePeriod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();

        if ($companyFinancePeriod) {
            $input['FYBiggin'] = $companyFinancePeriod->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod->dateTo;
        }

        if (isset($input['tranferDate'])) {
            if ($input['tranferDate']) {
                $input['tranferDate'] = new Carbon($input['tranferDate']);
            }
        }

        $documentDate = $input['tranferDate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('Transfer Date not between Financial period !',500);
        }

        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];

        $lastSerial = StockTransfer::where('companySystemID', $input['companySystemID'])
            ->orderBy('stockTransferAutoID', 'desc')
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

        $companyfinanceyear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        if ($companyfinanceyear) {
            $startYear = $companyfinanceyear['bigginingDate'];
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
            $input['stockTransferCode'] = $stockTransferCode;
        }

        $stockTransfers = $this->stockTransferRepository->create($input);

        return $this->sendResponse($stockTransfers->toArray(), 'Stock Transfer saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockTransfers/{id}",
     *      summary="Display the specified StockTransfer",
     *      tags={"StockTransfer"},
     *      description="Get StockTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransfer",
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
     *                  ref="#/definitions/StockTransfer"
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
        /** @var StockTransfer $stockTransfer */
        $stockTransfer = $this->stockTransferRepository->with(['created_by', 'confirmed_by', 'segment_by'])->findWithoutFail($id);

        if (empty($stockTransfer)) {
            return $this->sendError('Stock Transfer not found');
        }

        return $this->sendResponse($stockTransfer->toArray(), 'Stock Transfer retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateStockTransferAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockTransfers/{id}",
     *      summary="Update the specified StockTransfer in storage",
     *      tags={"StockTransfer"},
     *      description="Update StockTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransfer",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockTransfer that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockTransfer")
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
     *                  ref="#/definitions/StockTransfer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockTransferAPIRequest $request)
    {
        $userId = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($userId);

        $input = $request->all();

        $input = array_except($input, ['created_by', 'confirmed_by', 'segment_by']);
        $input = $this->convertArrayToValue($input);

        /** @var StockTransfer $stockTransfer */
        $stockTransfer = $this->stockTransferRepository->findWithoutFail($id);

        if (empty($stockTransfer)) {
            return $this->sendError('Stock Transfer not found');
        }

        $companyFinancePeriod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();

        if ($companyFinancePeriod) {
            $input['FYBiggin'] = $companyFinancePeriod->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod->dateTo;
        }

        if (isset($input['tranferDate'])) {
            if ($input['tranferDate']) {
                $input['tranferDate'] = new Carbon($input['tranferDate']);
            }
        }

        $documentDate = $input['tranferDate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('Transfer Date not between Financial period !');
        }

        //checking selected segment is active
        $segments = SegmentMaster::where("serviceLineSystemID", $input['serviceLineSystemID'])
            ->where('companySystemID', $input['companySystemID'])
            ->where('isActive', 1)
            ->first();

        if (empty($segments)) {
            return $this->sendError('Selected segment is not active. Please select an active segment');
        }

        if ($input['locationFrom'] == $input['locationTo']) {
            return $this->sendError('Location From and Location To  cannot me same');
        }

        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $companyTo = Company::where('companySystemID', $input['companyToSystemID'])->first();
        if ($companyTo) {
            $input['companyTo'] = $companyTo->CompanyID;
        }

        if ($input['interCompanyTransferYN']) {
            $input['interCompanyTransferYN'] = -1;
        } else {
            $input['interCompanyTransferYN'] = 0;
        }

        if ($stockTransfer->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            $stockTransDetailExist = StockTransferDetails::select(DB::raw('stockTransferDetailsID'))
                ->where('stockTransferAutoID', $input['stockTransferAutoID'])
                ->first();

            if (empty($stockTransDetailExist)) {
                return $this->sendError('Stock Transfer document cannot confirm without details');
            }

            $checkQuantity = StockTransferDetails::where('stockTransferAutoID', $id)
                ->where('qty', '<', 1)
                ->count();

            if ($checkQuantity > 0) {
                return $this->sendError('Every item should have at least one minimum Qty', 500);
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

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $user->employee['empID'];
        $input['modifiedUserSystemID'] = $user->employee['employeeSystemID'];


        $stockTransfer = $this->stockTransferRepository->update($input, $id);

        return $this->sendResponse($stockTransfer->toArray(), 'StockTransfer updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockTransfers/{id}",
     *      summary="Remove the specified StockTransfer from storage",
     *      tags={"StockTransfer"},
     *      description="Delete StockTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransfer",
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
        /** @var StockTransfer $stockTransfer */
        $stockTransfer = $this->stockTransferRepository->findWithoutFail($id);

        if (empty($stockTransfer)) {
            return $this->sendError('Stock Transfer not found');
        }

        $stockTransfer->delete();

        return $this->sendResponse($id, 'Stock Transfer deleted successfully');
    }

    public function getStockTransferMasterView(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'grvLocation', 'poCancelledYN', 'poConfirmedYN', 'approved', 'grvRecieved', 'month', 'year', 'invoicedBooked'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $stockTransferMaster = StockTransfer::where('companySystemID', $input['companyId']);
        $stockTransferMaster->where('documentSystemID', $input['documentId']);
        $stockTransferMaster->with(['created_by' => function ($query) {
        }, 'segment_by' => function ($query) {
        }]);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $stockTransferMaster->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $stockTransferMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $stockTransferMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $stockTransferMaster->whereMonth('tranferDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $stockTransferMaster->whereYear('tranferDate', '=', $input['year']);
            }
        }

        $stockTransferMaster = $stockTransferMaster->select(
            ['erp_stocktransfer.stockTransferAutoID',
                'erp_stocktransfer.stockTransferCode',
                'erp_stocktransfer.documentSystemID',
                'erp_stocktransfer.refNo',
                'erp_stocktransfer.createdDateTime',
                'erp_stocktransfer.createdUserSystemID',
                'erp_stocktransfer.comment',
                'erp_stocktransfer.tranferDate',
                'erp_stocktransfer.serviceLineSystemID',
                'erp_stocktransfer.confirmedDate',
                'erp_stocktransfer.approvedDate',
                'erp_stocktransfer.timesReferred',
                'erp_stocktransfer.confirmedYN',
                'erp_stocktransfer.approved',
                'erp_stocktransfer.approvedDate'
            ]);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $stockTransferMaster = $stockTransferMaster->where(function ($query) use ($search) {
                $query->where('stockTransferCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%")
                    ->orWhere('refNo', 'LIKE', "%{$search}%");
            });
        }

        $policy = 0;

        return \DataTables::eloquent($stockTransferMaster)
            ->addColumn('Actions', $policy)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('stockTransferAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getStockTransferFormData(Request $request)
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

        $years = StockTransfer::select(DB::raw("YEAR(createdDateTime) as year"))
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

    public function getItemsOptionForStockTransfer(Request $request)
    {
        $input = $request->all();

        $companyId = $input['companyId'];

        $items = ItemAssigned::where('companySystemID', $companyId);
        $items = $items->where('financeCategoryMaster', 1);

        if (array_key_exists('search', $input)) {

            $search = $input['search'];

            $items = $items->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%");
            });
        }

        $items = $items
            ->take(20)
            ->get();

        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');
    }

    public function StockTransferAudit(Request $request)
    {
        $id = $request->get('id');

        $stockTransfer = $this->stockTransferRepository->getAudit($id);

        if (empty($stockTransfer)) {
            return $this->sendError('Stock Transfer not found');
        }

        $stockTransfer->docRefNo = \Helper::getCompanyDocRefNo($stockTransfer->companySystemID,$stockTransfer->documentSystemID);

        return $this->sendResponse($stockTransfer->toArray(), 'Stock Transfer retrieved successfully');
    }

    public function printStockTransfer(Request $request)
    {
        $id = $request->get('id');
        $stockTransfer = $this->stockTransferRepository->getAudit($id);

        if (empty($stockTransfer)) {
            return $this->sendError('Stock Transfer not found');
        }

        $stockTransfer->docRefNo = \Helper::getCompanyDocRefNo($stockTransfer->companySystemID,$stockTransfer->documentSystemID);

        $array = array('entity' => $stockTransfer);
        $time = strtotime("now");
        $fileName = 'stock_transfer' . $id . '_' . $time . '.pdf';
        $html = view('print.stock_transfer', $array);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream($fileName);
    }


    public function getStockTransferApproval(Request $request)
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
            ->where('documentSystemID', 13)
            ->first();

        $stockTransferMasters = DB::table('erp_documentapproved')->select(
            'erp_stocktransfer.stockTransferAutoID',
            'erp_stocktransfer.stockTransferCode',
            'erp_stocktransfer.documentSystemID',
            'erp_stocktransfer.refNo',
            'erp_stocktransfer.tranferDate',
            'erp_stocktransfer.comment',
            'erp_stocktransfer.serviceLineCode',
            'erp_stocktransfer.createdDateTime',
            'erp_stocktransfer.confirmedDate',
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
                $query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
            }
            $query->where('employeesdepartments.documentSystemID', 13)
                ->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID);
        })->join('erp_stocktransfer', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'stockTransferAutoID')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('erp_stocktransfer.companySystemID', $companyID)
                ->where('erp_stocktransfer.approved', 0)
                ->where('erp_stocktransfer.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', 0)
            ->join('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->join('serviceline', 'erp_stocktransfer.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 13)
            ->where('erp_documentapproved.companySystemID', $companyID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $stockTransferMasters = $stockTransferMasters->where(function ($query) use ($search) {
                $query->where('stockTransferCode', 'LIKE', "%{$search}%")
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

    public function getApprovedSTForCurrentUser(Request $request)
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
            'erp_stocktransfer.stockTransferAutoID',
            'erp_stocktransfer.stockTransferCode',
            'erp_stocktransfer.documentSystemID',
            'erp_stocktransfer.refNo',
            'erp_stocktransfer.tranferDate',
            'erp_stocktransfer.comment',
            'erp_stocktransfer.serviceLineCode',
            'erp_stocktransfer.createdDateTime',
            'erp_stocktransfer.confirmedDate',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user',
            'serviceline.ServiceLineDes as serviceLineDescription'
        )->join('erp_stocktransfer', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'stockTransferAutoID')
                ->where('erp_stocktransfer.companySystemID', $companyID)
                ->where('erp_stocktransfer.approved', -1)
                ->where('erp_stocktransfer.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', -1)
            ->join('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->join('serviceline', 'erp_stocktransfer.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.documentSystemID', 13)
            ->where('erp_documentapproved.companySystemID', $companyID)->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $stockTransferMasters = $stockTransferMasters->where(function ($query) use ($search) {
                $query->where('stockTransferCode', 'LIKE', "%{$search}%")
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

    public function approveStockTransfer(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectStockTransfer(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }

    }

    public function getStockTransferForReceive(Request $request)
    {

        $input = $request->all();
        $id = $input['stockReceiveAutoID'];
        $stockReceive = StockReceive::find($id);
        if (empty($stockReceive)) {
            return $this->sendError('Stock Receive not found');
        }

       $stockTransfers =  StockTransfer::where('companyToSystemID',$stockReceive->companyToSystemID)
                                        ->where('companyFromSystemID',$stockReceive->companyFromSystemID)
                                        //->where('locationTo',$stockReceive->locationTo)
                                        //->where('locationFrom',$stockReceive->locationFrom)
                                        ->where('approved',-1)
                                        ->where('fullyReceived',0)
                                        ->orderby('createdDateTime', 'desc')
                                        ->get();

        return $this->sendResponse($stockTransfers->toArray(), 'Stock Transfer retrieved successfully');
    }

    public function getStockTransferDetailsByMaster(Request $request)
    {

        $input = $request->all();
        $id = $input['stockTransferAutoID'];
        $stockTransfer = StockTransfer::find($id);
        if (empty($stockTransfer)) {
            return $this->sendError('Stock Transfer not found');
        }
        $stockTransferDetails  = StockTransferDetails::where('stockTransferAutoID',$id)->with(['unit_by'])
                                                    ->where('stockRecieved',0)
                                                    ->get();

        return $this->sendResponse($stockTransferDetails->toArray(), 'Stock Transfer retrieved successfully');
    }

}
