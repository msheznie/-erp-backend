<?php
/**
 * =============================================
 * -- File Name : BankAccountAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Bank Account
 * -- Author : Mohamed Fayas
 * -- Create date : 17 - September 2018
 * -- Description : This file contains the all CRUD for  Bank Account
 * -- REVISION HISTORY
 * -- Date: 17-September 2018 By: Fayas Description: Added new functions named as getAllBankAccountByCompany()
 * -- Date: 20-December 2018 By: Fayas Description: Added new functions named as getAccountsByBank(),getBankAccpountrFormData()
 * -- Date: 21-December 2018 By: Fayas Description: Added new functions named as getBankAccountApprovalByUser(),
 *                                   getBankAccountApprovedByUser(),bankAccountAudit(),bankAccountReopen(),bankAccountReferBack
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankAccountAPIRequest;
use App\Http\Requests\API\UpdateBankAccountAPIRequest;
use App\Models\BankAccount;
use App\Models\BankAccountRefferedBack;
use App\Models\BankAssign;
use App\Models\BankLedger;
use App\Models\BankMaster;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CurrencyMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\BankAccountRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BankAccountController
 * @package App\Http\Controllers\API
 */
class BankAccountAPIController extends AppBaseController
{
    /** @var  BankAccountRepository */
    private $bankAccountRepository;

    public function __construct(BankAccountRepository $bankAccountRepo)
    {
        $this->bankAccountRepository = $bankAccountRepo;
    }

    /**
     * Display a listing of the BankAccount.
     * GET|HEAD /bankAccounts
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bankAccountRepository->pushCriteria(new RequestCriteria($request));
        $this->bankAccountRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankAccounts = $this->bankAccountRepository->all();

        return $this->sendResponse($bankAccounts->toArray(), 'Bank Accounts retrieved successfully');
    }

    /**
     * Store a newly created BankAccount in storage.
     * POST /bankAccounts
     *
     * @param CreateBankAccountAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateBankAccountAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        $input['createdPCID'] = gethostname();
        $input['createdEmpID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $validator = \Validator::make($input, [
            'companySystemID' => 'required',
            'bankmasterAutoID' => 'required',
            'bankAssignedAutoID' => 'required',
            'bankBranch' => 'required',
            'AccountNo' => 'required',
            'accountCurrencyID' => 'required',
            'chartOfAccountSystemID' => 'required'
            //'BranchEmail' => 'email'
        ]);


        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $input['documentSystemID'] = 66;
        $input['documentID'] = 'BA';

        $company = Company::where('companySystemID', $input['companySystemID'])->with(['localcurrency', 'reportingcurrency'])->first();
        if (empty($company)) {
            return $this->sendError('Company not found', 500);
        }
        $input['companyID'] = $company->CompanyID;

        $bank = BankAssign::where('companySystemID', $input['companySystemID'])
            ->where('bankAssignedAutoID', $input['bankAssignedAutoID'])
            ->first();

        if (empty($bank)) {
            return $this->sendError('Bank not found', 500);
        }

        $input['bankShortCode'] = $bank->bankShortCode;
        $input['bankName'] = $bank->bankName;

        $checkDuplicateAccountNo = BankAccount::where('bankAssignedAutoID', $input['bankAssignedAutoID'])
            ->where('companySystemID', $input['companySystemID'])
            ->where('AccountNo', $input['AccountNo'])
            ->first();
        if (!empty($checkDuplicateAccountNo)) {
            return $this->sendError('Account No ' . $checkDuplicateAccountNo->AccountNo . ' already exists', 500);
        }

        if (isset($input['isDefault']) && $input['isDefault']) {
            $checkDefaultAccount = BankAccount::where('bankAssignedAutoID', $input['bankAssignedAutoID'])
                ->where('accountCurrencyID', $input['accountCurrencyID'])
                ->where('isDefault', 1)
                ->first();

            if (!empty($checkDefaultAccount)) {
                return $this->sendError('You cannot make this account as default account. This bank already has a default account(' . $checkDefaultAccount->AccountNo . ') for selected currency.', 500);
            }
        }

        if (isset($input['accountIBAN'])) {
            $input['accountIBAN#'] = $input['accountIBAN'];
        }

        if (isset($input['chartOfAccountSystemID']) && $input['chartOfAccountSystemID']) {
            $chartOfAccount = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])
                ->where('companySystemID', $input['companySystemID'])
                ->where('isAssigned', -1)
                ->first();

            if (empty($chartOfAccount)) {
                return $this->sendError('GL Code not found');
            }

            if ($chartOfAccount->isActive == 0) {
                return $this->sendError('Please select a active GL Code', 500);
            }

            if ($input['isTempBank'] != 1) {
                $checkAlreadyAssignGl = BankAccount::where('companySystemID', $input['companySystemID'])
                    ->where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])
                    ->first();
                if (!empty($checkAlreadyAssignGl)) {
                    return $this->sendError('Selected chart of account code is already linked in ' . $checkAlreadyAssignGl->AccountNo . '.', 500);
                }
            }

            $input['glCodeLinked'] = $chartOfAccount->AccountCode;
        }

        $bankAccounts = $this->bankAccountRepository->create($input);

        return $this->sendResponse($bankAccounts->toArray(), 'Bank Account saved successfully');
    }

    /**
     * Display the specified BankAccount.
     * GET|HEAD /bankAccounts/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var BankAccount $bankAccount */
        $bankAccount = $this->bankAccountRepository->with(['currency', 'confirmed_by', 'chart_of_account'])->findWithoutFail($id);

