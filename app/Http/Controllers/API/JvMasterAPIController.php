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
 * -- Date: 23-December 2018 By: Nazir Description: Added new functions named as printJournalVoucher()
 * -- Date: 11-January 2019 By: Mubashir Description: Added new functions named as approvalPreCheckJV()
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\BudgetConsumedData;
use App\Models\ChartOfAccountsAssigned;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\Contract;
use App\Models\CurrencyMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\GeneralLedger;
use App\Models\JvDetail;
use App\Models\JvDetailsReferredback;
use App\Models\JvMaster;
use App\Models\JvMasterReferredback;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\BudgetConsumedDataRepository;
use App\Repositories\JvMasterRepository;
use App\Repositories\UserRepository;
use App\Services\UserTypeService;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\Helper;
use App\Models\ErpProjectMaster;
use App\Services\ValidateDocumentAmend;

/**
 * Class JvMasterController
 * @package App\Http\Controllers\API
 */
class JvMasterAPIController extends AppBaseController
{
    /** @var  JvMasterRepository */
    private $jvMasterRepository;
    private $userRepository;
    private $budgetConsumedDataRepository;

    public function __construct(JvMasterRepository $jvMasterRepo, UserRepository $userRepo,
                                BudgetConsumedDataRepository $budgetConsumedDataRepo)
    {
        $this->jvMasterRepository = $jvMasterRepo;
        $this->userRepository = $userRepo;
        $this->budgetConsumedDataRepository = $budgetConsumedDataRepo;
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
     * @param Request $request
     * @return array
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
    public function store(Request $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                return [
                    "success" => false,
                    "message" => "Selected financial year is not active"
                ];
            }
            else{
                return $this->sendError($companyFinanceYear["message"], 500);
            }
        }

