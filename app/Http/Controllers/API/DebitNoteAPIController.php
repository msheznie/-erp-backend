<?php
/**
 * =============================================
 * -- File Name : DebitNoteAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  DebitNote
 * -- Author : Mohamed Nazir
 * -- Create date : 16 - August 2018
 * -- Description : This file contains the all CRUD for Debit Note
 * -- REVISION HISTORY
 * -- Date: 08-August 2018 By: Nazir Description: Added new function getDebitNoteMasterRecord()
 * -- Date: 04-September 2018 By: Fayas Description: Added new function getAllDebitNotes(),getDebitNoteFormData()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDebitNoteAPIRequest;
use App\Http\Requests\API\UpdateDebitNoteAPIRequest;
use App\Models\Company;
use App\Models\CompanyFinanceYear;
use App\Models\DebitNote;
use App\Models\DebitNoteDetails;
use App\Models\DocumentMaster;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\DebitNoteRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DebitNoteController
 * @package App\Http\Controllers\API
 */
class DebitNoteAPIController extends AppBaseController
{
    /** @var  DebitNoteRepository */
    private $debitNoteRepository;

    public function __construct(DebitNoteRepository $debitNoteRepo)
    {
        $this->debitNoteRepository = $debitNoteRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/debitNotes",
     *      summary="Get a listing of the DebitNotes.",
     *      tags={"DebitNote"},
     *      description="Get all DebitNotes",
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
     *                  @SWG\Items(ref="#/definitions/DebitNote")
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
        $this->debitNoteRepository->pushCriteria(new RequestCriteria($request));
        $this->debitNoteRepository->pushCriteria(new LimitOffsetCriteria($request));
        $debitNotes = $this->debitNoteRepository->all();

        return $this->sendResponse($debitNotes->toArray(), 'Debit Notes retrieved successfully');
    }

