<?php
/**
 * =============================================
 * -- File Name : BankReconciliationAPIController.php
 * -- Project Name : ERP
 * -- Module Name : Bank Reconciliation
 * -- Author : Mohamed Fayas
 * -- Create date : 18 - September 2018
 * -- Description : This file contains the all CRUD for Bank Reconciliation
 * -- REVISION HISTORY
 * -- Date: 18-September 2018 By: Fayas Description: Added new functions named as getAllBankReconciliationByBankAccount()
 * -- Date: 26-September 2018 By: Fayas Description: Added new functions named as getBankReconciliationFormData()
 * -- Date: 27-September 2018 By: Fayas Description: Added new functions named as getBankReconciliationApprovalByUser(),getBankReconciliationApprovedByUser()
 * -- Date: 28-September 2018 By: Fayas Description: Added new functions named as bankReconciliationAudit()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankReconciliationAPIRequest;
use App\Http\Requests\API\UpdateBankReconciliationAPIRequest;
use App\Models\BankAccount;
use App\Models\BankLedger;
use App\Models\BankReconciliation;
use App\Models\Company;
use App\Models\YesNoSelection;
use App\Repositories\BankLedgerRepository;
use App\Repositories\BankReconciliationRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BankReconciliationController
 * @package App\Http\Controllers\API
 */
class BankReconciliationAPIController extends AppBaseController
{
    /** @var  BankReconciliationRepository */
    private $bankReconciliationRepository;
    private $bankLedgerRepository;

    public function __construct(BankReconciliationRepository $bankReconciliationRepo,BankLedgerRepository $bankLedgerRepo)
    {
        $this->bankReconciliationRepository = $bankReconciliationRepo;
        $this->bankLedgerRepository = $bankLedgerRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bankReconciliations",
     *      summary="Get a listing of the BankReconciliations.",
     *      tags={"BankReconciliation"},
     *      description="Get all BankReconciliations",
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
     *                  @SWG\Items(ref="#/definitions/BankReconciliation")
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
        $this->bankReconciliationRepository->pushCriteria(new RequestCriteria($request));
        $this->bankReconciliationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankReconciliations = $this->bankReconciliationRepository->all();

        return $this->sendResponse($bankReconciliations->toArray(), 'Bank Reconciliations retrieved successfully');
    }