        $inputParam = $input;
        $inputParam["departmentSystemID"] = 5;
        $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
        if (!$companyFinancePeriod["success"]) {
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                return [
                    "success" => false,
                    "message" => "Selected financial period is not active"
                ];
            }
            else{
                return $this->sendError($companyFinancePeriod["message"], 500);
            }
        }
        else {
            $input['FYBiggin'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod["message"]->dateTo;
        }

        unset($inputParam);

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
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                return [
                    "success" => false,
                    "message" => "Error"
                ];
            }
            else{
                return $this->sendError($validator->messages(), 422);
            }
        }

        if (isset($input['jvType']) && $input['jvType'] == 4) {
            $checkPendingJv = JvMaster::where('jvType', $input['jvType'])
                                      ->where('companySystemID', $input['companySystemID'])
                                      ->where('refferedBackYN', 0)
                                      ->where('approved', 0)
                                      ->first();

            if ($checkPendingJv) {
                return $this->sendError('There is a pending allocation JV, please approve those allocation JVs');
            }
        }

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
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                return [
                    "success" => false,
                    "message" => "JV date is not within the financial period"
                ];
            }
            else{
                return $this->sendError('JV date is not within the financial period!');
            }
        }

        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $FYPeriodDateFrom = $companyfinanceperiod->dateFrom;
        $FYPeriodDateTo = $companyfinanceperiod->dateTo;

        $input['FYPeriodDateFrom'] = $FYPeriodDateFrom;
        $input['FYPeriodDateTo'] = $FYPeriodDateTo;

        $input['createdPcID'] = gethostname();

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            $employee = UserTypeService::getSystemEmployee();
            $input['createdUserID'] = $employee->empID;
            $input['createdUserSystemID'] = $employee->employeeSystemID;
        }
        else{
            $id = Auth::id();
            $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

            $input['createdUserID'] = $user->employee['empID'];
            $input['createdUserSystemID'] = $user->employee['employeeSystemID'];
        }

        $input['documentSystemID'] = '17';
        $input['documentID'] = 'JV';

        $lastSerial = JvMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
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

        $jvMaster = $this->jvMasterRepository->create($input);

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            return [
                "success" => true,
                "data" => $jvMaster->toArray()
            ];
        }
        else{
            return $this->sendResponse($jvMaster->toArray(), 'JV created successfully');
        }
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
     * @param Request $request
     * @return array
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
    public function update($id, Request $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmedByName', 'financeperiod_by', 'financeyear_by', 'supplier',
            'confirmedByEmpID', 'confirmedDate', 'company', 'confirmed_by', 'confirmedByEmpSystemID', 'transactioncurrency', 'modified_by']);
        $input = $this->convertArrayToValue($input);

        /** @var JvMaster $jvMaster */
        $jvMaster = $this->jvMasterRepository->findWithoutFail($id);

        if (empty($jvMaster)) {
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                return [
                    "success" => false,
                    "message" => "Jv Master not found"
                ];
            }
            else{
                return $this->sendError('Jv Master not found');
            }
        }

        $jvConfirmedYN = $input['confirmedYN'];
        $prevJvConfirmedYN = $jvMaster->confirmedYN;

        $currencyDecimalPlace = \Helper::getCurrencyDecimalPlace($jvMaster->currencyID);

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
        }
        else{
            if (isset($input['JVdate'])) {
                if ($input['JVdate']) {
                    $input['JVdate'] = Carbon::parse($input['JVdate']);
                }
            }

            if (isset($input['jvType']) && $input['jvType'] == 4) {
                $checkPendingJv = JvMaster::where('jvType', $input['jvType'])
                    ->where('companySystemID', $input['companySystemID'])
                    ->where('refferedBackYN', 0)
                    ->where('approved', 0)
                    ->first();

                if ($checkPendingJv) {
                    return $this->sendError('There is a pending allocation JV, please approve those allocation JVs');
                }
            }

            $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
            if (!$companyFinanceYear["success"]) {
                return $this->sendError($companyFinanceYear["message"], 500);
            } else {
                $input['FYBiggin'] = $companyFinanceYear["message"]->bigginingDate;
                $input['FYEnd'] = $companyFinanceYear["message"]->endingDate;
            }

            $inputParam = $input;
            $inputParam["departmentSystemID"] = 5;
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
                if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                    return [
                        "success" => false,
                        "message" => "Validation failed"
                    ];
                }
                else{
                    return $this->sendError($validator->messages(), 422);
                }
            }


            $query = JvDetail::selectRaw("chartofaccounts.AccountCode")
            ->join('chartofaccounts', 'chartofaccounts.chartOfAccountSystemID', '=', 'erp_jvdetail.chartOfAccountSystemID')
            ->where('chartofaccounts.isActive',0)
            ->where('erp_jvdetail.jvMasterAutoId', $input['jvMasterAutoId'])
            ->groupBy('chartofaccounts.AccountCode');
            
            if($query->count() > 0)
            {
                $inActiveAccounts = $query->pluck('AccountCode');
                $lastKey = count($inActiveAccounts) - 1;


                $msg = '';
                foreach($inActiveAccounts as $key => $account)
                {   
                    if ($key != $lastKey) {
                        $msg .= ' '.$account.' ,';
                    }
                    else
                    {
                        $msg .= ' '.$account;
                    }
                   
                }

                if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']) {
                    return [
                        "success" => false,
                        "message" => "The Chart of Account/s are Inactive"
                    ];
                }
                else{
                    return $this->sendError("The Chart of Account/s $msg are Inactive, update it as active/change the GL code to proceed.",500,['type' => 'ca_inactive']);
                }
            }

            $documentDate = $input['JVdate'];
            $monthBegin = $input['FYPeriodDateFrom'];
            $monthEnd = $input['FYPeriodDateTo'];
            if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
            } else {
                if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                    return [
                        "success" => false,
                        "message" => "Document date is not within the selected financial period !"
                    ];
                }
                else{
                    return $this->sendError('Document date is not within the selected financial period !', 500);
                }
            }

            $checkItems = JvDetail::where('jvMasterAutoId', $id)
                ->count();
            if ($checkItems == 0) {
                if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                    return [
                        "success" => false,
                        "message" => "Journal Voucher should have at least one item"
                    ];
                }
                else{
                    return $this->sendError('Journal Voucher should have at least one item', 500);
                }
            }

            if ($jvMaster->jvType != 4) {
                $checkQuantity = JvDetail::where('jvMasterAutoId', $id)
                    ->where('debitAmount', '<=', 0)
                    ->where('creditAmount', '<=', 0)
                    ->count();
                if ($checkQuantity > 0) {
                    if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                        return [
                            "success" => false,
                            "message" => "Amount should be greater than 0 for debit amount or credit amount"
                        ];
                    }
                    else{
                        return $this->sendError('Amount should be greater than 0 for debit amount or credit amount', 500);
                    }
                }
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
                    if ($jvMaster->jvType != 5) {
                        $checkDepartmentActive = SegmentMaster::where('serviceLineSystemID', $updateItem->serviceLineSystemID)
                            ->where('isActive', 1)
                            ->first();
                        if (empty($checkDepartmentActive)) {
                            $updateItem->serviceLineSystemID = null;
                            $updateItem->serviceLineCode = null;
                            array_push($finalError['active_serviceLine'], $updateItem->glAccount);
                            $error_count++;
                        }
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
                if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                    return [
                        "success" => false,
                        "message" => "You cannot confirm this document."
                    ];
                }
                else{
                    return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
                }
            }

            $JvDetailDebitSum = JvDetail::where('jvMasterAutoId', $id)
                ->sum('debitAmount');

            $JvDetailCreditSum = JvDetail::where('jvMasterAutoId', $id)
                ->sum('creditAmount');

            if (round($JvDetailDebitSum, $currencyDecimalPlace) != round($JvDetailCreditSum, $currencyDecimalPlace)) {
                if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                    return [
                        "success" => false,
                        "message" => "Debit amount total and credit amount total is not matching"
                    ];
                }
                else{
                    return $this->sendError('Debit amount total and credit amount total is not matching', 500);
                }
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
                'amount' => $JvDetailDebitSum,
                'isAutoCreateDocument' => isset($input['isAutoCreateDocument'])
            );

            $confirm = \Helper::confirmDocument($params);

            if (!$confirm["success"]) {
                if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']) {
                    return [
                        "success" => false,
                        "message" => $confirm["message"]
                    ];
                }
                else{
                    return $this->sendError($confirm["message"], 500);
                }
            }
        }

        $employee = (isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']) ? UserTypeService::getSystemEmployee() : Helper::getEmployeeInfo();

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;

        $jvMaster = $this->jvMasterRepository->update($input, $id);

        if ($jvConfirmedYN == 1 && $prevJvConfirmedYN == 0) {
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']) {
                return [
                    "success" => true,
                    "data" => $jvMaster->toArray()
                ];
            }
            else{
                return $this->sendResponse($jvMaster->toArray(), 'Journal Voucher confirmed successfully');
            }
        }

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']) {
            return [
                "success" => true,
                "data" => $jvMaster->toArray()
            ];
        }
        else{
            return $this->sendResponse($jvMaster->toArray(), 'Journal Voucher updated successfully');
        }
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
            //$companyFinanceYear = $companyFinanceYear->where('isCurrent', -1);
        }
        $companyFinanceYear = $companyFinanceYear->get();

        $isGroupCompany = \Helper::checkIsCompanyGroup($companyId);

        $allSubCompanies = [];
        if ($isGroupCompany) {
            $subCompanies = \Helper::getSubCompaniesByGroupCompany($companyId);
            $allSubCompanies = Company::whereIn("companySystemID", $subCompanies)->where("isGroup",0)->get();
        }

        $assetAllocatePolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 61)
        ->where('companySystemID', $companyId)
        ->where('isYesNO', 1)
        ->first();
        
        $isProject_base = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
        ->where('companySystemID', $companyId)
        ->where('isYesNO', 1)
        ->exists();

        $projects = [];
        $projects = ErpProjectMaster::where('companySystemID', $companyId)
                                        ->get();

        $output = array('yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'isGroupCompany' => $isGroupCompany,
            'currencies' => $currencies,
            'financialYears' => $financialYears,
            'allSubCompanies' => $allSubCompanies,
            'assetAllocatePolicy' => $assetAllocatePolicy ? true : false,
            'companyFinanceYear' => $companyFinanceYear,
            'segments' => $segments,
            'isProjectBase' => $isProject_base,
            'projects' => $projects
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

        $search = $request->input('search.value');

        $invMaster = $this->jvMasterRepository->jvMasterListQuery($request, $input, $search);

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
        $jvMasterData = $this->jvMasterRepository->with(['created_by', 'confirmed_by', 'modified_by', 'transactioncurrency', 'company', 'detail' => function ($query) {
            $query->with('project','segment');
        }, 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 17);
        },'audit_trial.modified_by'])->findWithoutFail($id);

        if (empty($jvMasterData)) {
            return $this->sendError('Jv Master not found');
        }

        $companyId = $jvMasterData->companySystemID;
        $isProject_base = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
                        ->where('companySystemID', $companyId)
                        ->where('isYesNO', 1)
                        ->exists();

        $jvMasterData['isProject_base'] = $isProject_base;

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
	/*accruvalfromop.accrualAmount,*/
	IFNULL(accruvalfromop.rptAmount,0) as accrualAmount ,
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
        $disk = Helper::policyWiseDisk($input['companySystemID'], 'public');

        $checkProjectSelectionPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
				->where('companySystemID', $input['companySystemID'])
				->first();

        if ($checkProjectSelectionPolicy->isYesNO == 0) {
            if ($exists = Storage::disk($disk)->exists('standard_jv_template/standard_jv_upload_template.xlsx')) {
                return Storage::disk($disk)->download('standard_jv_template/standard_jv_upload_template.xlsx', 'standard_jv_upload_template.xlsx');
            } else{
                return $this->sendError('Attachments not found', 500);
            }
        } else {
            if ($exists = Storage::disk($disk)->exists('standard_jv_template/standard_jv_upload_with_project_template.xlsx')) {
                return Storage::disk($disk)->download('standard_jv_template/standard_jv_upload_with_project_template.xlsx', 'standard_jv_upload_with_project_template.xlsx');
            } else{
                return $this->sendError('Attachments not found', 500);
            }
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

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            $employee = UserTypeService::getSystemEmployee();
            $empID = $employee->employeeSystemID;
        }
        else{
            $empID = \Helper::getEmployeeSystemID();
        }

        $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyID)
            ->where('documentSystemID', 17)
            ->first();

        $grvMasters = DB::table('erp_documentapproved')->select(
            'employeesdepartments.approvalDeligated',
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
        )->join('erp_jvmaster', function ($query) use ($companyID) {
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

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
        }
        else{
            $grvMasters->join('employeesdepartments', function ($query) use ($companyID, $empID, $serviceLinePolicy) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
                if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                    $query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
                }
                $query->where('employeesdepartments.documentSystemID', 17)
                    ->where('employeesdepartments.companySystemID', $companyID)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            });

            $search = $request->input('search.value');

            if ($search) {
                $search = str_replace("\\", "\\\\", $search);
                $grvMasters = $grvMasters->where(function ($query) use ($search) {
                    $query->where('JVcode', 'LIKE', "%{$search}%")
                        ->orWhere('JVNarration', 'LIKE', "%{$search}%");
                });
            }
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $grvMasters = [];
        }

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            if(!empty($grvMasters)){
                $grvMasters = $grvMasters->where('erp_jvmaster.jvMasterAutoId',$input['jvMasterAutoId'])->first();
                return [
                    "success" => true,
                    "data" => $grvMasters
                ];
            }
            else{
                return [
                    "success" => false,
                    "message" => "Employee discharged"
                ];
            }
        }
        else{
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
        $input = $request->all();
        $jvMasterData = JvMaster::find($request->jvMasterAutoId);

        if (($jvMasterData->jvType == 1 || $jvMasterData->jvType == 5) && $jvMasterData->isReverseAccYN == 0) {

            $formattedJvDateR = Carbon::parse($jvMasterData->JVdate)->format('Y-m-01');
            $firstDayNextMonth = Carbon::parse($formattedJvDateR)->addMonth()->firstOfMonth();
            $formattedDate = date("Y-m-d", strtotime($firstDayNextMonth));

            $companyFinanceYear = collect(\DB::select("SELECT companyFinanceYearID,bigginingDate,endingDate FROM companyfinanceyear WHERE companySystemID = " . $jvMasterData->companySystemID . " AND isDeleted = 0 AND date('" . $formattedDate . "') BETWEEN bigginingDate AND endingDate"))->first();

            if (empty($companyFinanceYear)) {
                return $this->sendError('Financial year not created or not active for reversal document. You cannot approve this document.');
            }

            $companyFinancePeriod = collect(\DB::select("SELECT companyFinancePeriodID,dateFrom, dateTo FROM companyfinanceperiod WHERE companySystemID = " . $jvMasterData->companySystemID . " AND departmentSystemID = 5 AND isActive = -1 AND companyFinanceYearID = " . $companyFinanceYear->companyFinanceYearID . " AND date('" . $formattedDate . "') BETWEEN dateFrom AND dateTo"))->first();

            if (empty($companyFinancePeriod)) {
                return $this->sendError('Financial period not created or not active for reversal document. You cannot approve this document.');
            }
        }

        $approve = \Helper::approveDocument($input);
        if (!$approve["success"]) {
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                return [
                    "success" => false,
                    "message" => $approve["message"]
                ];
            }
            else{
                return $this->sendError($approve["message"]);
            }
        } else {
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                return [
                    "success" => true,
                    "data" => null
                ];
            }
            else{
                return $this->sendResponse(array(), $approve["message"]);
            }
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

        $filter = '';
        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\\\\\", $search);
            $filter = " AND ( pomaster.purchaseOrderCode LIKE '%{$search}%') OR ( podetail.itemPrimaryCode LIKE '%{$search}%') OR ( podetail.itemDescription LIKE '%{$search}%') OR ( pomaster.supplierName LIKE '%{$search}%')";
        }

        $formattedJVdate = Carbon::parse($jvMasterData->JVdate)->format('Y-m-d');

        $qry = "SELECT
                podetail.purchaseOrderDetailsID,
                pomaster.purchaseOrderID,
                pomaster.poType,
                pomaster.purchaseOrderCode,
                pomaster.serviceLineSystemID,
                pomaster.serviceLine,
                pomaster.expectedDeliveryDate,
                pomaster.approvedDate,
                podetail.itemPrimaryCode,
                podetail.itemDescription,
                IF (
                podetail.financeGLcodePL IS NULL
                OR podetail.financeGLcodePL = '',
                podetail.financeGLcodebBS,
                podetail.financeGLcodePL
            ) AS glCode,
            IF (
                podetail.financeGLcodePL IS NULL
                OR podetail.financeGLcodePL = '',
                podetail.financeGLcodebBSSystemID,
                podetail.financeGLcodePLSystemID
            ) AS glCodeSystemID,
                pomaster.supplierName,
                podetail.poSum AS poCost,
                IFNULL(grvdetail.grvSum, 0) AS grvCost,
                (
                    podetail.poSum - IFNULL(grvdetail.grvSum, 0)
                ) AS balanceCost
            FROM
                erp_purchaseordermaster AS pomaster
            INNER JOIN (
                SELECT
                    GRVcostPerUnitComRptCur * noQty AS poSum,
                    purchaseOrderDetailsID,
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
                    WHERE erp_purchaseorderdetails.itemFinanceCategoryID IN (2, 4)
            ) AS podetail ON podetail.purchaseOrderMasterID = pomaster.purchaseOrderID
            LEFT JOIN (
                SELECT
                    purchaseOrderMastertID,
                    purchaseOrderDetailsID,
                    sum(GRVcostPerUnitComRptCur * noQty) as GRVSum
                FROM
                    erp_grvdetails
                INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID
                WHERE
                    grvTypeID = 2
                AND DATE(grvDate) <= '$formattedJVdate' AND erp_grvmaster.companySystemID = $companySystemID
                group by purchaseOrderDetailsID
            ) AS grvdetail ON grvdetail.purchaseOrderDetailsID = podetail.purchaseOrderDetailsID
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
                round(balanceCost, 2) > 0";

        //echo $qry;
        //exit();
        $invMaster = DB::select($qry);

        if ($input['temptype'] == 0) {
            return $invMaster;
        }

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

        DocumentApproved::where('documentSystemCode', $jvMasterAutoId)
            ->where('companySystemID', $jvMasterData->companySystemID)
            ->where('documentSystemID', $jvMasterData->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($jvMasterData->documentSystemID,$jvMasterAutoId,$input['reopenComments'],'Reopened');

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
                $bookDetail->setAppends([]);
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
                if (!isset($formatChk['gl_account']) || !isset($formatChk['gl_account_description']) || !isset($formatChk['comments']) || !isset($formatChk['debit_amount']) || !isset($formatChk['credit_amount'])) {
                    return $this->sendError('Uploaded data format is invalid', 500);
                }
            }

            $checkProjectSelectionPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
				->where('companySystemID', $input['companySystemID'])
				->first();

            if ($checkProjectSelectionPolicy->isYesNO == 0) {
                $record = \Excel::selectSheets('Sheet1')->load(Storage::disk('local')->url('app/' . $originalFileName), function ($reader) {
                })->select(array('gl_account', 'gl_account_description', 'department', 'client_contract', 'comments', 'debit_amount', 'credit_amount'))->get()->toArray();
            } else {
                $record = \Excel::selectSheets('Sheet1')->load(Storage::disk('local')->url('app/' . $originalFileName), function ($reader) {
                })->select(array('gl_account', 'gl_account_description', 'project', 'department', 'client_contract', 'comments', 'debit_amount', 'credit_amount'))->get()->toArray();
            }

            $count = 0;
            $valid = 0;
            $failed_gl = [];
            if (count($record) > 0) {

                $jvMasterData = JvMaster::find($input['jvMasterAutoId']);

                if (empty($jvMasterData)) {
                    return $this->sendError('Journal Voucher not found');
                }
                $line_nu = 1;
                foreach ($record as $val) {

                    $serviceLineSystemID = null;
                    $serviceLineCode = '';
                    $chartOfAccountSystemID = null;
                    $glAccountDescription = '';
                    $contractUID = null;
                    $debitAmount = 0;
                    $creditAmount = 0;
                    $projectID = null;
                    $is_failed = false;
                    $line_nu++;
                    if (isset($val['gl_account']) && !is_null($val['gl_account'])) {
                        $count ++;
                        $department = isset($val['department']) ? $val['department'] : '-';
                        $segmentData = SegmentMaster::where('ServiceLineDes', $department)
                            ->where('companySystemID', $jvMasterData->companySystemID)
                            ->first();
                        if ($segmentData) {
                            $serviceLineSystemID = $segmentData['serviceLineSystemID'];
                            $serviceLineCode = $segmentData['ServiceLineCode'];
                        }

                        
                      

                        $is_char_acc_exist = ChartOfAccount::where('AccountCode', $val['gl_account'])
                                ->first();
                        if(!isset($is_char_acc_exist))
                        {
                            $is_failed = true;
                        }
                                

                        $chartOfAccountData = chartofaccountsassigned::where('AccountCode', $val['gl_account'])
                            ->where('companySystemID', $jvMasterData->companySystemID)
                            ->where('isAssigned', -1)
                            ->first();

                            

                        if ($chartOfAccountData) {
                            $chartOfAccountSystemID = $chartOfAccountData->chartOfAccountSystemID;
                            $glAccountDescription = $chartOfAccountData->AccountDescription;
                        }
                        else
                        {
                            $is_failed = true;
                        }


                        $client_contract = isset($val['client_contract']) ? $val['client_contract'] : '-';
                        $contract = Contract::where('ContractNumber', $client_contract)->where('companySystemID', $jvMasterData->companySystemID)->first();
                        if ($contract) {
                            $contractUID = $contract->contractUID;
                        }else if(strtolower($client_contract == 'x')){
                            $contractUID =  159;
                            $val['client_contract'] = strtoupper($client_contract);
                        }else{
                            $contractUID =  159;
                            $val['client_contract'] = 'X';
                        }
                        if (isset($val['debit_amount']) && $val['debit_amount'] != '') {
                            $debitAmount = $val['debit_amount'];
                        }
                        if ($val['credit_amount'] != '') {
                            $creditAmount = $val['credit_amount'];
                        }
                        if (isset($val['project']) && $val['project'] != '') {
                            $project = ErpProjectMaster::whereRaw("CONCAT(projectCode, '-', description) = ?", [$val['project']])
                                    ->where('companySystemID', $jvMasterData->companySystemID)
                                    ->first();
                            if(!empty($project)){
                                $projectID = $project['id'];
                            }else {
                                $projectID = null;
                            }    
                        }

                        if(!$is_failed)
                        {
                            $valid++;
                            $data = [];
                            $data['jvMasterAutoId'] = $input['jvMasterAutoId'];
                            $data['documentSystemID'] = $jvMasterData->documentSystemID;
                            $data['documentID'] = $jvMasterData->documentID;
                            $data['companySystemID'] = $jvMasterData->companySystemID;
                            $data['companyID'] = $jvMasterData->companyID;
                            $data['serviceLineSystemID'] = $serviceLineSystemID;
                            $data['serviceLineCode'] = $serviceLineCode;
                            $data['chartOfAccountSystemID'] = $chartOfAccountSystemID;
                            $data['glAccount'] = $val['gl_account'];
                            $data['glAccountDescription'] = $glAccountDescription;
                            $data['contractUID'] = $contractUID;
                            $data['clientContractID'] = $val['client_contract'];
                            $data['comments'] = $val['comments'];
                            $data['currencyID'] = $jvMasterData->currencyID;
                            $data['currencyER'] = $jvMasterData->currencyER;
                            $data['debitAmount'] = $debitAmount;
                            $data['creditAmount'] = $creditAmount;
                            $data['createdPcID'] = gethostname();
                            $data['createdUserID'] = \Helper::getEmployeeID();
                            $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                            $data['createdDateTime'] = NOW();
                            $data['timeStamp'] = NOW();
                            $data['detail_project_id'] = $projectID;
                            $finalData[] = $data;
                        }
                        else
                        {
                            $info['line'] = $line_nu;
                            $info['gl'] = $val['gl_account'];
                            array_push($failed_gl,$info);
                        }

                    }
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
            if($count == $valid)
            {
                $details['detail'] = $failed_gl;
                $details['valid'] = true;
                return $this->sendResponse($details, 'All JV Details uploaded successfully');
            }
            else if($count > 0)
            {
                if($valid == 0)
                {
                    $details['detail'] = $failed_gl;
                    $details['valid'] = false;
                    return $this->sendResponse($details, 'Out Of '.$count.' All JV Details fail to upload ');
                }
                else
                {
                    $details['detail'] = $failed_gl;
                    $details['valid'] = true;
                    return $this->sendResponse($details, 'Out Of '.$count.' JV Details '.$valid.' JV Details uploaded successfully');
                }
            }
            
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
        //Storage::disk('local')->delete($originalFileName);

    }


    public function printJournalVoucher(Request $request)
    {
        $id = $request->get('jvMasterAutoId');

        $jvMasterData = jvMaster::find($id);
        if (empty($jvMasterData)) {
            return $this->sendError('Jv Master not found');
        }

        $jvMasterDataLine = jvMaster::where('jvMasterAutoId', $id)->with(['created_by', 'confirmed_by', 'modified_by', 'transactioncurrency', 'company', 'detail' => function ($query) {
            $query->with('project','segment');
        }, 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 17);
        }])->first();

        if (empty($jvMasterDataLine)) {
            return $this->sendError('Jv Master not found');
        }

        $refernaceDoc = \Helper::getCompanyDocRefNo($jvMasterDataLine->companySystemID, $jvMasterDataLine->documentSystemID);

        $companyId = $jvMasterDataLine->companySystemID;
        $isProject_base = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
                        ->where('companySystemID', $companyId)
                        ->where('isYesNO', 1)
                        ->exists();

        $transDecimal = 2;

        if ($jvMasterDataLine->transactioncurrency) {
            $transDecimal = $jvMasterDataLine->transactioncurrency->DecimalPlaces;
        }

        $debitTotal = JvDetail::where('jvMasterAutoId', $id)
            ->sum('debitAmount');

        $creditTotal = JvDetail::where('jvMasterAutoId', $id)
            ->sum('creditAmount');

        $order = array(
            'masterdata' => $jvMasterDataLine,
            'docRef' => $refernaceDoc,
            'transDecimal' => $transDecimal,
            'debitTotal' => $debitTotal,
            'isProject_base' => $isProject_base,
            'creditTotal' => $creditTotal
        );

        $time = strtotime("now");
        $fileName = 'journal_voucher_' . $id . '_' . $time . '.pdf';
        $html = view('print.journal_voucher', $order);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4', 'portrait')->setWarnings(false)->stream($fileName);
    }

    public function approvalPreCheckJV(Request $request)
    {
        $input = $request->all();
        $approve = \Helper::postedDatePromptInFinalApproval($request);
        if (!$approve["success"]) {
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                return [
                    "success" => false,
                    "message" => $approve["message"]
                ];
            }
            else{
                return $this->sendError($approve["message"], 500, ['type' => $approve["type"]]);
            }
        } else {
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                return [
                    "success" => true,
                    "data" => $approve["type"],
                ];
            }
            else{
                return $this->sendResponse(array('type' => $approve["type"]), $approve["message"]);
            }
        }

    }

    public function exportJournalVoucherForPOAccrualJVDetail(Request $request)
    {

        $companySystemID = $request['companyId'];
        $jvMasterAutoId = $request['jvMasterAutoId'];

        $jvMasterData = jvMaster::find($jvMasterAutoId);
        if (empty($jvMasterData)) {
            return $this->sendError('Jv Master not found');
        }

        $type = $request->type;

        $filter = '';
        $search = $request->input('search.value');
        if ($search) {
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
	IF (
	podetail.financeGLcodePL IS NULL
	OR podetail.financeGLcodePL = '',
	podetail.financeGLcodebBS,
	podetail.financeGLcodePL
) AS glCode,
IF (
	podetail.financeGLcodePL IS NULL
	OR podetail.financeGLcodePL = '',
	podetail.financeGLcodebBSSystemID,
	podetail.financeGLcodePLSystemID
) AS glCodeSystemID,
	pomaster.supplierName,
	podetail.poSum AS poCost,
	IFNULL(grvdetail.grvSum, 0) AS grvCost,
	(
		podetail.poSum - IFNULL(grvdetail.grvSum, 0)
	) AS balanceCost
FROM
	erp_purchaseordermaster AS pomaster
INNER JOIN (
	SELECT
        GRVcostPerUnitComRptCur * noQty AS poSum,
        purchaseOrderDetailsID,
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
) AS podetail ON podetail.purchaseOrderMasterID = pomaster.purchaseOrderID
LEFT JOIN (
	SELECT
		purchaseOrderMastertID,
		purchaseOrderDetailsID,
		sum(GRVcostPerUnitComRptCur * noQty) as GRVSum
	FROM
		erp_grvdetails
	INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID
	WHERE
		grvTypeID = 2
	AND DATE(grvDate) <= '$formattedJVdate' AND erp_grvmaster.companySystemID = $companySystemID
    group by purchaseOrderDetailsID
) AS grvdetail ON grvdetail.purchaseOrderDetailsID = podetail.purchaseOrderDetailsID
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
	round(balanceCost, 2) > 0";

        //echo $qry;
        //exit();
        $invMaster = DB::select($qry);

        if ($invMaster) {
            $x = 0;
            foreach ($invMaster as $val) {
                $data[$x]['PO Code'] = $val->purchaseOrderCode;
                $data[$x]['Department'] = $val->serviceLine;
                $data[$x]['PO Expected Delivery Date'] = $val->expectedDeliveryDate;
                $data[$x]['PO Approved'] = $val->approvedDate;
                $data[$x]['Item Code'] = $val->itemPrimaryCode;
                $data[$x]['Item Description'] = $val->itemDescription;
                $data[$x]['GL Code'] = $val->glCode;
                $data[$x]['Supplier Name'] = $val->supplierName;
                $data[$x]['PO Total Cost'] = round($val->poCost, $request->fractionTot);
                $data[$x]['GRV Total Cost'] = round($val->grvCost, $request->fractionTot);
                $data[$x]['Balance'] = round($val->balanceCost, $request->fractionTot);
                $x++;
            }
        }

         \Excel::create('accrual_export', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse(array(), 'successfully export');
    }

    public function amendJournalVoucherReview(Request $request)
    {
        $input = $request->all();

        $id = $input['jvMasterAutoId'];

        $employee = \Helper::getEmployeeInfo();
        $emails = array();

        $jvMaster = JvMaster::find($id);

        if (empty($jvMaster)) {
            return $this->sendError('Journal voucher not found');
        }

        if ($jvMaster->confirmedYN == 0) {
            return $this->sendError('You cannot return back to amend this journal voucher, it is not confirmed');
        }

        $documentAutoId = $id;
        $documentSystemID = $jvMaster->documentSystemID;

        if($jvMaster->approved == -1){
            $validateFinanceYear = ValidateDocumentAmend::validateFinanceYear($documentAutoId,$documentSystemID);
            if(isset($validateFinanceYear['status']) && $validateFinanceYear['status'] == false){
                if(isset($validateFinanceYear['message']) && $validateFinanceYear['message']){
                    return $this->sendError($validateFinanceYear['message']);
                }
            }
    
            $validateFinancePeriod = ValidateDocumentAmend::validateFinancePeriod($documentAutoId,$documentSystemID);
            if(isset($validateFinancePeriod['status']) && $validateFinancePeriod['status'] == false){
                if(isset($validateFinancePeriod['message']) && $validateFinancePeriod['message']){
                    return $this->sendError($validateFinancePeriod['message']);
                }
            }
    
            $validatePendingGlPost = ValidateDocumentAmend::validatePendingGlPost($documentAutoId,$documentSystemID);
            if(isset($validatePendingGlPost['status']) && $validatePendingGlPost['status'] == false){
                if(isset($validatePendingGlPost['message']) && $validatePendingGlPost['message']){
                    return $this->sendError($validatePendingGlPost['message']);
                }
            }    
        }

        $emailBody = '<p>' . $jvMaster->JVcode . ' has been return back to amend by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';
        $emailSubject = $jvMaster->JVcode . ' has been return back to amend';

        DB::beginTransaction();
        try {

            //sending email to relevant party
            if ($jvMaster->confirmedYN == 1) {
                $emails[] = array('empSystemID' => $jvMaster->confirmedByEmpSystemID,
                    'companySystemID' => $jvMaster->companySystemID,
                    'docSystemID' => $jvMaster->documentSystemID,
                    'docSystemCode' => $jvMaster->jvMasterAutoId,
                    'alertMessage' => $emailSubject,
                    'emailAlertMessage' => $emailBody);
            }

            $documentApproval = DocumentApproved::where('companySystemID', $jvMaster->companySystemID)
                ->where('documentSystemCode', $id)
                ->where('documentSystemID', $jvMaster->documentSystemID)
                ->get();

            foreach ($documentApproval as $da) {
                if ($da->approvedYN == -1) {
                    $emails[] = array('empSystemID' => $da->employeeSystemID,
                        'companySystemID' => $jvMaster->companySystemID,
                        'docSystemID' => $jvMaster->documentSystemID,
                        'docSystemCode' => $jvMaster->jvMasterAutoId,
                        'alertMessage' => $emailSubject,
                        'emailAlertMessage' => $emailBody);
                }
            }

            $sendEmail = \Email::sendEmail($emails);
            if (!$sendEmail["success"]) {
                return $this->sendError($sendEmail["message"], 500);
            }

            //deleting from approval table
            $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
                ->where('companySystemID', $jvMaster->companySystemID)
                ->where('documentSystemID', $jvMaster->documentSystemID)
                ->delete();

            //deleting from general ledger table
            $deleteGLData = GeneralLedger::where('documentSystemCode', $id)
                ->where('companySystemID', $jvMaster->companySystemID)
                ->where('documentSystemID', $jvMaster->documentSystemID)
                ->delete();

            BudgetConsumedData::where('documentSystemCode', $id)
                ->where('companySystemID', $jvMaster->companySystemID)
                ->where('documentSystemID', $jvMaster->documentSystemID)
                ->delete();

            // updating fields
            $jvMaster->confirmedYN = 0;
            $jvMaster->confirmedByEmpSystemID = null;
            $jvMaster->confirmedByEmpID = null;
            $jvMaster->confirmedByName = null;
            $jvMaster->confirmedDate = null;
            $jvMaster->RollLevForApp_curr = 1;

            $jvMaster->approved = 0;
            $jvMaster->approvedByUserSystemID = null;
            $jvMaster->approvedByUserID = null;
            $jvMaster->approvedDate = null;
            $jvMaster->postedDate = null;
            $jvMaster->save();

            AuditTrial::createAuditTrial($jvMaster->documentSystemID,$id,$input['returnComment'],'returned back to amend');

            DB::commit();
            return $this->sendResponse($jvMaster->toArray(), 'Journal voucher amend saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function journalVoucherBudgetUpload(Request $request)
    {
        $input = $request->all();

        $id = isset($input['jvMasterAutoId']) ? $input['jvMasterAutoId'] : 0;

        $employee = \Helper::getEmployeeInfo();

        $jvMaster = JvMaster::find($id);

        if (empty($jvMaster)) {
            return $this->sendError('Journal voucher not found');
        }

        if ($jvMaster->approved != -1) {
            return $this->sendError('You cannot upload to budget this journal voucher, It is not approved', 500);
        }

        $checkAlreadyAdded = BudgetConsumedData::where('documentSystemCode', $id)
            ->where('companySystemID', $jvMaster->companySystemID)
            ->where('documentSystemID', $jvMaster->documentSystemID)
            ->count();
        if ($checkAlreadyAdded > 0) {
            return $this->sendError('Cannot update. Already data is updated to budget consumed', 500);
        }

        DB::beginTransaction();
        try {

            //get data from general ledger table
            $glData = GeneralLedger::where('documentSystemCode', $id)
                ->where('companySystemID', $jvMaster->companySystemID)
                ->where('documentSystemID', $jvMaster->documentSystemID)
                ->where('glAccountTypeID', 2)
                ->get();

            if (count($glData) == 0) {
                return $this->sendError('There is no data to update', 500);
            }

            foreach ($glData as $val) {
                $tem = array();
                $tem['companySystemID'] = $val['companySystemID'];
                $tem['companyID'] = $val['companyID'];
                $tem['serviceLineSystemID'] = $val['serviceLineSystemID'];
                $tem['serviceLineCode'] = $val['serviceLineCode'];
                $tem['documentSystemID'] = $val['documentSystemID'];
                $tem['documentID'] = $val['documentID'];
                $tem['documentSystemCode'] = $val['documentSystemCode'];
                $tem['documentCode'] = $val['documentCode'];
                $tem['chartOfAccountID'] = $val['chartOfAccountSystemID'];
                $tem['GLCode'] = $val['glCode'];
                $tem['year'] = $val['documentYear'];
                $tem['month'] = $val['documentMonth'];
                $tem['consumedLocalCurrencyID'] = $val['documentLocalCurrencyID'];
                $tem['consumedLocalAmount'] = $val['documentLocalAmount'];
                $tem['consumedRptCurrencyID'] = $val['documentRptCurrencyID'];
                $tem['consumedRptAmount'] = $val['documentRptAmount'];
                $tem['timestamp'] = date('d/m/Y H:i:s A');
                BudgetConsumedData::insert($tem);
                //$this->budgetConsumedDataRepository->create($tem);
            }

            AuditTrial::createAuditTrial($jvMaster->documentSystemID,$id,'','budget uploaded');

            DB::commit();
            return $this->sendResponse($glData->toArray(), 'Journal voucher uploaded to budget successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function copyJV(Request $request)
    {
        $input = $request->all();

        $id = isset($input['jvMasterAutoId']) ? $input['jvMasterAutoId'] : 0;
        $jvMaster = JvMaster::find($id);

        if (empty($jvMaster)) {
            return $this->sendError('Journal voucher not found');
        }

        if ($jvMaster->approved != -1) {
            return $this->sendError('You cannot copy this journal voucher, It is not approved', 500);
        }

        $formattedDate = Carbon::now()->format("Y-m-d");

        $companyFinanceYear = CompanyFinanceYear::where('companySystemID', $input['companySystemID'])
                                                ->where('isActive', -1)
                                                ->where('isCurrent', -1)
                                                ->first();

        if (!$companyFinanceYear) {
            return $this->sendError("Financial year not created or not active");
        }


        $companyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $input['companySystemID'])
                                                     ->where('isActive', -1)
                                                     ->where('isCurrent', -1)
                                                     ->where('departmentSystemID', 5)
                                                     ->where('companyFinanceYearID', $companyFinanceYear->companyFinanceYearID)
                                                     ->first();

        if (!$companyFinancePeriod) {
            return $this->sendError("Financial period not created or not active");
        }


        $jvInsertData = $jvMaster->toArray();


        $userID = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($userID);
       
        $jvInsertData['companyFinanceYearID'] = $companyFinanceYear->companyFinanceYearID;
        $jvInsertData['companyFinancePeriodID'] = $companyFinancePeriod->companyFinancePeriodID;
        $jvInsertData['FYBiggin'] = $companyFinanceYear->bigginingDate;
        $jvInsertData['FYEnd'] = $companyFinanceYear->endingDate;
        $jvInsertData['JVdate'] = Carbon::now();

        $jvInsertData['FYPeriodDateFrom'] = $companyFinancePeriod->dateFrom;
        $jvInsertData['FYPeriodDateTo'] = $companyFinancePeriod->dateTo;
        
        $documentDate = $jvInsertData['JVdate'];
        $monthBegin = $jvInsertData['FYPeriodDateFrom'];
        $monthEnd = $jvInsertData['FYPeriodDateTo'];

        if (($documentDate < $monthBegin) || ($documentDate > $monthEnd)) {
            return $this->sendError('Current date is not within the financial period!, you cannot copy JV');
        } 

        $jvInsertData['createdPcID'] = gethostname();
        $jvInsertData['modifiedPc'] = gethostname();
        $jvInsertData['timestamp'] = Carbon::now();
        $jvInsertData['createdDateTime'] = Carbon::now();
        $jvInsertData['postedDate'] = null;
        $jvInsertData['createdUserID'] = $user->employee['empID'];
        $jvInsertData['modifiedUser'] = $user->employee['empID'];
        $jvInsertData['createdUserSystemID'] = $user->employee['employeeSystemID'];
        $jvInsertData['modifiedUserSystemID'] = $user->employee['employeeSystemID'];
       
        $lastSerial = JvMaster::where('companySystemID', $input['companySystemID'])
                                ->where('companyFinanceYearID', $companyFinanceYear->companyFinanceYearID)
                                ->orderBy('serialNo', 'desc')
                                ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

      
        $jvInsertData['serialNo'] = $lastSerialNumber;

        $documentMaster = DocumentMaster::where('documentSystemID', $jvInsertData['documentSystemID'])->first();

        if ($companyFinanceYear) {
            $startYear = $companyFinanceYear->bigginingDate;
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];
        } else {
            $finYear = date("Y");
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();

        $oldCode = $jvInsertData['JVcode'];

        if ($documentMaster) {
            $jvCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $jvInsertData['JVcode'] = $jvCode;
        }

        $jvInsertData['approved'] = 0;
        $jvInsertData['confirmedYN'] = 0;
        $jvInsertData['approvedByUserID'] = null;
        $jvInsertData['approvedByUserSystemID'] = null;
        $jvInsertData['approvedDate'] = null;
        $jvInsertData['confirmedByEmpID'] = null;
        $jvInsertData['confirmedByEmpSystemID'] = null;
        $jvInsertData['confirmedByName'] = null;
        $jvInsertData['confirmedDate'] = null;
        $jvInsertData['RollLevForApp_curr'] = 1;

        if (isset($input['reverseFlag']) && $input['reverseFlag']) {
            $jvInsertData['JVNarration'] = ($jvInsertData['JVNarration'] == " " || $jvInsertData['JVNarration'] == null) ? "Reversal JV for ". $oldCode : $jvInsertData['JVNarration']. " - Reversal JV for ". $oldCode;
        }

        DB::beginTransaction();
        try {
            $jvMasterRes = $this->jvMasterRepository->create($jvInsertData);

            $fetchJVDetail = JvDetail::where('jvMasterAutoId', $id)
                                            ->get()
                                            ->toArray();

            foreach ($fetchJVDetail as $key => $value) {
                $value['jvMasterAutoId'] = $jvMasterRes->jvMasterAutoId;

                if (isset($input['reverseFlag']) && $input['reverseFlag']) {
                    $debitAmount = $value['debitAmount'];
                    $creditAmount = $value['creditAmount'];
                    $value['debitAmount'] = $creditAmount;
                    $value['creditAmount'] = $debitAmount;
                }

                $value['createdDateTime'] = Carbon::now();
                $value['timeStamp'] = Carbon::now();
                $value['createdUserID'] = $user->employee['empID'];
                $value['createdUserSystemID'] = $user->employee['employeeSystemID'];
                $value['createdPcID'] = gethostname();

                $jvDetailRes = JvDetail::create($value);
            }

            DB::commit();
            return $this->sendResponse($jvMasterRes->jvMasterAutoId, 'JV Copied successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

    }
}
