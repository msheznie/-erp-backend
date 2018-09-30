<?php
/**
 * =============================================
 * -- File Name : JvMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  JV Master
 * -- Author : Mohamed Nazir
 * -- Create date : 25-September 2018
 * -- Description : This file contains the all CRUD for GRV Master
 * -- REVISION HISTORY
 * -- Date: 25-September 2018 By: Nazir Description: Added new functions named as getJournalVoucherMasterFormData()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateJvMasterAPIRequest;
use App\Http\Requests\API\UpdateJvMasterAPIRequest;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CurrencyMaster;
use App\Models\DocumentMaster;
use App\Models\JvDetail;
use App\Models\JvMaster;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\JvMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use Response;

/**
 * Class JvMasterController
 * @package App\Http\Controllers\API
 */
class JvMasterAPIController extends AppBaseController
{
    /** @var  JvMasterRepository */
    private $jvMasterRepository;
    private $userRepository;

    public function __construct(JvMasterRepository $jvMasterRepo, UserRepository $userRepo)
    {
        $this->jvMasterRepository = $jvMasterRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/jvMasters",
     *      summary="Get a listing of the JvMasters.",
     *      tags={"JvMaster"},
     *      description="Get all JvMasters",
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
     *                  @SWG\Items(ref="#/definitions/JvMaster")
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
        $this->jvMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->jvMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $jvMasters = $this->jvMasterRepository->all();

        return $this->sendResponse($jvMasters->toArray(), 'Jv Masters retrieved successfully');
    }

    /**
     * @param CreateJvMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/jvMasters",
     *      summary="Store a newly created JvMaster in storage",
     *      tags={"JvMaster"},
     *      description="Store JvMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JvMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/JvMaster")
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
     *                  ref="#/definitions/JvMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateJvMasterAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);


        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            return $this->sendError($companyFinanceYear["message"], 500);
        }

        $inputParam = $input;
        $inputParam["departmentSystemID"] = 17;
        $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
        if (!$companyFinancePeriod["success"]) {
            return $this->sendError($companyFinancePeriod["message"], 500);
        } else {
            $input['FYBiggin'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod["message"]->dateTo;
        }

        unset($inputParam);

        if (isset($input['JVdate'])) {
            if ($input['JVdate']) {
                $input['JVdate'] = new Carbon($input['JVdate']);
            }
        }

        $documentDate = $input['JVdate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('JV date is not within the financial period!');
        }

        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $FYPeriodDateFrom = $companyfinanceperiod->dateFrom;
        $FYPeriodDateTo = $companyfinanceperiod->dateTo;

        $input['FYPeriodDateFrom'] = $FYPeriodDateFrom;
        $input['FYPeriodDateTo'] = $FYPeriodDateTo;

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];
        $input['documentSystemID'] = '17';
        $input['documentID'] = 'JV';

        $lastSerial = JvMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('jvMasterAutoId', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['currencyID'], $input['currencyID'], 0);

        //var_dump($companyCurrencyConversion);
        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
            $input['rptCurrencyID'] = $company->reportingCurrency;
            $input['rptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
        }

        $input['serialNo'] = $lastSerialNumber;
        $input['currencyER'] = 1;

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

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
        if ($documentMaster) {
            $jvCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['JVcode'] = $jvCode;
        }

        $jvMasters = $this->jvMasterRepository->create($input);

        return $this->sendResponse($jvMasters->toArray(), 'JV created successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/jvMasters/{id}",
     *      summary="Display the specified JvMaster",
     *      tags={"JvMaster"},
     *      description="Get JvMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JvMaster",
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
     *                  ref="#/definitions/JvMaster"
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
        /** @var JvMaster $jvMaster */
        $jvMaster = $this->jvMasterRepository->with(['created_by', 'confirmed_by', 'company', 'modified_by', 'transactioncurrency', 'financeperiod_by', 'financeyear_by'])->findWithoutFail($id);

        if (empty($jvMaster)) {
            return $this->sendError('Jv Master not found');
        }

