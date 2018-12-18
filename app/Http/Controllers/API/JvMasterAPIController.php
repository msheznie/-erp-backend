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
 * -- Date: 02-October 2018 By: Nazir Description: Added new functions named as getJournalVoucherMasterRecord()
 * -- Date: 03-October 2018 By: Nazir Description: Added new functions named as journalVoucherForSalaryJVMaster()
 * -- Date: 03-October 2018 By: Nazir Description: Added new functions named as journalVoucherForSalaryJVDetail()
 * -- Date: 04-October 2018 By: Nazir Description: Added new functions named as journalVoucherForAccrualJVMaster()
 * -- Date: 04-October 2018 By: Nazir Description: Added new functions named as journalVoucherForAccrualJVDetail()
 * -- Date: 10-October 2018 By: Nazir Description: Added new functions named as getJournalVoucherMasterApproval()
 * -- Date: 10-October 2018 By: Nazir Description: Added new functions named as getApprovedJournalVoucherForCurrentUser()
 * -- Date: 14-October 2018 By: Nazir Description: Added new functions named as journalVoucherForPOAccrualJVDetail()
 * -- Date: 15-October 2018 By: Nazir Description: Added new functions named as journalVoucherReopen()
 * -- Date: 05-December 2018 By: Nazir Description: Added new functions named as getJournalVoucherAmend()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateJvMasterAPIRequest;