    /**
     * @param CreateDebitNoteAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/debitNotes",
     *      summary="Store a newly created DebitNote in storage",
     *      tags={"DebitNote"},
     *      description="Store DebitNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DebitNote that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DebitNote")
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
     *                  ref="#/definitions/DebitNote"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDebitNoteAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            return $this->sendError($companyFinanceYear["message"], 500);
        } else {
            $input['FYBiggin'] = $companyFinanceYear["message"]->bigginingDate;
            $input['FYEnd'] = $companyFinanceYear["message"]->endingDate;
        }

        $inputParam = $input;
        $inputParam["departmentSystemID"] = 1;
        $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
        if (!$companyFinancePeriod["success"]) {
            return $this->sendError($companyFinancePeriod["message"], 500);
        } else {
            $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
        }
        unset($inputParam);

        $validator = \Validator::make($input, [
            'companyFinancePeriodID' => 'required|numeric|min:1',
            'companyFinanceYearID' => 'required|numeric|min:1',
            'debitNoteDate' => 'required',
            'supplierID' => 'required|numeric|min:1',
            'supplierTransactionCurrencyID' => 'required|numeric|min:1',
            'comments' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if (isset($input['debitNoteDate'])) {
            if ($input['debitNoteDate']) {
                $input['debitNoteDate'] = new Carbon($input['debitNoteDate']);
            }
        }
        $documentDate = $input['debitNoteDate'];
        $monthBegin = $input['FYPeriodDateFrom'];
        $monthEnd = $input['FYPeriodDateTo'];
        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('Document date is not within the selected financial period !', 500);
        }

        $input['documentSystemID'] = 15;
        $input['documentID'] = 'DN';

        $lastSerial = DebitNote::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('debitNoteAutoID', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();

        if (empty($company)) {
            return $this->sendError('Company not found', 500);
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], 0);

        $input['supplierTransactionCurrencyER'] = 1;
        $input['companyID'] = $company->CompanyID;
        $input['companyReportingCurrencyID'] = $company->reportingCurrency;
        $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
        $input['localCurrencyID'] = $company->localCurrencyID;
        $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];

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

        $supplier = SupplierMaster::where("supplierCodeSystem", $input["supplierID"])->first();

        if (!empty($supplier)) {
            $input["supplierGLCodeSystemID"] = $supplier->liabilityAccountSysemID;
            $input["supplierGLCode"] = $supplier->liabilityAccount;
        }

        if ($documentMaster) {
            $code = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['debitNoteCode'] = $code;
        }

        $debitNotes = $this->debitNoteRepository->create($input);

        return $this->sendResponse($debitNotes->toArray(), 'Debit Note saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/debitNotes/{id}",
     *      summary="Display the specified DebitNote",
     *      tags={"DebitNote"},
     *      description="Get DebitNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNote",
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
     *                  ref="#/definitions/DebitNote"
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
        /** @var DebitNote $debitNote */
        $debitNote = $this->debitNoteRepository->with(['confirmed_by', 'created_by', 'supplier', 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }])->findWithoutFail($id);

        if (empty($debitNote)) {
            return $this->sendError('Debit Note not found');
        }

        return $this->sendResponse($debitNote->toArray(), 'Debit Note retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDebitNoteAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/debitNotes/{id}",
     *      summary="Update the specified DebitNote in storage",
     *      tags={"DebitNote"},
     *      description="Update DebitNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNote",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DebitNote that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DebitNote")
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
     *                  ref="#/definitions/DebitNote"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDebitNoteAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmedByName', 'finance_period_by', 'finance_year_by', 'supplier',
            'confirmedByEmpID', 'confirmedDate', 'confirmed_by', 'confirmedByEmpSystemID']);

        $input = $this->convertArrayToValue($input);
        /** @var DebitNote $debitNote */
        $debitNote = $this->debitNoteRepository->findWithoutFail($id);

        if (empty($debitNote)) {
            return $this->sendError('Debit Note not found');
        }

        $supplier = SupplierMaster::where("supplierCodeSystem", $input["supplierID"])->first();

        if (!empty($supplier)) {
            $input["supplierGLCodeSystemID"] = $supplier->liabilityAccountSysemID;
            $input["supplierGLCode"] = $supplier->liabilityAccount;
        }

        if (isset($input['debitNoteDate'])) {
            if ($input['debitNoteDate']) {
                $input['debitNoteDate'] = new Carbon($input['debitNoteDate']);
            }
        }

        if ($debitNote->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
            if (!$companyFinanceYear["success"]) {
                return $this->sendError($companyFinanceYear["message"], 500);
            } else {
                $input['FYBiggin'] = $companyFinanceYear["message"]->bigginingDate;
                $input['FYEnd'] = $companyFinanceYear["message"]->endingDate;
            }

            $inputParam = $input;
            $inputParam["departmentSystemID"] = 1;
            $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
            if (!$companyFinancePeriod["success"]) {
                return $this->sendError($companyFinancePeriod["message"], 500);
            } else {
                $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
                $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
            }
            unset($inputParam);

            $validator = \Validator::make($input, [
                'companyFinancePeriodID' => 'required|numeric|min:1',
                'companyFinanceYearID' => 'required|numeric|min:1',
                'debitNoteDate' => 'required',
                'supplierID' => 'required|numeric|min:1',
                'supplierTransactionCurrencyID' => 'required|numeric|min:1',
                'comments' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $documentDate = $input['debitNoteDate'];
            $monthBegin = $input['FYPeriodDateFrom'];
            $monthEnd = $input['FYPeriodDateTo'];
            if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
            } else {
                return $this->sendError('Document date is not within the selected financial period !', 500);
            }

            $checkItems = DebitNoteDetails::where('debitNoteAutoID', $id)
                ->count();
            if ($checkItems == 0) {
                return $this->sendError('Every debit note should have at least one item', 500);
            }

            $checkQuantity = DebitNoteDetails::where('debitNoteAutoID', $id)
                ->where(function ($q) {
                    $q->where('debitAmount', '<=', 0)
                        ->orWhereNull('localAmount', '<=', 0)
                        ->orWhereNull('comRptAmount', '<=', 0)
                        ->orWhereNull('debitAmount')
                        ->orWhereNull('localAmount')
                        ->orWhereNull('comRptAmount');
                })
                ->count();
            if ($checkQuantity > 0) {
                return $this->sendError('Amount should be greater than 0 for every items', 500);
            }

            $itemIssueDetails = DebitNoteDetails::where('debitNoteAutoID', $id)->get();

            /* $finalError = array('cost_zero' => array(),
                 'cost_neg' => array(),
                 'currentStockQty_zero' => array(),
                 'currentWareHouseStockQty_zero' => array(),
                 'currentStockQty_more' => array(),
                 'currentWareHouseStockQty_more' => array(),
                 'issuingQty_more_requested' => array()
             );
             $error_count = 0;

             foreach ($itemIssueDetails as $item) {
                 $updateItem = DebitNoteDetails::find($item['itemIssueDetailID']);
                 $data = array('companySystemID' => $debitNote->companySystemID,
                     'itemCodeSystem' => $updateItem->itemCodeSystem,
                     'wareHouseId' => $debitNote->wareHouseFrom);
                 $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);
                 $updateItem->currentStockQty = $itemCurrentCostAndQty['currentStockQty'];
                 $updateItem->currentWareHouseStockQty = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                 $updateItem->currentStockQtyInDamageReturn = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];
                 $updateItem->issueCostLocal = $itemCurrentCostAndQty['wacValueLocal'];
                 $updateItem->issueCostRpt = $itemCurrentCostAndQty['wacValueReporting'];
                 $updateItem->issueCostLocalTotal = $itemCurrentCostAndQty['wacValueLocal'] * $updateItem->qtyIssuedDefaultMeasure;
                 $updateItem->issueCostRptTotal = $itemCurrentCostAndQty['wacValueReporting'] * $updateItem->qtyIssuedDefaultMeasure;
                 $updateItem->save();

                 if ($updateItem->issueCostLocal == 0 || $updateItem->issueCostRpt == 0) {
                     array_push($finalError['cost_zero'], $updateItem->itemPrimaryCode);
                     $error_count++;
                 }
                 if ($updateItem->issueCostLocal < 0 || $updateItem->issueCostRpt < 0) {
                     array_push($finalError['cost_neg'], $updateItem->itemPrimaryCode);
                     $error_count++;
                 }
                 if ($updateItem->currentWareHouseStockQty <= 0) {
                     array_push($finalError['currentStockQty_zero'], $updateItem->itemPrimaryCode);
                     $error_count++;
                 }
                 if ($updateItem->currentStockQty <= 0) {
                     array_push($finalError['currentWareHouseStockQty_zero'], $updateItem->itemPrimaryCode);
                     $error_count++;
                 }
                 if ($updateItem->qtyIssuedDefaultMeasure > $updateItem->currentStockQty) {
                     array_push($finalError['currentStockQty_more'], $updateItem->itemPrimaryCode);
                     $error_count++;
                 }

                 if ($updateItem->qtyIssuedDefaultMeasure > $updateItem->currentWareHouseStockQty) {
                     array_push($finalError['currentWareHouseStockQty_more'], $updateItem->itemPrimaryCode);
                     $error_count++;
                 }

             }

             $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
             if ($error_count > 0) {
                 return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
             }*/

            $amount = DebitNoteDetails::where('debitNoteAutoID', $id)
                ->sum('debitAmount');

            $input['debitAmountTrans'] = $amount;

            $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $amount);

            $input['debitAmountLocal'] = $companyCurrencyConversion['localAmount'];
            $input['debitAmountRpt'] = $companyCurrencyConversion['reportingAmount'];
            $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
            $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];

            $input['RollLevForApp_curr'] = 1;
            $params = array('autoID' => $id,
                'company' => $debitNote->companySystemID,
                'document' => $debitNote->documentSystemID,
                'segment' => 0,
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

        $debitNote = $this->debitNoteRepository->update($input, $id);

        return $this->sendResponse($debitNote->toArray(), 'DebitNote updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/debitNotes/{id}",
     *      summary="Remove the specified DebitNote from storage",
     *      tags={"DebitNote"},
     *      description="Delete DebitNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNote",
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
        /** @var DebitNote $debitNote */
        $debitNote = $this->debitNoteRepository->findWithoutFail($id);

        if (empty($debitNote)) {
            return $this->sendError('Debit Note not found');
        }

        $debitNote->delete();

        return $this->sendResponse($id, 'Debit Note deleted successfully');
    }


    public function getDebitNoteMasterRecord(Request $request)
    {
        $input = $request->all();

        $output = DebitNote::where('debitNoteAutoID', $input['debitNoteAutoID'])->with(['detail' => function ($query) {
            $query->with('segment');
        }, 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 15);
        }, 'company', 'transactioncurrency', 'localcurrency', 'rptcurrency', 'supplier', 'confirmed_by', 'created_by', 'modified_by'])->first();

        return $this->sendResponse($output, 'Data retrieved successfully');
    }

    public function getAllDebitNotes(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'month', 'approved', 'year', 'isProforma'));

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

        $debitNotes = DebitNote::whereIn('companySystemID', $subCompanies)
            ->with('created_by', 'transactioncurrency', 'supplier')
            ->where('documentSystemID', $input['documentId']);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $debitNotes = $debitNotes->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $debitNotes = $debitNotes->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $debitNotes = $debitNotes->whereMonth('debitNoteDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $debitNotes = $debitNotes->whereYear('debitNoteDate', '=', $input['year']);
            }
        }

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $debitNotes = $debitNotes->where(function ($query) use ($search) {
                $query->where('debitNoteCode', 'LIKE', "%{$search}%")
                    ->orWhereHas('supplier', function ($query) use ($search) {
                        $query->where('supplierName', 'like', "%{$search}%");
                    });
            });
        }

        return \DataTables::of($debitNotes)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('debitNoteAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getDebitNoteFormData(Request $request)
    {
        $companyId = $request['companyId'];
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = DebitNote::select(DB::raw("YEAR(createdDateAndTime) as year"))
            ->whereNotNull('createdDateAndTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();
        $companyFinanceYear = \Helper::companyFinanceYear($companyId);

        $suppliers = SupplierAssigned::select(DB::raw("supplierCodeSytem,CONCAT(primarySupplierCode, ' | ' ,supplierName) as supplierName"))
            ->where('companySystemID', $companyId)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        $segments = SegmentMaster::where("companySystemID", $companyId)
            ->where('isActive', 1)->get();
        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'companyFinanceYear' => $companyFinanceYear,
            'suppliers' => $suppliers,
            'segments' => $segments
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }
}