        return $this->sendResponse($jvMaster->toArray(), 'Jv Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateJvMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/jvMasters/{id}",
     *      summary="Update the specified JvMaster in storage",
     *      tags={"JvMaster"},
     *      description="Update JvMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JvMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JvMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/JvMaster")
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
     *                  ref="#/definitions/JvMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateJvMasterAPIRequest $request)
    {
        $input = $request->all();

        $input = array_except($input, ['company', 'created_by', 'confirmedByName', 'financeperiod_by', 'financeyear_by', 'transactioncurrency', 'confirmedByEmpID', 'confirmedDate', 'confirmed_by', 'confirmedByEmpSystemID']);

        $input = $this->convertArrayToValue($input);

        /** @var JvMaster $jvMaster */
        $jvMaster = $this->jvMasterRepository->findWithoutFail($id);

        if (empty($jvMaster)) {
            return $this->sendError('Jv Master not found');
        }

        if (isset($input['JVdate'])) {
            if ($input['JVdate']) {
                $input['JVdate'] = new Carbon($input['JVdate']);
            }
        }


        if ($jvMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {

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
                'JVdate' => 'required',
                'currencyID' => 'required|numeric|min:1',
                'JVNarration' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $documentDate = $input['JVdate'];
            $monthBegin = $input['FYPeriodDateFrom'];
            $monthEnd = $input['FYPeriodDateTo'];
            if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
            } else {
                return $this->sendError('Document date is not within the selected financial period !', 500);
            }

            $checkItems = JvDetail::where('jvMasterAutoId', $id)
                ->count();
            if ($checkItems == 0) {
                return $this->sendError('Journal Voucher should have at least one item', 500);
            }

            $checkQuantity = JvDetail::where('jvMasterAutoId', $id)
                ->where('debitAmount', '<=', 0)
                ->where('creditAmount', '<=', 0)
                ->count();
            if ($checkQuantity > 0) {
                return $this->sendError('Amount should be greater than 0 for debit amount or credit amount', 500);
            }

            $debitNoteDetails = JvDetail::where('jvMasterAutoId', $id)->get();

            $finalError = array('amount_zero' => array(),
                'amount_neg' => array(),
                'required_serviceLine' => array(),
                'active_serviceLine' => array(),
            );
            $error_count = 0;

            foreach ($debitNoteDetails as $item) {
                $updateItem = JvDetail::find($item['jvDetailAutoID']);

                if ($updateItem->serviceLineSystemID && !is_null($updateItem->serviceLineSystemID)) {

                    $checkDepartmentActive = SegmentMaster::where('serviceLineSystemID', $updateItem->serviceLineSystemID)
                        ->where('isActive', 1)
                        ->first();
                    if (empty($checkDepartmentActive)) {
                        $updateItem->serviceLineSystemID = null;
                        $updateItem->serviceLineCode = null;
                        array_push($finalError['active_serviceLine'], $updateItem->glCode);
                        $error_count++;
                    }
                } else {
                    array_push($finalError['required_serviceLine'], $updateItem->glCode);
                    $error_count++;
                }

            }

            $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
            if ($error_count > 0) {
                return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
            }

            $JvDetailDebitSum = JvDetail::where('jvMasterAutoId', $id)
                ->sum('debitAmount');

            $JvDetailCreditSum = JvDetail::where('jvMasterAutoId', $id)
                ->sum('creditAmount');

            if ($JvDetailDebitSum != $JvDetailCreditSum) {
                return $this->sendError('Debit total not matching with credit total ', 500);
            }

            $params = array('autoID' => $id,
                'company' => $jvMaster->companySystemID,
                'document' => $jvMaster->documentSystemID,
                'segment' => 0,
                'category' => 0,
                'amount' => $JvDetailDebitSum
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

        $jvMaster = $this->jvMasterRepository->update($input, $id);

        return $this->sendResponse($jvMaster->toArray(), 'JvMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/jvMasters/{id}",
     *      summary="Remove the specified JvMaster from storage",
     *      tags={"JvMaster"},
     *      description="Delete JvMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JvMaster",
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
        /** @var JvMaster $jvMaster */
        $jvMaster = $this->jvMasterRepository->findWithoutFail($id);

        if (empty($jvMaster)) {
            return $this->sendError('Jv Master not found');
        }

        $jvMaster->delete();

        return $this->sendResponse($id, 'Jv Master deleted successfully');
    }

    public function getJournalVoucherMasterFormData(Request $request)
    {
        $companyId = $request['companyId'];

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = JvMaster::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $segments = SegmentMaster::where("companySystemID", $companyId)
            ->where('isActive', 1)->get();

        $currencies = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
            ->get();

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear"));
        $companyFinanceYear = $companyFinanceYear->where('companySystemID', $companyId);
        if (isset($request['type']) && ($request['type'] == 'add' || $request['type'] == 'edit')) {
            $companyFinanceYear = $companyFinanceYear->where('isActive', -1);
            $companyFinanceYear = $companyFinanceYear->where('isCurrent', -1);
        }
        $companyFinanceYear = $companyFinanceYear->get();

        $output = array('yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'currencies' => $currencies,
            'financialYears' => $financialYears,
            'companyFinanceYear' => $companyFinanceYear,
            'segments' => $segments
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }


    public function getJournalVoucherMasterView(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approved', 'month', 'year'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $invMaster = JvMaster::where('companySystemID', $input['companySystemID']);
        //$invMaster->where('documentSystemID', $input['documentId']);
        $invMaster->with(['created_by', 'transactioncurrency', 'detail' => function ($query) {
            $query->selectRaw('COALESCE(SUM(debitAmount),0) as debitSum,COALESCE(SUM(creditAmount),0) as creditSum,jvMasterAutoId');
            $query->groupBy('jvMasterAutoId');
        }]);
        if (array_key_exists('jvType', $input)) {
            if (($input['jvType'] == 0 || $input['jvType'] == 1 || $input['jvType'] == 2 || $input['jvType'] == 3 || $input['jvType'] == 4 || $input['jvType'] == 5) && !is_null($input['jvType'])) {
                $invMaster->where('jvType', $input['jvType']);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $invMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $invMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $invMaster->whereMonth('JVdate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $invMaster->whereYear('JVdate', '=', $input['year']);
            }
        }

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $invMaster = $invMaster->where(function ($query) use ($search) {
                $query->where('JVcode', 'LIKE', "%{$search}%")
                    ->orWhere('JVNarration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($invMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('jvMasterAutoId', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