use App\Http\Requests\API\UpdateJvMasterAPIRequest;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\CurrencyMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\JvDetail;
use App\Models\JvDetailsReferredback;
use App\Models\JvMaster;
use App\Models\JvMasterReferredback;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Models\chartOfAccount;
use App\Repositories\JvMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Storage;
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

        $validator = \Validator::make($input, [
            'companyFinancePeriodID' => 'required|numeric|min:1',
            'companyFinanceYearID' => 'required|numeric|min:1',
            'jvType' => 'required',
            'JVdate' => 'required',
            'companySystemID' => 'required',
            'currencyID' => 'required|numeric|min:1',
            'JVNarration' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

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
        $jvMaster = $this->jvMasterRepository->with(['created_by', 'confirmed_by', 'company', 'modified_by', 'transactioncurrency', 'financeperiod_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'financeyear_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }])->findWithoutFail($id);

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
        $input = array_except($input, ['created_by', 'confirmedByName', 'financeperiod_by', 'financeyear_by', 'supplier',
            'confirmedByEmpID', 'confirmedDate', 'company', 'confirmed_by', 'confirmedByEmpSystemID', 'transactioncurrency']);
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

        $currencyDecimalPlace = \Helper::getCurrencyDecimalPlace($jvMaster->currencyID);

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

        $documentDate = $input['JVdate'];
        $monthBegin = $input['FYPeriodDateFrom'];
        $monthEnd = $input['FYPeriodDateTo'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('Document date is not within the selected financial period !', 500);
        }

        if ($jvMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {

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

            $jvDetails = JvDetail::where('jvMasterAutoId', $id)->get();

            $finalError = array(
                'required_serviceLine' => array(),
                'active_serviceLine' => array(),
                'contract_check' => array()
            );
            $error_count = 0;

            foreach ($jvDetails as $item) {
                $updateItem = JvDetail::find($item['jvDetailAutoID']);

                if ($updateItem->serviceLineSystemID && !is_null($updateItem->serviceLineSystemID)) {

                    $checkDepartmentActive = SegmentMaster::where('serviceLineSystemID', $updateItem->serviceLineSystemID)
                        ->where('isActive', 1)
                        ->first();
                    if (empty($checkDepartmentActive)) {
                        $updateItem->serviceLineSystemID = null;
                        $updateItem->serviceLineCode = null;
                        array_push($finalError['active_serviceLine'], $updateItem->glAccount);
                        $error_count++;
                    }
                } else {
                    array_push($finalError['required_serviceLine'], $updateItem->glAccount);
                    $error_count++;
                }

            }

            //if standard jv
            if ($input['jvType'] == 0) {
                $policyConfirmedUserToApprove = CompanyPolicyMaster::where('companyPolicyCategoryID', 15)
                    ->where('companySystemID', $input['companySystemID'])
                    ->first();

                if ($policyConfirmedUserToApprove->isYesNO == 0) {

                    foreach ($jvDetails as $item) {

                        $chartOfAccount = ChartOfAccountsAssigned::select('controlAccountsSystemID')->where('chartOfAccountSystemID', $item->chartOfAccountSystemID)->first();

                        if ($chartOfAccount->controlAccountsSystemID == 1) {
                            if ($item['contractUID'] == '' || $item['contractUID'] == 0) {
                                array_push($finalError['contract_check'], $item->glAccount);
                                $error_count++;
                            }
                        }
                    }
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

            if (round($JvDetailDebitSum, $currencyDecimalPlace) != round($JvDetailCreditSum, $currencyDecimalPlace)) {
                return $this->sendError('Debit amount total and credit amount total is not matching', 500);
            }

            $input['RollLevForApp_curr'] = 1;

            unset($input['confirmedYN']);
            unset($input['confirmedByEmpSystemID']);
            unset($input['confirmedByEmpID']);
            unset($input['confirmedByName']);
            unset($input['confirmedDate']);

            $params = array(
                'autoID' => $id,
                'company' => $input["companySystemID"],
                'document' => $input["documentSystemID"],
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
        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approved', 'month', 'year', 'jvType'));
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

    public function getJournalVoucherMasterRecord(Request $request)
    {
        $id = $request->get('matchDocumentMasterAutoID');
        /** @var JvMaster $jvMaster */
        $jvMasterData = $this->jvMasterRepository->with(['created_by', 'confirmed_by', 'modified_by'])->findWithoutFail($id);

        if (empty($jvMasterData)) {
            return $this->sendError('Jv Master not found');
        }

        return $this->sendResponse($jvMasterData, 'Jv Master retrieved successfully');
    }

    public function journalVoucherForSalaryJVMaster(Request $request)
    {
        $companySystemID = $request['companySystemID'];

        $company = Company::where('companySystemID', $companySystemID)->first();

        if (empty($company)) {
            return $this->sendError('Company master not found');
        }

        if ($company) {
            $companyID = $company->CompanyID;
        }

        $output = DB::select("SELECT
	hrms_jvmaster.accruvalMasterID,
	hrms_jvmaster.salaryProcessMasterID,
	hrms_jvmaster.JVCode,
	hrms_jvmaster.accruvalNarration,
	hrms_jvmaster.accConfirmedYN,
	hrms_jvmaster.accJVSelectedYN,
	hrms_jvmaster.accJVpostedYN,
	hrms_jvmaster.accmonth
FROM
	hrms_jvmaster
WHERE hrms_jvmaster.accConfirmedYN = 1
AND hrms_jvmaster.accJVSelectedYN = 0
AND hrms_jvmaster.accJVpostedYN = 0
AND hrms_jvmaster.companyID = '" . $companyID . "'");

        return $this->sendResponse($output, 'Data retrieved successfully');

    }


    public function journalVoucherForSalaryJVDetail(Request $request)
    {
        $companySystemID = $request['companyId'];
        $accruvalMasterID = $request['accruvalMasterID'];

        $company = Company::where('companySystemID', $companySystemID)->first();

        if (empty($company)) {
            return $this->sendError('Company master not found');
        }

        if ($company) {
            $companyID = $company->CompanyID;
        }

        $output = DB::select("SELECT
	hrms_jvdetails.accruvalDetID,
	hrms_jvdetails.accMasterID,
	serviceline.serviceLineSystemID,
	hrms_jvdetails.serviceLine,
	hrms_jvdetails.GlCode,
	hrms_jvdetails.localAmount,
	chartofaccounts.chartOfAccountSystemID,
	chartofaccounts.AccountDescription,
	hrms_jvdetails.localCurrency,
	Sum(

		IF (
			localAmount < 0,
			localAmount * - 1,
			0
		)
	) AS CreditAmount,
	Sum(

		IF (
			localAmount > 0,
			localAmount,
			0
		)
	) AS DebitAmount
FROM
	hrms_jvdetails
INNER JOIN chartofaccounts ON hrms_jvdetails.GlCode = chartofaccounts.AccountCode
LEFT JOIN serviceline ON hrms_jvdetails.serviceLine = serviceline.ServiceLineCode
WHERE
	hrms_jvdetails.accMasterID = $accruvalMasterID
AND hrms_jvdetails.companyID = '" . $companyID . "'
GROUP BY
	hrms_jvdetails.accMasterID,
	hrms_jvdetails.serviceLine,
	hrms_jvdetails.GlCode,
	chartofaccounts.AccountDescription,
	hrms_jvdetails.localCurrency,
	hrms_jvdetails.companyID");

        return $this->sendResponse($output, 'Data retrieved successfully');
    }

    public function journalVoucherForAccrualJVMaster(Request $request)
    {

        $companySystemID = $request['companySystemID'];

        $company = Company::where('companySystemID', $companySystemID)->first();

        if (empty($company)) {
            return $this->sendError('Company master not found');
        }

        if ($company) {
            $companyID = $company->CompanyID;
        }

        $output = DB::select("SELECT
	accruavalfromopmaster.accruvalMasterID,
	accruavalfromopmaster.accrualDateAsOF,
	accruavalfromopmaster.accmonth,
	accruavalfromopmaster.accYear,
	accruavalfromopmaster.accruvalNarration
FROM
	accruavalfromopmaster
WHERE accruavalfromopmaster.companyID = '" . $companyID . "'
AND accruavalfromopmaster.accConfirmedYN = 1
AND accruavalfromopmaster.accJVpostedYN = 0");

        return $this->sendResponse($output, 'Data retrieved successfully');
    }

    public function journalVoucherForAccrualJVDetail(Request $request)
    {
        $companySystemID = $request['companyId'];
        $accruvalMasterID = $request['accruvalMasterID'];

        $company = Company::where('companySystemID', $companySystemID)->first();

        if (empty($company)) {
            return $this->sendError('Company master not found');
        }

        if ($company) {
            $companyID = $company->CompanyID;
        }

        $output = DB::select("SELECT
	accruvalfromop.accruvalDetID,
	accruvalfromop.contractID,
	serviceline.serviceLineSystemID,
	accruvalfromop.serviceLine,
	accruvalfromop.stdAmount,
	accruvalfromop.opAmount,
	accruvalfromop.accMasterID,
	accruvalfromop.companyID,
	accruvalfromop.accrualAmount,
	accruvalfromop.GlCode,
	chartofaccounts.chartOfAccountSystemID,
	chartofaccounts.AccountDescription
FROM
	accruvalfromop
LEFT JOIN serviceline ON accruvalfromop.serviceLine = serviceline.ServiceLineCode
LEFT JOIN chartofaccounts ON accruvalfromop.GlCode = chartofaccounts.AccountCode
WHERE
	accruvalfromop.accMasterID = $accruvalMasterID
AND accruvalfromop.companyID = '" . $companyID . "'");

        return $this->sendResponse($output, 'Data retrieved successfully');
    }

    public function exportStandardJVFormat(Request $request)
    {
        $input = $request->all();
        if ($exists = Storage::disk('public')->exists('standard_jv_template/standard_jv_upload_template.xlsx')) {
            return Storage::disk('public')->download('standard_jv_template/standard_jv_upload_template.xlsx', 'standard_jv_upload_template.xlsx');
        } else {
            return $this->sendError('Attachments not found', 500);
        }
    }

    public function getJournalVoucherMasterApproval(Request $request)
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
            ->where('documentSystemID', 3)
            ->first();

        $grvMasters = DB::table('erp_documentapproved')->select(
            'erp_jvmaster.jvMasterAutoId',
            'erp_jvmaster.JVcode',
            'erp_jvmaster.documentSystemID',
            'erp_jvmaster.JVdate',
            'erp_jvmaster.JVNarration',
            'erp_jvmaster.createdDateTime',
            'erp_jvmaster.confirmedDate',
            'erp_jvmaster.jvType',
            'jvDetailRec.debitSum',
            'jvDetailRec.creditSum',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.DecimalPlaces As DecimalPlaces',
            'currencymaster.CurrencyCode As CurrencyCode',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        )->join('employeesdepartments', function ($query) use ($companyID, $empID, $serviceLinePolicy) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
            if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                $query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
            }
            $query->where('employeesdepartments.documentSystemID', 17)
                ->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID);
        })->join('erp_jvmaster', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'jvMasterAutoId')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('erp_jvmaster.companySystemID', $companyID)
                ->where('erp_jvmaster.approved', 0)
                ->where('erp_jvmaster.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('currencymaster', 'erp_jvmaster.currencyID', 'currencymaster.currencyID')
            ->leftJoin(DB::raw('(SELECT COALESCE(SUM(debitAmount),0) as debitSum,COALESCE(SUM(creditAmount),0) as creditSum,jvMasterAutoId FROM erp_jvdetail GROUP BY jvMasterAutoId) as jvDetailRec'), 'jvDetailRec.jvMasterAutoId', '=', 'erp_jvmaster.jvMasterAutoId')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 17)
            ->where('erp_documentapproved.companySystemID', $companyID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMasters = $grvMasters->where(function ($query) use ($search) {
                $query->where('JVcode', 'LIKE', "%{$search}%")
                    ->orWhere('JVNarration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($grvMasters)
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

    public function getApprovedJournalVoucherForCurrentUser(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

        $grvMasters = DB::table('erp_documentapproved')->select(
            'erp_jvmaster.jvMasterAutoId',
            'erp_jvmaster.JVcode',
            'erp_jvmaster.documentSystemID',
            'erp_jvmaster.JVdate',
            'erp_jvmaster.JVNarration',
            'erp_jvmaster.createdDateTime',
            'erp_jvmaster.confirmedDate',
            'erp_jvmaster.jvType',
            'jvDetailRec.debitSum',
            'jvDetailRec.creditSum',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.DecimalPlaces As DecimalPlaces',
            'currencymaster.CurrencyCode As CurrencyCode',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        )->join('erp_jvmaster', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'jvMasterAutoId')
                ->where('erp_jvmaster.companySystemID', $companyID)
                ->where('erp_jvmaster.approved', -1)
                ->where('erp_jvmaster.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('currencymaster', 'erp_jvmaster.currencyID', 'currencymaster.currencyID')
            ->leftJoin(DB::raw('(SELECT COALESCE(SUM(debitAmount),0) as debitSum,COALESCE(SUM(creditAmount),0) as creditSum,jvMasterAutoId FROM erp_jvdetail GROUP BY jvMasterAutoId) as jvDetailRec'), 'jvDetailRec.jvMasterAutoId', '=', 'erp_jvmaster.jvMasterAutoId')
            ->where('erp_documentapproved.documentSystemID', 17)
            ->where('erp_documentapproved.companySystemID', $companyID)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMasters = $grvMasters->where(function ($query) use ($search) {
                $query->where('JVcode', 'LIKE', "%{$search}%")
                    ->orWhere('JVNarration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($grvMasters)
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

    public function approveJournalVoucher(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectJournalVoucher(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }

    }

    public function generateJournalVoucher($masterData)
    {
        $jvMasterData = JvMaster::find($masterData['autoID']);

        if ($jvMasterData->jvType == 1) {

            $lastSerial = JvMaster::where('companySystemID', $jvMasterData->companySystemID)
                ->where('companyFinanceYearID', $jvMasterData->companyFinanceYearID)
                ->orderBy('jvMasterAutoId', 'desc')
                ->first();

            $lastSerialNumber = 1;
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
            }

            $firstDayNextMonth = date('Y-m-d', strtotime('first day of next month'));

            $companyfinanceyear = CompanyFinanceYear::where('companyFinanceYearID', $jvMasterData->companyFinanceYearID)
                ->where('companySystemID', $jvMasterData->companySystemID)
                ->first();

            if ($companyfinanceyear) {
                $startYear = $companyfinanceyear->bigginingDate;
                $finYearExp = explode('-', $startYear);
                $finYear = $finYearExp[0];
            } else {
                $finYear = date("Y");
            }

            $jvCode = ($jvMasterData->CompanyID . '\\' . $finYear . '\\' . $jvMasterData->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

            $postJv = $jvMasterData->toArray();
            $postJv['JVcode'] = $jvCode;
            $postJv['serialNo'] = $lastSerialNumber;
            $postJv['JVdate'] = $firstDayNextMonth;

            $storeJV = JvMaster::create($postJv);

            //inserting to jv detail
            $fetchJVDetail = JvDetail::where('jvMasterAutoId', $masterData['autoID'])->get();

            if (!empty($fetchJVDetail)) {
                foreach ($fetchJVDetail as $key => $val) {
                    $fetchJVDetail[$key]['debitAmount'] = $val['creditAmount'];
                    $fetchJVDetail[$key]['creditAmount'] = $val['debitAmount'];
                }
            }

            $jvDetailArray = $fetchJVDetail->toArray();

            $storeJvDetail = JvDetail::insert($jvDetailArray);
        }
    }

    public function journalVoucherForPOAccrualJVDetail(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companySystemID = $request['companyId'];
        $jvMasterAutoId = $request['jvMasterAutoId'];

        $jvMasterData = jvMaster::find($jvMasterAutoId);
        if (empty($jvMasterData)) {
            return $this->sendError('Jv Master not found');
        }

        $filter='';
        $search = $request->input('search.value');
        if($search){
            $search = str_replace("\\", "\\\\\\\\", $search);
            $filter = " AND ( pomaster.purchaseOrderCode LIKE '%{$search}%') OR ( podetail.itemPrimaryCode LIKE '%{$search}%') OR ( podetail.itemDescription LIKE '%{$search}%') OR ( pomaster.supplierName LIKE '%{$search}%')";
        }

        $formattedJVdate = Carbon::parse($jvMasterData->JVdate)->format('Y-m-d');

        $qry = "SELECT
	pomaster.purchaseOrderID,
	pomaster.poType,
	pomaster.purchaseOrderCode,
	pomaster.serviceLineSystemID,
	pomaster.serviceLine,
	pomaster.expectedDeliveryDate,
	pomaster.approvedDate,
	podetail.itemPrimaryCode,
	podetail.itemDescription,
	(
		CASE podetail.financeGLcodePL
		WHEN podetail.financeGLcodePL IS NULL THEN
			podetail.financeGLcodebBS
		WHEN podetail.financeGLcodePL = '' THEN
			podetail.financeGLcodebBS
		ELSE
			podetail.financeGLcodePL
		END
	) AS glCode,
	(
		CASE podetail.financeGLcodePL
		WHEN podetail.financeGLcodePL IS NULL THEN
			podetail.financeGLcodebBSSystemID
		WHEN podetail.financeGLcodePL = '' THEN
			podetail.financeGLcodebBSSystemID
		ELSE
			podetail.financeGLcodePLSystemID
		END
	) AS glCodeSystemID,
	pomaster.supplierName,
	pomaster.poTotalComRptCurrency AS poCost,
	IFNULL(grvdetail.grvSum, 0) AS grvCost,
	(
		pomaster.poTotalComRptCurrency - IFNULL(grvdetail.grvSum, 0)
	) AS balanceCost
FROM
	erp_purchaseordermaster AS pomaster
INNER JOIN (
	SELECT
		COALESCE (
			SUM(
				GRVcostPerUnitComRptCur * noQty
			),
			0
		) AS poSum,
		purchaseOrderMasterID,
		itemCode,
		itemPrimaryCode,
		itemDescription,
		financeGLcodePL,
		financeGLcodePLSystemID,
		financeGLcodebBS,
		financeGLcodebBSSystemID
	FROM
		erp_purchaseorderdetails
	GROUP BY
		purchaseOrderMasterID,
		itemCode
) AS podetail ON podetail.purchaseOrderMasterID = pomaster.purchaseOrderID
LEFT JOIN (
	SELECT
		COALESCE (
			SUM(
				GRVcostPerUnitComRptCur * noQty
			),
			0
		) AS grvSum,
		purchaseOrderMastertID,
		erp_grvmaster.grvTypeID,
		erp_grvmaster.grvAutoID
	FROM
		erp_grvdetails
	INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID
	WHERE
		grvTypeID = 2
	AND DATE(grvDate) <= '$formattedJVdate' AND erp_grvmaster.companySystemID = $companySystemID
	GROUP BY
		purchaseOrderMastertID,
		itemCode
) AS grvdetail ON grvdetail.purchaseOrderMastertID = pomaster.purchaseOrderID
INNER JOIN suppliermaster AS supmaster ON pomaster.supplierID = supmaster.supplierCodeSystem
WHERE
	pomaster.companySystemID = $companySystemID
AND pomaster.poConfirmedYN = 1
AND pomaster.poCancelledYN = 0
AND pomaster.approved = - 1
AND pomaster.poType_N <> 5
AND pomaster.manuallyClosed = 0
AND pomaster.financeCategory IN (2, 4)
AND date(pomaster.approvedDate) >= '2016-05-01'
AND date(
	pomaster.expectedDeliveryDate
) <= '$formattedJVdate'
{$filter}
AND supmaster.companyLinkedToSystemID IS NULL
HAVING
	round(balanceCost, 2) <> 0";

        //echo $qry;
        //exit();
        $invMaster = DB::select($qry);

        $col[0] = $input['order'][0]['column'];
        $col[1] = $input['order'][0]['dir'];
        $request->request->remove('order');
        $data['order'] = [];
        /*  $data['order'][0]['column'] = '';
          $data['order'][0]['dir'] = '';*/
        $data['search']['value'] = '';
        $request->merge($data);

        $depAmountLocal = collect($invMaster)->pluck('balanceCost')->toArray();
        $depAmountLocal = array_sum($depAmountLocal);

        $request->request->remove('search.value');

        return \DataTables::of($invMaster)
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->with('balanceTotal', $depAmountLocal)
            ->make(true);
    }

    public function journalVoucherReopen(Request $request)
    {
        $input = $request->all();

        $jvMasterAutoId = $input['jvMasterAutoId'];

        $jvMasterData = JvMaster::find($jvMasterAutoId);
        $emails = array();
        if (empty($jvMasterData)) {
            return $this->sendError('Journal Voucher not found');
        }

        if ($jvMasterData->RollLevForApp_curr > 1) {
            return $this->sendError('You cannot reopen this journal voucher it is already partially approved');
        }

        if ($jvMasterData->approved == -1) {
            return $this->sendError('You cannot reopen this journal voucher it is already fully approved');
        }

        if ($jvMasterData->confirmedYN == 0) {
            return $this->sendError('You cannot reopen this journal voucher, it is not confirmed');
        }

        // updating fields

        $jvMasterData->confirmedYN = 0;
        $jvMasterData->confirmedByEmpSystemID = null;
        $jvMasterData->confirmedByEmpID = null;
        $jvMasterData->confirmedByName = null;
        $jvMasterData->confirmedDate = null;
        $jvMasterData->RollLevForApp_curr = 1;
        $jvMasterData->save();

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $jvMasterData->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $jvMasterData->bookingInvCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $jvMasterData->bookingInvCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $jvMasterData->companySystemID)
            ->where('documentSystemCode', $jvMasterData->bookingSuppMasInvAutoID)
            ->where('documentSystemID', $jvMasterData->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $jvMasterData->companySystemID)
                    ->where('documentSystemID', $jvMasterData->documentSystemID)
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

        $deleteApproval = DocumentApproved::where('documentSystemCode', $jvMasterAutoId)
            ->where('companySystemID', $jvMasterData->companySystemID)
            ->where('documentSystemID', $jvMasterData->documentSystemID)
            ->delete();

        return $this->sendResponse($jvMasterData->toArray(), 'JV reopened successfully');
    }

    public function getJournalVoucherAmend(Request $request)
    {
        $input = $request->all();

        $jvMasterAutoId = $input['jvMasterAutoId'];

        $jvMasterData = JvMaster::find($jvMasterAutoId);
        if (empty($jvMasterData)) {
            return $this->sendError('Journal Voucher not found');
        }

        if ($jvMasterData->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this journal voucher');
        }

        $journalVoucherArray = $jvMasterData->toArray();

        $storeJournalVoucherHistory = JvMasterReferredback::insert($journalVoucherArray);

        $fetchJournalVoucherDetails = JvDetail::where('jvMasterAutoId', $jvMasterAutoId)
            ->get();

        if (!empty($fetchJournalVoucherDetails)) {
            foreach ($fetchJournalVoucherDetails as $bookDetail) {
                $bookDetail['timesReferred'] = $jvMasterData->timesReferred;
            }
        }

        $journalVoucherDetailArray = $fetchJournalVoucherDetails->toArray();

        $storeJournalVoucherDetailHistory = JvDetailsReferredback::insert($journalVoucherDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $jvMasterAutoId)
            ->where('companySystemID', $jvMasterData->companySystemID)
            ->where('documentSystemID', $jvMasterData->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $jvMasterData->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $jvMasterAutoId)
            ->where('companySystemID', $jvMasterData->companySystemID)
            ->where('documentSystemID', $jvMasterData->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $jvMasterData->refferedBackYN = 0;
            $jvMasterData->confirmedYN = 0;
            $jvMasterData->confirmedByEmpSystemID = null;
            $jvMasterData->confirmedByEmpID = null;
            $jvMasterData->confirmedByName = null;
            $jvMasterData->confirmedDate = null;
            $jvMasterData->RollLevForApp_curr = 1;
            $jvMasterData->save();
        }


        return $this->sendResponse($jvMasterData->toArray(), 'Journal Voucher Amend successfully');
    }

    public function standardJvExcelUpload(request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $excelUpload = $input['assetExcelUpload'];
            $input = array_except($request->all(), 'assetExcelUpload');
            $input = $this->convertArrayToValue($input);

            $decodeFile = base64_decode($excelUpload[0]['file']);
            $originalFileName = $excelUpload[0]['filename'];

            Storage::disk('local')->put($originalFileName, $decodeFile);

            $finalData = [];
            $formatChk = \Excel::selectSheets('Sheet1')->load(Storage::disk('local')->url('app/' . $originalFileName), function ($reader) {
            })->first();
            $formatChk2 = '';

            if (!$formatChk) {
                return $this->sendError('No records found', 500);
            } else {
                $formatChk2 = collect($formatChk)->toArray();
            }

            if (count($formatChk2) > 0) {
                if (!isset($formatChk['gl_account']) || !isset($formatChk['gl_account_description']) || !isset($formatChk['department']) || !isset($formatChk['client_contract']) || !isset($formatChk['comments']) || !isset($formatChk['debit_amount']) || !isset($formatChk['credit_amount'])) {
                    return $this->sendError('Uploaded data format is invalid', 500);
                }
            }

            $record = \Excel::selectSheets('Sheet1')->load(Storage::disk('local')->url('app/' . $originalFileName), function ($reader) {
            })->select(array('gl_account', 'gl_account_description', 'department', 'client_contract', 'comments', 'debit_amount', 'credit_amount'))->get()->toArray();

            if (count($record) > 0) {

                $jvMasterData = JvMaster::find($input['jvMasterAutoId']);

                if (empty($jvMasterData)) {
                    return $this->sendError('Journal Voucher not found');
                }

                foreach ($record as $val) {

                    $segmentData = SegmentMaster::where('ServiceLineDes', $val['department'])
                        ->where('companySystemID', $jvMasterData->companySystemID)
                        ->first();
                    $serviceLineSystemID = 0;
                    $chartOfAccountSystemID = 0;
                    $debitAmount = 0;
                    $creditAmount = 0;
                    if ($segmentData) {
                        $serviceLineSystemID = $segmentData['serviceLineSystemID'];
                    }
                    $chartOfAccountData = chartofaccountsassigned::where('AccountCode', $val['gl_account'])
                        ->where('companySystemID', $jvMasterData->companySystemID)
                        ->first();

                    if ($chartOfAccountData) {
                       $chartOfAccountSystemID = $chartOfAccountData->chartOfAccountSystemID;
                    }
                    if($val['debit_amount'] != ''){
                        $debitAmount = $val['debit_amount'];
                    }
                    if($val['credit_amount'] != ''){
                        $creditAmount = $val['credit_amount'];
                    }
                    $data = [];
                    $data['jvMasterAutoId'] = $input['jvMasterAutoId'];
                    $data['documentSystemID'] = $jvMasterData->documentSystemID;
                    $data['documentID'] = $jvMasterData->documentID;
                    $data['companySystemID'] = $jvMasterData->companySystemID;
                    $data['companyID'] = $jvMasterData->companyID;
                    $data['serviceLineSystemID'] = $serviceLineSystemID;
                    $data['serviceLineCode'] = $val['department'];
                    $data['chartOfAccountSystemID'] = $chartOfAccountSystemID;
                    $data['glAccount'] = $val['gl_account'];
                    $data['glAccountDescription'] = $val['gl_account_description'];
                    $data['clientContractID'] = $val['client_contract'];
                    $data['comments'] = $val['comments'];
                    $data['currencyID'] = $jvMasterData->currencyID;
                    $data['currencyER'] = $jvMasterData->currencyER;
                    $data['debitAmount'] = $debitAmount;
                    $data['creditAmount'] = $creditAmount;
                    $data['createdPcID'] = gethostname();
                    $data['createdUserID'] = \Helper::getEmployeeID();
                    $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                    $finalData[] = $data;
                }
            } else {
                return $this->sendError('No Records found!', 500);
            }

            if (count($finalData) > 0) {
                foreach (array_chunk($finalData, 500) as $t) {
                    JvDetail::insert($t);
                }
            }
            Storage::disk('local')->delete($originalFileName);
            DB::commit();
            return $this->sendResponse([], 'JV Details uploaded successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
        //Storage::disk('local')->delete($originalFileName);

    }
}