    /**
     * @param CreateBankReconciliationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bankReconciliations",
     *      summary="Store a newly created BankReconciliation in storage",
     *      tags={"BankReconciliation"},
     *      description="Store BankReconciliation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BankReconciliation that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BankReconciliation")
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
     *                  ref="#/definitions/BankReconciliation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBankReconciliationAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();
        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $validator = \Validator::make($input, [
            'description' => 'required',
            'bankRecAsOf' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }


        $input['bankRecAsOf'] = new Carbon($input['bankRecAsOf']);


         $end = (new Carbon())->endOfMonth();
        if($input['bankRecAsOf'] > $end){
            return $this->sendError('You cannot select a date greater than the current month last day', 500);
        }

        $input['documentSystemID'] = 62;
        $input['documentID'] = 'BRC';

        $bankAccount = BankAccount::find($input['bankAccountAutoID']);

        if (!empty($bankAccount)) {
            $input['bankGLAutoID'] = $bankAccount->chartOfAccountSystemID;
            $input['companySystemID'] = $bankAccount->companySystemID;
        } else {
            return $this->sendError('bank Account not found.!', 500);
        }


        $checkPending = BankReconciliation::where('bankAccountAutoID', $input['bankAccountAutoID'])
            ->where('approvedYN', 0)
            ->first();


        if (!empty($checkPending)) {
             return $this->sendError("There is a bank reconciliation (" . $checkPending->bankRecPrimaryCode . ") pending for approval for the bank reconciliation you are trying to add. Please check again.", 500);
        }

        $maxAsOfDate = BankReconciliation::where('bankAccountAutoID', $input['bankAccountAutoID'])
            ->max('bankRecAsOf');

        if ($maxAsOfDate >= $input['bankRecAsOf']) {
            return $this->sendError('You cannot create bank reconciliation, Please select the as of date after ' . (new Carbon($maxAsOfDate))->format('d/m/Y'), 500);
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $lastSerial = BankReconciliation::where('companySystemID', $input['companySystemID'])
            ->orderBy('bankRecAutoID', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }
        $input['serialNo'] = $lastSerialNumber;

        $dateArray = explode('-', $input['bankRecAsOf']);
        $input['month'] = $dateArray[1];
        $input['year'] = $dateArray[0];

        $code = ($input['companyID'] . '\\' . $input['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
        $input['bankRecPrimaryCode'] = $code;


        $openingBalance = BankLedger::selectRaw('companySystemID,bankAccountID,trsClearedYN,bankClearedYN,ABS(SUM(if(bankClearedAmount < 0,bankClearedAmount,0))) - SUM(if(bankClearedAmount > 0,bankClearedAmount,0)) as opening')
                                    ->where('companySystemID', $input['companySystemID'])
                                    ->where("bankAccountID", $input['bankAccountAutoID'])
                                    ->where("trsClearedYN", -1)
                                    ->where("bankClearedYN", -1)
                                    ->groupBy('companySystemID', 'bankAccountID')
                                    ->first();
        $input['openingBalance'] = $openingBalance->opening;
        $input['closingBalance'] = $openingBalance->opening;
        $bankReconciliations = $this->bankReconciliationRepository->create($input);

        return $this->sendResponse($bankReconciliations->toArray(), 'Bank Reconciliation saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bankReconciliations/{id}",
     *      summary="Display the specified BankReconciliation",
     *      tags={"BankReconciliation"},
     *      description="Get BankReconciliation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankReconciliation",
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
     *                  ref="#/definitions/BankReconciliation"
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
        /** @var BankReconciliation $bankReconciliation */
        $bankReconciliation = $this->bankReconciliationRepository->with(['bank_account.currency', 'confirmed_by'])->findWithoutFail($id);

        if (empty($bankReconciliation)) {
            return $this->sendError('Bank Reconciliation not found');
        }

        $totalReceiptAmount = BankLedger::where('companySystemID', $bankReconciliation->companySystemID)
            ->where('payAmountBank', '<', 0)
            ->where("bankAccountID", $bankReconciliation->bankAccountAutoID)
            ->where("trsClearedYN", -1)
            ->where(function ($q) use ($bankReconciliation) {
                $q->where(function ($q1) use ($bankReconciliation) {
                    $q1->where('bankRecAutoID', $bankReconciliation->bankRecAutoID)
                        ->where("bankClearedYN", -1);
                })->orWhere("bankClearedYN", 0);
            })->sum('payAmountBank');

        $totalPaymentAmount = BankLedger::where('companySystemID', $bankReconciliation->companySystemID)
            ->where('payAmountBank', '>', 0)
            ->where("bankAccountID", $bankReconciliation->bankAccountAutoID)
            ->where("trsClearedYN", -1)
            ->where(function ($q) use ($bankReconciliation) {
                $q->where(function ($q1) use ($bankReconciliation) {
                    $q1->where('bankRecAutoID', $bankReconciliation->bankRecAutoID)
                        ->where("bankClearedYN", -1);
                })->orWhere("bankClearedYN", 0);
            })->sum('payAmountBank');

        $totalReceiptClearedAmount = BankLedger::where('companySystemID', $bankReconciliation->companySystemID)
            ->where('payAmountBank', '<', 0)
            ->where("bankAccountID", $bankReconciliation->bankAccountAutoID)
            ->where("trsClearedYN", -1)
            ->where(function ($q) use ($bankReconciliation) {
                $q->where(function ($q1) use ($bankReconciliation) {
                    $q1->where('bankRecAutoID', $bankReconciliation->bankRecAutoID)
                        ->where("bankClearedYN", -1);
                });
            })->sum('bankClearedAmount');