        if (empty($bankAccount)) {
            return $this->sendError('Bank Account not found');
        }

        $bankAccount->amounts = $this->getBankAccountBalanceSummery($bankAccount);
        $bankAccount->accountIBAN = $bankAccount['accountIBAN#'];

        return $this->sendResponse($bankAccount->toArray(), 'Bank Account retrieved successfully');
    }

    /**
     * Update the specified BankAccount in storage.
     * PUT/PATCH /bankAccounts/{id}
     *
     * @param  int $id
     * @param UpdateBankAccountAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBankAccountAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmedByName', 'chart_of_account', 'amounts',
            'confirmedByEmpID', 'confirmedDate', 'confirmed_by', 'confirmedByEmpSystemID', 'currency']);
        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        $input['modifiedPCID'] = gethostname();
        $input['modifiedByEmpID'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;
        $input['modifedDateTime'] = now();

        /** @var BankAccount $bankAccount */
        $bankAccount = $this->bankAccountRepository->findWithoutFail($id);

        if (empty($bankAccount)) {
            return $this->sendError('Bank Account not found');
        }

        if ($bankAccount->confirmedYN == 1) {
            return $this->sendError('This document already confirmed.', 500);
        }

        if ($bankAccount->approvedYN == 1) {
            return $this->sendError('This document already approved.', 500);
        }

        $checkDuplicateAccountNo = BankAccount::where('bankAccountAutoID', '!=', $id)
            ->where('bankAssignedAutoID', $input['bankAssignedAutoID'])
            ->where('companySystemID', $input['companySystemID'])
            ->where('AccountNo', $input['AccountNo'])
            ->first();

        if (!empty($checkDuplicateAccountNo)) {
            return $this->sendError('Account No ' . $checkDuplicateAccountNo->AccountNo . ' already exists', 500);
        }

        if (isset($input['isDefault']) && $input['isDefault']) {
            $checkDefaultAccount = BankAccount::where('bankAccountAutoID', '!=', $id)
                ->where('bankAssignedAutoID', $input['bankAssignedAutoID'])
                ->where('accountCurrencyID', $input['accountCurrencyID'])
                ->where('isDefault', 1)
                ->first();

            if (!empty($checkDefaultAccount)) {
                return $this->sendError('You cannot make this account as default account. This bank already has a default account(' . $checkDefaultAccount->AccountNo . ') for selected currency.', 500);
            }
        }

        if (isset($input['accountIBAN'])) {
            $input['accountIBAN#'] = $input['accountIBAN'];
        }

        if (isset($input['chartOfAccountSystemID']) && $input['chartOfAccountSystemID']) {
            $chartOfAccount = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])
                ->where('companySystemID', $input['companySystemID'])
                ->where('isAssigned', -1)
                ->first();

            if (empty($chartOfAccount)) {
                return $this->sendError('GL Code not found');
            }

            if ($chartOfAccount->isActive == 0) {
                return $this->sendError('Please select a active GL Code', 500);
            }

            if ($input['isTempBank'] != 1) {
                $checkAlreadyAssignGl = BankAccount::where('bankAccountAutoID', '!=', $id)
                    ->where('companySystemID', $input['companySystemID'])
                    ->where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])
                    ->first();
                if (!empty($checkAlreadyAssignGl)) {
                    return $this->sendError('Selected chart of account code is already linked in ' . $checkAlreadyAssignGl->AccountNo . '.', 500);
                }

                $input['glCodeLinked'] = $chartOfAccount->AccountCode;
            } 

            if ($bankAccount->isTempBank != $input['isTempBank'] && $input['isTempBank'] != 1) {
                $input['chartOfAccountSystemID'] = null;
                $input['glCodeLinked'] = null;
            }
        }

        if ($bankAccount->confirmedYN == 0 && $input['confirmedYN'] == 1) {
            $validator = \Validator::make($input, [
                'companySystemID' => 'required',
                'bankmasterAutoID' => 'required',
                'bankAssignedAutoID' => 'required',
                'bankBranch' => 'required',
                'AccountNo' => 'required',
                'accountCurrencyID' => 'required|numeric|min:1',
                'chartOfAccountSystemID' => 'required|numeric|min:1'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            if ($input['isAccountActive'] == 0) {
                return $this->sendError('Bank Account should be activated before confirm.', 500);
            }

            $params = array('autoID' => $id,
                'company' => $bankAccount->companySystemID,
                'document' => $bankAccount->documentSystemID,
                'segment' => 0,
                'category' => 0,
                'amount' => 0
            );

            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }

        $bankAccount = $this->bankAccountRepository->update($input, $id);

        return $this->sendResponse($bankAccount->toArray(), 'BankAccount updated successfully');
    }

    /**
     * Remove the specified BankAccount from storage.
     * DELETE /bankAccounts/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var BankAccount $bankAccount */
        $bankAccount = $this->bankAccountRepository->findWithoutFail($id);

        if (empty($bankAccount)) {
            return $this->sendError('Bank Account not found');
        }

        $bankAccount->delete();

        return $this->sendResponse($id, 'Bank Account deleted successfully');
    }


    public function getAllBankAccountByCompany(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('bankmasterAutoID', 'isAccountActive'));

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

        $logistics = BankAccount::whereIn('companySystemID', $subCompanies)
            ->when(request('bankmasterAutoID',false), function ($q) use ($input) {
                $q->where('bankmasterAutoID', $input['bankmasterAutoID']);
            })
            ->with(['currency']);

        if (array_key_exists('isAccountActive', $input)) {
            if (($input['isAccountActive'] == 0 || $input['isAccountActive'] == 1) && !is_null($input['isAccountActive'])) {
                $logistics->where('isAccountActive', $input['isAccountActive']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $logistics = $logistics->where(function ($query) use ($search) {
                $query->where('bankShortCode', 'LIKE', "%{$search}%")
                    ->orWhere('bankName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($logistics)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bankAccountAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->addColumn('amounts', function ($row) {
                return $this->getBankAccountBalanceSummery($row);
            })
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getAccountsByBank(Request $request)
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

        $bankAccounts = BankAccount::whereIn('companySystemID', $subCompanies)
            //->where('isAccountActive',1)
            ->where('bankAssignedAutoID', $input['id'])
            ->with(['currency']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankAccounts = $bankAccounts->where(function ($query) use ($search) {
                $query->where('AccountNo', 'LIKE', "%{$search}%")
                    ->orWhere('bankBranch', 'LIKE', "%{$search}%")
                    ->orWhere('glCodeLinked', 'LIKE', "%{$search}%")
                    ->orWhere('accountSwiftCode', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($bankAccounts)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bankAccountAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    function getBankAccountBalanceSummery($row)
    {
        $bankBalance = BankLedger::where('companySystemID', $row->companySystemID)
            ->where('bankID', $row->bankmasterAutoID)
            ->where('bankAccountID', $row->bankAccountAutoID)
            ->where('bankClearedYN', -1)
            ->sum('bankClearedAmount');

        /* $withTreasury = BankLedger::where('companySystemID',$row->companySystemID)
                                     ->where('bankID',$row->bankmasterAutoID)
                                     ->where('bankAccountID',$row->bankAccountAutoID)
                                     ->where('bankClearedYN',0)
                                     ->where('trsClearedYN',-1)
                                     ->sum('trsClearedAmount');*/

        $receiptsTotal = BankLedger::where('companySystemID', $row->companySystemID)
            ->where('bankID', $row->bankmasterAutoID)
            ->where('bankAccountID', $row->bankAccountAutoID)
            ->where('payAmountBank', '<', 0)
            ->where('bankClearedYN', 0)
            ->where('trsClearedYN', -1)
            ->sum('trsClearedAmount');

        $paymentsTotal = BankLedger::where('companySystemID', $row->companySystemID)
            ->where('bankID', $row->bankmasterAutoID)
            ->where('bankAccountID', $row->bankAccountAutoID)
            ->where('payAmountBank', '>', 0)
            ->where('bankClearedYN', 0)
            ->where('trsClearedYN', -1)
            ->sum('trsClearedAmount');
        $withTreasury = ($receiptsTotal * -1) - $paymentsTotal;

        $totalBankBalance = ($bankBalance * -1);
        $array = array('bankBalance' => $totalBankBalance,
            'withTreasury' => $withTreasury,
            'netBankBalance' => ($totalBankBalance + $withTreasury),
            'receiptsTotal' => $receiptsTotal,
            'paymentsTotal' => $paymentsTotal,
        );
        return $array;
    }

    public function getBankAccountFormData(Request $request)
    {
        $companyId = $request['companyId'];
        $currencies = CurrencyMaster::all();
        $company = Company::where('companySystemID', $companyId)
            ->with(['localcurrency', 'reportingcurrency'])->first();

        $yesNoSelection = YesNoSelection::all();
        $yesNoSelectionMin = YesNoSelectionForMinus::all();


        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionMin' => $yesNoSelectionMin,
            'currencies' => $currencies,
            'company' => $company
        );
        return $this->sendResponse($output, 'Record retrieved successfully');

    }

    public function getBankAccountApprovedByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'wareHouseFrom', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $bankAccount = DB::table('erp_documentapproved')
            ->select(
                'erp_bankaccount.*',
                'currencymaster.CurrencyCode As CurrencyCode',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_bankaccount', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'bankAccountAutoID')
                    ->where('erp_bankaccount.companySystemID', $companyId)
                    ->where('erp_bankaccount.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('currencymaster', 'accountCurrencyID', 'currencymaster.currencyID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [66])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankAccount = $bankAccount->where(function ($query) use ($search) {
                $query->where('AccountNo', 'LIKE', "%{$search}%")
                    ->orWhere('bankBranch', 'LIKE', "%{$search}%")
                    ->orWhere('glCodeLinked', 'LIKE', "%{$search}%")
                    ->orWhere('accountSwiftCode', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($bankAccount)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bankAccountAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * get Bank Account Approval By User
     * POST /getBankAccountApprovalByUser
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getBankAccountApprovalByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approvedYN'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $bankAccount = DB::table('erp_documentapproved')
            ->select(
                'erp_bankaccount.*',
                'currencymaster.CurrencyCode As CurrencyCode',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
                $query->whereIn('employeesdepartments.documentSystemID', [66])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_bankaccount', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'bankAccountAutoID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_bankaccount.companySystemID', $companyId)
                    ->where('erp_bankaccount.approvedYN', 0)
                    ->where('erp_bankaccount.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('currencymaster', 'accountCurrencyID', 'currencymaster.currencyID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [66])
            ->where('erp_documentapproved.companySystemID', $companyId);


        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankAccount = $bankAccount->where(function ($query) use ($search) {
                $query->where('AccountNo', 'LIKE', "%{$search}%")
                    ->orWhere('bankBranch', 'LIKE', "%{$search}%")
                    ->orWhere('glCodeLinked', 'LIKE', "%{$search}%")
                    ->orWhere('accountSwiftCode', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $bankAccount = [];
        }

        return \DataTables::of($bankAccount)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bankAccountAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function bankAccountAudit(Request $request)
    {
        $id = $request->get('id');
        $bankAccount = $this->bankAccountRepository->getAudit($id);

        if (empty($bankAccount)) {
            return $this->sendError('Bank Account not found');
        }

        $bankAccount->docRefNo = \Helper::getCompanyDocRefNo($bankAccount->companySystemID, $bankAccount->documentSystemID);

        return $this->sendResponse($bankAccount->toArray(), 'Bank Account retrieved successfully');
    }

    public function bankAccountReopen(Request $request)
    {
        $input = $request->all();

        $id = $input['bankAccountAutoID'];
        $bankAccount = $this->bankAccountRepository->findWithoutFail($id);
        $emails = array();
        if (empty($bankAccount)) {
            return $this->sendError('Bank Account not found');
        }

        if ($bankAccount->approvedYN == -1) {
            return $this->sendError('You cannot reopen this Bank Account it is already fully approved');
        }

        if ($bankAccount->RollLevForApp_curr > 1) {
            return $this->sendError('You cannot reopen this Bank Account it is already partially approved');
        }

        if ($bankAccount->confirmedYN == 0) {
            return $this->sendError('You cannot reopen this Bank Account, it is not confirmed');
        }

        $updateInput = ['confirmedYN' => 0, 'confirmedByEmpSystemID' => null, 'confirmedByEmpID' => null,
            'confirmedByName' => null, 'confirmedDate' => null, 'RollLevForApp_curr' => 1];

        $this->bankAccountRepository->update($updateInput, $id);

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $bankAccount->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $bankAccount->AccountNo . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $bankAccount->AccountNo;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $bankAccount->companySystemID)
            ->where('documentSystemCode', $bankAccount->bankAccountAutoID)
            ->where('documentSystemID', $bankAccount->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $bankAccount->companySystemID)
                    ->where('documentSystemID', $bankAccount->documentSystemID)
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
            ->where('companySystemID', $bankAccount->companySystemID)
            ->where('documentSystemID', $bankAccount->documentSystemID)
            ->delete();

        return $this->sendResponse($bankAccount->toArray(), 'Bank Account reopened successfully');
    }

    public function bankAccountReferBack(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];

        $bankAccount = $this->bankAccountRepository->find($id);
        if (empty($bankAccount)) {
            return $this->sendError('Bank Account not found');
        }

        if ($bankAccount->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this bank account');
        }

        $bankAccountArray = $bankAccount->toArray();

        $storeSRHistory = BankAccountRefferedBack::insert($bankAccountArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $bankAccount->companySystemID)
            ->where('documentSystemID', $bankAccount->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $bankAccount->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentRefereedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $bankAccount->companySystemID)
            ->where('documentSystemID', $bankAccount->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $updateArray = ['refferedBackYN' => 0, 'confirmedYN' => 0, 'confirmedByEmpSystemID' => null,
                'confirmedByEmpID' => null, 'confirmedByName' => null, 'confirmedDate' => null, 'RollLevForApp_curr' => 1];

            $this->bankAccountRepository->update($updateArray, $id);
        }

        return $this->sendResponse($bankAccount->toArray(), 'Bank Account Amend successfully');
    }


}
