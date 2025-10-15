<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRecurringVoucherSetupAPIRequest;
use App\Http\Requests\API\UpdateRecurringVoucherSetupAPIRequest;
use App\Models\BudgetConsumedData;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\CurrencyMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\EmployeesDepartment;
use App\Models\ErpProjectMaster;
use App\Models\GeneralLedger;
use App\Models\Months;
use App\Models\RecurringVoucherSetup;
use App\Models\RecurringVoucherSetupDetail;
use App\Models\RecurringVoucherSetupSchedule;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\RecurringVoucherSetupRepository;
use App\Repositories\UserRepository;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class RecurringVoucherSetupController
 * @package App\Http\Controllers\API
 */

class RecurringVoucherSetupAPIController extends AppBaseController
{
    /** @var  RecurringVoucherSetupRepository */
    private $recurringVoucherSetupRepository;
    private $userRepository;

    public function __construct(
        RecurringVoucherSetupRepository $recurringVoucherSetupRepo,
        UserRepository $userRepository
    )
    {
        $this->recurringVoucherSetupRepository = $recurringVoucherSetupRepo;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/recurringVoucherSetups",
     *      summary="getRecurringVoucherSetupList",
     *      tags={"RecurringVoucherSetup"},
     *      description="Get all RecurringVoucherSetups",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/RecurringVoucherSetup")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->recurringVoucherSetupRepository->pushCriteria(new RequestCriteria($request));
        $this->recurringVoucherSetupRepository->pushCriteria(new LimitOffsetCriteria($request));
        $recurringVoucherSetups = $this->recurringVoucherSetupRepository->all();

        return $this->sendResponse($recurringVoucherSetups->toArray(), trans('custom.recurring_voucher_setups_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/recurringVoucherSetups",
     *      summary="createRecurringVoucherSetup",
     *      tags={"RecurringVoucherSetup"},
     *      description="Create RecurringVoucherSetup",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/RecurringVoucherSetup"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRecurringVoucherSetupAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $validator = \Validator::make($input, [
            'schedule' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'noOfDayMonthYear' => 'required',
            'processDate' => 'required',
            'documentStatus' => 'required',
            'currencyID' => 'required',
            'documentType' => 'required',
            'narration' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $input['startDate'] = new Carbon($input['startDate']);
        $input['endDate'] = new Carbon($input['endDate']);
        $input['processDate'] = new Carbon($input['processDate']);
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdPcID'] = gethostname();
        $input['documentSystemID'] = '119';
        $input['documentID'] = 'RRV';

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        $company = Company::where('companySystemID', $input['companySystemID'])->first();

        if(!$company)
            return $this->sendError(trans('custom.company_details_not_found'));


        if(!isset($input['companyFinanceYearID']))
            return $this->sendError(trans('custom.company_finance_year_not_found'));


        $companyfinanceyear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])->where('companySystemID', $input['companySystemID'])->first();

        if ($companyfinanceyear) {
            $startYear = $companyfinanceyear['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];
        } else {
            $finYear = date("Y");
        }

        $lastSerial = RecurringVoucherSetup::where('companySystemID', $input['companySystemID'])->where('companyFinanceYearID', $input['companyFinanceYearID'])->orderBy('serialNo', 'desc')->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $input['serialNo'] = $lastSerialNumber;

        if ($documentMaster) {
            $rrvCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['RRVcode'] = $rrvCode;
        }

        $recurringVoucher = $this->recurringVoucherSetupRepository->create($input);

        return $this->sendResponse($recurringVoucher->toArray(), trans('custom.recurring_voucher_created_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/recurringVoucherSetups/{id}",
     *      summary="getRecurringVoucherSetupItem",
     *      tags={"RecurringVoucherSetup"},
     *      description="Get RecurringVoucherSetup",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RecurringVoucherSetup",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/RecurringVoucherSetup"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var RecurringVoucherSetup $recurringVoucherSetup */
        $recurringVoucherSetup = $this->recurringVoucherSetupRepository->with(['created_by', 'confirmed_by', 'modified_by', 'transactioncurrency'])->findWithoutFail($id);

        if (empty($recurringVoucherSetup)) {
            return $this->sendError(trans('custom.recurring_voucher_setup_not_found'));
        }

        return $this->sendResponse($recurringVoucherSetup->toArray(), trans('custom.recurring_voucher_setup_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/recurringVoucherSetups/{id}",
     *      summary="updateRecurringVoucherSetup",
     *      tags={"RecurringVoucherSetup"},
     *      description="Update RecurringVoucherSetup",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RecurringVoucherSetup",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/RecurringVoucherSetup"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRecurringVoucherSetupAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmedByName', 'confirmedByEmpID', 'confirmedDate', 'confirmed_by', 'confirmedByEmpSystemID', 'transactioncurrency', 'modified_by']);
        $input = $this->convertArrayToValue($input);

        $rrvMaster = $this->recurringVoucherSetupRepository->findWithoutFail($id);

        if (empty($rrvMaster)) {
            return $this->sendError(trans('custom.rrv_master_not_found'));
        }

        $rrvConfirmedYN = $input['confirmedYN'];
        $prevRrvConfirmedYN = $rrvMaster->confirmedYN;


        if (isset($input['startDate'])) {
            if ($input['startDate']) {
                $input['startDate'] = Carbon::parse($input['startDate']);
            }
        }

        if (isset($input['endDate'])) {
            if ($input['endDate']) {
                $input['endDate'] = Carbon::parse($input['endDate']);
            }
        }

        if (isset($input['processDate'])) {
            if ($input['processDate']) {
                $input['processDate'] = Carbon::parse($input['processDate']);
            }
        }

        $currencyDecimalPlace = \Helper::getCurrencyDecimalPlace($rrvMaster->currencyID);

        if ($prevRrvConfirmedYN == 0 && $rrvConfirmedYN == 1) {

            $validator = \Validator::make($input, [
                'companyFinanceYearID' => 'required|numeric|min:1',
                'startDate' => 'required',
                'schedule' => 'required',
                'endDate' => 'required',
                'processDate' => 'required',
                'noOfDayMonthYear' => 'required',
                'currencyID' => 'required|numeric|min:1',
                'narration' => 'required',
                'documentStatus' => 'required',
                'documentType' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $query = RecurringVoucherSetupDetail::selectRaw("chartofaccounts.AccountCode")
                ->join('chartofaccounts', 'chartofaccounts.chartOfAccountSystemID', '=', 'recurring_voucher_setup_detail.chartOfAccountSystemID')
                ->where('chartofaccounts.isActive',0)
                ->where('recurring_voucher_setup_detail.recurringVoucherAutoId', $input['recurringVoucherAutoId'])
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

                return $this->sendError(trans('custom.chart_of_accounts_inactive_update_active', ['accounts' => $msg]),500,['type' => 'ca_inactive']);

            }

            $rrvDetails = RecurringVoucherSetupDetail::where('recurringVoucherAutoId', $id)->get();
            if (count($rrvDetails) == 0) {
                return $this->sendError(trans('custom.recurring_voucher_should_have_at_least_one_item'), 500);
            }

            foreach ($rrvDetails as $item) {
                $updateItem = RecurringVoucherSetupDetail::find($item['rrvDetailAutoId']);

                if (($updateItem->serviceLineSystemID == 0) && is_null($updateItem->serviceLineCode)) {
                    return $this->sendError(trans('custom.cannot_confirm_segment_not_updated'), 500);
                }
            }

            $checkQuantity = RecurringVoucherSetupDetail::where('recurringVoucherAutoId', $id)
                ->where('debitAmount', '<=', 0)
                ->where('creditAmount', '<=', 0)
                ->count();
            if ($checkQuantity > 0) {
                return $this->sendError(trans('custom.amount_should_be_greater_than_zero'), 500);
            }

            $rrvDetailDebitSum = RecurringVoucherSetupDetail::where('recurringVoucherAutoId', $id)->sum('debitAmount');

            $rrvDetailCreditSum = RecurringVoucherSetupDetail::where('recurringVoucherAutoId', $id)->sum('creditAmount');

            if (round($rrvDetailDebitSum, $currencyDecimalPlace) != round($rrvDetailCreditSum, $currencyDecimalPlace)) {
                return $this->sendError(trans('custom.debit_amount_total_credit_amount_total_not_matching'), 500);
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
                'amount' => $rrvDetailDebitSum
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

        $rrvMaster = $this->recurringVoucherSetupRepository->update($input, $id);

        if ($rrvConfirmedYN == 1 && $prevRrvConfirmedYN == 0) {
            return $this->sendResponse($rrvMaster->toArray(), trans('custom.recurring_voucher_confirmed_successfully'));
        }

        return $this->sendResponse($rrvMaster->toArray(), trans('custom.recurring_voucher_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/recurringVoucherSetups/{id}",
     *      summary="deleteRecurringVoucherSetup",
     *      tags={"RecurringVoucherSetup"},
     *      description="Delete RecurringVoucherSetup",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RecurringVoucherSetup",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var RecurringVoucherSetup $recurringVoucherSetup */
        $recurringVoucherSetup = $this->recurringVoucherSetupRepository->findWithoutFail($id);

        if (empty($recurringVoucherSetup)) {
            return $this->sendError(trans('custom.recurring_voucher_setup_not_found'));
        }

        $recurringVoucherSetup->delete();

        return $this->sendSuccess(trans('custom.recurring_voucher_setup_deleted_successfully'));
    }

    public function getRecurringVoucherMasterFormData(Request $request)
    {
        $companyId = $request['companyId'];

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $segments = SegmentMaster::where("companySystemID", $companyId)->approved()->withAssigned($companyId)->where('isActive', 1)->get();

        $currencies = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))->get();

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear"));
        $companyFinanceYear = $companyFinanceYear->where('companySystemID', $companyId);
        if (isset($request['type']) && ($request['type'] == 'add' || $request['type'] == 'edit')) {
            $companyFinanceYear = $companyFinanceYear->where('isActive', -1);
        }
        $companyFinanceYear = $companyFinanceYear->get();

        $isProjectBase = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)->where('companySystemID', $companyId)->where('isYesNO', 1)->exists();

        $projects = ErpProjectMaster::where('companySystemID', $companyId)->get();

        $output = array(
            'currencies' => $currencies,
            'companyFinanceYear' => $companyFinanceYear,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'segments' => $segments,
            'projects' => $projects,
            'isProjectBase' => $isProjectBase,
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function getRecurringVoucherMasterView(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approved', 'month', 'documentType'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $invMaster = $this->recurringVoucherSetupRepository->rrvMasterListQuery($request, $input, $search);

        return \DataTables::eloquent($invMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('recurringVoucherAutoId', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getRecurringVoucherMasterRecord(Request $request)
    {
        $id = $request->get('matchDocumentMasterAutoID');
        $rrvMasterData = $this->recurringVoucherSetupRepository->with(['created_by', 'confirmed_by', 'modified_by', 'transactioncurrency', 'company', 'detail' => function ($query) {
            $query->with('project','segment');
        }, 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 119);
        },'audit_trial.modified_by'])->findWithoutFail($id);

        if (empty($rrvMasterData)) {
            return $this->sendError(trans('custom.rrv_master_not_found_1'));
        }

        $companyId = $rrvMasterData->companySystemID;
        $isProject_base = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
            ->where('companySystemID', $companyId)
            ->where('isYesNO', 1)
            ->exists();

        $rrvMasterData['isProject_base'] = $isProject_base;

        return $this->sendResponse($rrvMasterData, trans('custom.rrv_master_retrieved_successfully'));
    }

    public function printRecurringVoucher(Request $request)
    {
        $id = $request->get('recurringVoucherAutoId');
        $lang = $request->get('lang', 'en'); // Added to capture language

        $rrvMasterData = RecurringVoucherSetup::find($id);
        if (empty($rrvMasterData)) {
            return $this->sendError(trans('custom.rrv_master_not_found'));
        }

        $rrvMasterDataLine = RecurringVoucherSetup::where('recurringVoucherAutoId', $id)->with(['created_by', 'confirmed_by', 'modified_by', 'transactioncurrency', 'company', 'detail' => function ($query) {
            $query->with('project','segment');
        }, 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 119);
        }])->first();

        if (empty($rrvMasterDataLine)) {
            return $this->sendError(trans('custom.rrv_master_not_found'));
        }

        $refernaceDoc = \Helper::getCompanyDocRefNo($rrvMasterDataLine->companySystemID, $rrvMasterDataLine->documentSystemID);

        $companyId = $rrvMasterDataLine->companySystemID;
        $isProject_base = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
            ->where('companySystemID', $companyId)
            ->where('isYesNO', 1)
            ->exists();

        $transDecimal = 2;

        if ($rrvMasterDataLine->transactioncurrency) {
            $transDecimal = $rrvMasterDataLine->transactioncurrency->DecimalPlaces;
        }

        $debitTotal = RecurringVoucherSetupDetail::where('recurringVoucherAutoId', $id)->sum('debitAmount');

        $creditTotal = RecurringVoucherSetupDetail::where('recurringVoucherAutoId', $id)->sum('creditAmount');

        $order = array(
            'masterdata' => $rrvMasterDataLine,
            'docRef' => $refernaceDoc,
            'transDecimal' => $transDecimal,
            'debitTotal' => $debitTotal,
            'isProject_base' => $isProject_base,
            'creditTotal' => $creditTotal,
            'lang' => $lang // Pass lang to view
        );

        $time = strtotime("now");
        $fileName = 'recurring_voucher_' . $id . '_' . $time . '.pdf';
        
        $isRTL = ($lang === 'ar'); // Check if Arabic language for RTL support

        $mpdfConfig = [
            'tempDir' => public_path('tmp'),
            'mode' => 'utf-8',
            'format' => 'A4-P',
            'setAutoTopMargin' => 'stretch',
            'autoMarginPadding' => -10
        ];

        if ($isRTL) {
            $mpdfConfig['direction'] = 'rtl'; // Set RTL direction for mPDF
        }

        $html = view('print.recurring_voucher', $order);
        $mpdf = new \Mpdf\Mpdf($mpdfConfig);
        $mpdf->AddPage('P');
        $mpdf->setAutoBottomMargin = 'stretch';

        try {
            $mpdf->WriteHTML($html);
            return $mpdf->Output($fileName, 'I');
        } catch (\Exception $e) {
            \Log::error('mPDF Error in printRecurringVoucher: ' . $e->getMessage());
            return $this->sendError(trans('custom.pdf_generation_failed') . $e->getMessage());
        }
    }

    public function getRecurringVoucherMasterApproval(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

        $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyID)->where('documentSystemID', 119)->first();

        $grvMasters = DB::table('erp_documentapproved')->select(
            'employeesdepartments.approvalDeligated',
            'recurring_voucher_setup.recurringVoucherAutoId',
            'recurring_voucher_setup.RRVcode',
            'recurring_voucher_setup.documentSystemID',
            'recurring_voucher_setup.narration',
            'recurring_voucher_setup.createdDateTime',
            'recurring_voucher_setup.startDate',
            'recurring_voucher_setup.endDate',
            'recurring_voucher_setup.confirmedDate',
            'recurring_voucher_setup.documentType',
            'rrvDetailRec.debitSum',
            'rrvDetailRec.creditSum',
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
            $query->where('employeesdepartments.documentSystemID', 119)
                ->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
        })
            ->join('recurring_voucher_setup', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'recurringVoucherAutoId')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('recurring_voucher_setup.companySystemID', $companyID)
                ->where('recurring_voucher_setup.approved', 0)
                ->where('recurring_voucher_setup.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('currencymaster', 'recurring_voucher_setup.currencyID', 'currencymaster.currencyID')
            ->leftJoin(DB::raw('(SELECT COALESCE(SUM(debitAmount),0) as debitSum,COALESCE(SUM(creditAmount),0) as creditSum,recurringVoucherAutoId FROM recurring_voucher_setup_detail GROUP BY recurringVoucherAutoId) as rrvDetailRec'), 'rrvDetailRec.recurringVoucherAutoId', '=', 'recurring_voucher_setup.recurringVoucherAutoId')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 119)
            ->where('erp_documentapproved.companySystemID', $companyID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMasters = $grvMasters->where(function ($query) use ($search) {
                $query->where('RRVcode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $grvMasters = [];
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
            ->make(true);
    }

    public function getApprovedRecurringVoucherForCurrentUser(Request $request)
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
            'recurring_voucher_setup.recurringVoucherAutoId',
            'recurring_voucher_setup.RRVcode',
            'recurring_voucher_setup.documentSystemID',
            'recurring_voucher_setup.narration',
            'recurring_voucher_setup.createdDateTime',
            'recurring_voucher_setup.startDate',
            'recurring_voucher_setup.endDate',
            'recurring_voucher_setup.confirmedDate',
            'recurring_voucher_setup.documentType',
            'rrvDetailRec.debitSum',
            'rrvDetailRec.creditSum',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.DecimalPlaces As DecimalPlaces',
            'currencymaster.CurrencyCode As CurrencyCode',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        )->join('recurring_voucher_setup', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'recurringVoucherAutoId')
                ->where('recurring_voucher_setup.companySystemID', $companyID)
                ->where('recurring_voucher_setup.approved', -1)
                ->where('recurring_voucher_setup.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('currencymaster', 'recurring_voucher_setup.currencyID', 'currencymaster.currencyID')
            ->leftJoin(DB::raw('(SELECT COALESCE(SUM(debitAmount),0) as debitSum,COALESCE(SUM(creditAmount),0) as creditSum,recurringVoucherAutoId FROM recurring_voucher_setup_detail GROUP BY recurringVoucherAutoId) as rrvDetailRec'), 'rrvDetailRec.recurringVoucherAutoId', '=', 'recurring_voucher_setup.recurringVoucherAutoId')
            ->where('erp_documentapproved.documentSystemID', 119)
            ->where('erp_documentapproved.companySystemID', $companyID)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMasters = $grvMasters->where(function ($query) use ($search) {
                $query->where('RRVcode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
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
            ->make(true);
    }

    public function approveRecurringVoucher(Request $request)
    {
        $input = $request->all();
        $recurringVoucher = RecurringVoucherSetup::find($input['recurringVoucherAutoId']);
        $endDate = Carbon::parse($input['endDate']);
        $financeYear = CompanyFinanceYear::where('companyFinanceYearID',$recurringVoucher->companyFinanceYearID)
            ->where('isDeleted',0)
            ->exists();

        if($financeYear){
            $approve = \Helper::approveDocument($input);

            if (!$approve["success"]) {
                return $this->sendError($approve["message"]);
            } else {
                return $this->sendResponse(array(), $approve["message"]);
            }
        }
        else{
            return $this->sendError(trans('custom.financial_period_year_not_created', ['year' => $endDate->year]));
        }
    }

    public function rejectRecurringVoucher(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }
    }

    public function amendRecurringVoucherReview(Request $request)
    {
        $input = $request->all();

        $id = $input['rrvMasterAutoId'];

        $employee = \Helper::getEmployeeInfo();
        $emails = array();

        $rrvMaster = RecurringVoucherSetup::find($id);

        if (empty($rrvMaster)) {
            return $this->sendError(trans('custom.recurring_voucher_not_found_1'));
        }

        if(count($rrvMaster->schedules()->where('isInProccess',1)->get()) > 0)
            return $this->sendError(trans('custom.schedule_on_process_recurring_voucher'));

        if ($rrvMaster->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_return_back_to_amend_this_recurring_vou'));
        }

        $rrvSetupScheduleStates = RecurringVoucherSetupSchedule::where('recurringVoucherAutoId',$rrvMaster->recurringVoucherAutoId)->where('rrvGeneratedYN',1)->exists();
        if($rrvSetupScheduleStates){
            return $this->sendError(trans('custom.you_cannot_return_back_to_amend_this_recurring_vou_1'));
        }

        $emailBody = __('email.recurring_voucher_returned_to_amend_body', [
            'rrvCode' => $rrvMaster->RRVcode,
            'empName' => $employee->empName,
            'returnComment' => $input['returnComment']
        ]);
        $emailSubject = __('email.recurring_voucher_returned_to_amend', [
            'rrvCode' => $rrvMaster->RRVcode
        ]);

        DB::beginTransaction();
        try {

            //sending email to relevant party
            if ($rrvMaster->confirmedYN == 1) {
                $emails[] = array('empSystemID' => $rrvMaster->confirmedByEmpSystemID,
                    'companySystemID' => $rrvMaster->companySystemID,
                    'docSystemID' => $rrvMaster->documentSystemID,
                    'docSystemCode' => $rrvMaster->recurringVoucherAutoId,
                    'alertMessage' => $emailSubject,
                    'emailAlertMessage' => $emailBody);
            }

            $documentApproval = DocumentApproved::where('companySystemID', $rrvMaster->companySystemID)
                ->where('documentSystemCode', $id)
                ->where('documentSystemID', $rrvMaster->documentSystemID)
                ->get();

            foreach ($documentApproval as $da) {
                if ($da->approvedYN == -1) {
                    $emails[] = array('empSystemID' => $da->employeeSystemID,
                        'companySystemID' => $rrvMaster->companySystemID,
                        'docSystemID' => $rrvMaster->documentSystemID,
                        'docSystemCode' => $rrvMaster->recurringVoucherAutoId,
                        'alertMessage' => $emailSubject,
                        'emailAlertMessage' => $emailBody);
                }
            }

            $sendEmail = \Email::sendEmail($emails);
            if (!$sendEmail["success"]) {
                return $this->sendError($sendEmail["message"], 500);
            }

            //deleting from approval table
            DocumentApproved::where('documentSystemCode', $id)
                ->where('companySystemID', $rrvMaster->companySystemID)
                ->where('documentSystemID', $rrvMaster->documentSystemID)
                ->delete();

            //deleting from general ledger table
            GeneralLedger::where('documentSystemCode', $id)
                ->where('companySystemID', $rrvMaster->companySystemID)
                ->where('documentSystemID', $rrvMaster->documentSystemID)
                ->delete();

            BudgetConsumedData::where('documentSystemCode', $id)
                ->where('companySystemID', $rrvMaster->companySystemID)
                ->where('documentSystemID', $rrvMaster->documentSystemID)
                ->delete();

            RecurringVoucherSetupSchedule::where('recurringVoucherAutoId',$rrvMaster->recurringVoucherAutoId)->delete();

            // updating fields
            $rrvMaster->confirmedYN = 0;
            $rrvMaster->confirmedByEmpSystemID = null;
            $rrvMaster->confirmedByEmpID = null;
            $rrvMaster->confirmedByName = null;
            $rrvMaster->confirmedDate = null;
            $rrvMaster->RollLevForApp_curr = 1;

            $rrvMaster->approved = 0;
            $rrvMaster->approvedByUserSystemID = null;
            $rrvMaster->approvedByUserID = null;
            $rrvMaster->approvedDate = null;
            $rrvMaster->postedDate = null;

            $rrvMaster->refferedBackYN = 0;
            $rrvMaster->save();

            AuditTrial::createAuditTrial($rrvMaster->documentSystemID,$id,$input['returnComment'],trans('custom.returned_back_to_amend'));

            DB::commit();
            return $this->sendResponse($rrvMaster->toArray(), trans('custom.recurring_voucher_amend_saved_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function recurringVoucherReopen(Request $request)
    {
        $input = $request->all();

        $rrvMasterAutoId = $input['recurringVoucherAutoId'];

        $rrvMasterData = RecurringVoucherSetup::find($rrvMasterAutoId);
        $emails = array();
        if (empty($rrvMasterData)) {
            return $this->sendError(trans('custom.recurring_voucher_not_found'));
        }

        if ($rrvMasterData->RollLevForApp_curr > 1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_recurring_voucher_it_is_alr_1'));
        }

        if ($rrvMasterData->approved == -1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_recurring_voucher_it_is_alr'));
        }

        if ($rrvMasterData->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_recurring_voucher_it_is_not'));
        }

        // updating fields
        $rrvMasterData->confirmedYN = 0;
        $rrvMasterData->confirmedByEmpSystemID = null;
        $rrvMasterData->confirmedByEmpID = null;
        $rrvMasterData->confirmedByName = null;
        $rrvMasterData->confirmedDate = null;
        $rrvMasterData->RollLevForApp_curr = 1;
        $rrvMasterData->save();

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $rrvMasterData->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $rrvMasterData->bookingInvCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $rrvMasterData->bookingInvCode;

        $subject = trans('email.is_reopened_subject', ['attribute' => $cancelDocNameSubject]);

        $body = trans('email.is_reopened_body', [
            'attribute' => $cancelDocNameBody,
            'empID' => $employee->empID,
            'empName' => $employee->empFullName,
            'reopenComments' => $input['reopenComments']
        ]);

        $documentApproval = DocumentApproved::where('companySystemID', $rrvMasterData->companySystemID)
            ->where('documentSystemCode', $rrvMasterData->bookingSuppMasInvAutoID)
            ->where('documentSystemID', $rrvMasterData->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $rrvMasterData->companySystemID)
                    ->where('documentSystemID', $rrvMasterData->documentSystemID)
                    ->first();

                if (empty($companyDocument)) {
                    return ['success' => false, 'message' => trans('custom.policy_not_found_for_document')];
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

        DocumentApproved::where('documentSystemCode', $rrvMasterAutoId)
            ->where('companySystemID', $rrvMasterData->companySystemID)
            ->where('documentSystemID', $rrvMasterData->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($rrvMasterData->documentSystemID,$rrvMasterAutoId,$input['reopenComments'],'Reopened');

        return $this->sendResponse($rrvMasterData->toArray(), trans('custom.rrv_reopened_successfully'));
    }
}