        $totalPaymentClearedAmount = BankLedger::where('companySystemID', $bankReconciliation->companySystemID)
            ->where('payAmountBank', '>', 0)
            ->where("bankAccountID", $bankReconciliation->bankAccountAutoID)
            ->where("trsClearedYN", -1)
            ->where(function ($q) use ($bankReconciliation) {
                $q->where(function ($q1) use ($bankReconciliation) {
                    $q1->where('bankRecAutoID', $bankReconciliation->bankRecAutoID)
                        ->where("bankClearedYN", -1);
                });
            })->sum('bankClearedAmount');

        $bankReconciliation->totalReceiptAmount = $totalReceiptAmount * -1;
        $bankReconciliation->totalReceiptClearedAmount = $totalReceiptClearedAmount * -1;
        $bankReconciliation->totalPaymentAmount = $totalPaymentAmount;
        $bankReconciliation->totalPaymentClearedAmount = $totalPaymentClearedAmount;

        return $this->sendResponse($bankReconciliation->toArray(), 'Bank Reconciliation retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateBankReconciliationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bankReconciliations/{id}",
     *      summary="Update the specified BankReconciliation in storage",
     *      tags={"BankReconciliation"},
     *      description="Update BankReconciliation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankReconciliation",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BankReconciliation that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BankReconciliation")
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
     *                  ref="#/definitions/BankReconciliation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBankReconciliationAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmedByName', 'confirmedByEmpID', 'confirmedDate',
            'confirmed_by', 'confirmedByEmpSystemID']);
        /** @var BankReconciliation $bankReconciliation */
        $bankReconciliation = $this->bankReconciliationRepository->findWithoutFail($id);

        if (empty($bankReconciliation)) {
            return $this->sendError('Bank Reconciliation not found');
        }
        if ($bankReconciliation->confirmedYN == 1) {
            return $this->sendError('This document already confirmed.', 500);
        }

        if ($bankReconciliation->confirmedYN == 0 && $input['confirmedYN'] == 1) {


            $checkItems = BankLedger::where('bankRecAutoID', $id)
                ->count();
            if ($checkItems == 0) {
                return $this->sendError('Every bank reconciliation should have at least one cleared item', 500);
            }

            $input['RollLevForApp_curr'] = 1;
            $params = array('autoID' => $id,
                'company' => $bankReconciliation->companySystemID,
                'document' => $bankReconciliation->documentSystemID,
                'segment' => 0,
                'category' => 0,
                'amount' => 0
            );

            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }

        //$bankReconciliation = $this->bankReconciliationRepository->update($input, $id);

        return $this->sendResponse($bankReconciliation->toArray(), 'BankReconciliation updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bankReconciliations/{id}",
     *      summary="Remove the specified BankReconciliation from storage",
     *      tags={"BankReconciliation"},
     *      description="Delete BankReconciliation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankReconciliation",
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
        /** @var BankReconciliation $bankReconciliation */
        $bankReconciliation = $this->bankReconciliationRepository->findWithoutFail($id);

        if (empty($bankReconciliation)) {
            return $this->sendError('Bank Reconciliation not found');
        }

        $bankLedgerData = BankLedger::where('bankAccountID',$bankReconciliation->bankAccountAutoID)
                                    ->where('companySystemID',$bankReconciliation->companySystemID)
                                    ->where('bankRecAutoID',$bankReconciliation->bankRecAutoID)
                                    ->where('bankClearedYN',-1)
                                    ->get();

        foreach ($bankLedgerData as $data){
            $updateArray = ['bankClearedYN' => 0,'bankClearedAmount' => 0,'bankClearedByEmpName' => null,
            'bankClearedByEmpID' => null,'bankClearedByEmpSystemID' => null,'bankClearedDate' => null,'bankRecAutoID' => null];

            $bankLedger = $this->bankLedgerRepository->update($updateArray, $data['bankLedgerAutoID']);
        }

        $bankReconciliation->delete();

        return $this->sendResponse($id, 'Bank Reconciliation deleted successfully');
    }

    public function getAllBankReconciliationByBankAccount(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month', 'year'));

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

        $bankReconciliation = BankReconciliation::whereIn('companySystemID', $subCompanies)
            ->where("bankAccountAutoID", $input['bankAccountAutoID'])
            ->with(['month', 'created_by', 'bank_account']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankReconciliation = $bankReconciliation->where(function ($query) use ($search) {
                $query->where('bankRecPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($bankReconciliation)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bankRecAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getCheckBeforeCreate(Request $request)
    {
        $input = $request->all();
        $bankAccount = BankAccount::find($input['bankAccountAutoID']);

        if (empty($bankAccount)) {
            return $this->sendError('Bank Account not found');
        }

        $checkPending = BankReconciliation::where('bankAccountAutoID', $input['bankAccountAutoID'])
            ->where('approvedYN', 0)
            ->first();


        if (!empty($checkPending)) {
            return $this->sendError("There is a bank reconciliation (" . $checkPending->bankRecPrimaryCode . ") pending for approval for the bank reconciliation you are trying to add. Please check again.", 500);
        }

        return $this->sendResponse($bankAccount->toArray(), 'successfully');
    }

    public function getBankReconciliationFormData(Request $request)
    {
        $companyId = $request['companyId'];
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        $output = array(
            'yesNoSelection' => $yesNoSelection,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getBankReconciliationApprovalByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array());

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $bankReconciliation = DB::table('erp_documentapproved')
            ->select(
                'erp_bankrecmaster.*',
                'employees.empName As created_emp',
                'erp_bankaccount.AccountNo As AccountNo',
                'months.monthDes As monthDes',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $query->whereIn('employeesdepartments.documentSystemID', [62])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID);
            })
            ->join('erp_bankrecmaster', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'bankRecAutoID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_bankrecmaster.companySystemID', $companyId)
                    ->where('erp_bankrecmaster.approvedYN', 0)
                    ->where('erp_bankrecmaster.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('months', 'month', 'months.monthID')
            ->leftJoin('erp_bankaccount', 'erp_bankrecmaster.bankAccountAutoID', 'erp_bankaccount.bankAccountAutoID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [62])
            ->where('erp_documentapproved.companySystemID', $companyId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankReconciliation = $bankReconciliation->where(function ($query) use ($search) {
                $query->where('bankRecPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($bankReconciliation)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bankRecAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getBankReconciliationApprovedByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array());

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $bankReconciliation = DB::table('erp_documentapproved')
            ->select(
                'erp_bankrecmaster.*',
                'employees.empName As created_emp',
                'erp_bankaccount.AccountNo As AccountNo',
                'months.monthDes As monthDes',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_bankrecmaster', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'bankRecAutoID')
                    ->where('erp_bankrecmaster.companySystemID', $companyId)
                    ->where('erp_bankrecmaster.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('months', 'month', 'months.monthID')
            ->leftJoin('erp_bankaccount', 'erp_bankrecmaster.bankAccountAutoID', 'erp_bankaccount.bankAccountAutoID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [62])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankReconciliation = $bankReconciliation->where(function ($query) use ($search) {
                $query->where('bankRecPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($bankReconciliation)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bankRecAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function bankReconciliationAudit(Request $request)
    {

        $id = $request->id;
        /** @var BankReconciliation $bankReconciliation */
        $bankReconciliation = $this->bankReconciliationRepository->with(['bank_account.currency', 'confirmed_by','company','month'])->findWithoutFail($id);

        if (empty($bankReconciliation)) {
            return $this->sendError('Bank Reconciliation not found');
        }

        return $this->sendResponse($bankReconciliation->toArray(), 'BankReconciliation updated successfully');
    }


}
