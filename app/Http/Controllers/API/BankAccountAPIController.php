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
use App\Models\CustomerInvoice;
use App\Models\CustomerReceivePayment;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\PaySupplierInvoiceMaster;
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

        return $this->sendResponse($bankAccounts->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_accounts')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company')]), 500);
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
            return $this->sendError(trans('custom.account_no') .' '. $checkDuplicateAccountNo->AccountNo .' '. trans('custom.already_exists'), 500);
        }

        if (isset($input['isDefault']) && $input['isDefault']) {
            $checkDefaultAccount = BankAccount::where('bankAssignedAutoID', $input['bankAssignedAutoID'])
                ->where('accountCurrencyID', $input['accountCurrencyID'])
                ->where('isDefault', 1)
                ->first();

            if (!empty($checkDefaultAccount)) {
                return $this->sendError(trans('custom.you_cannot_make_this_account_as_default_account_this_bank_already_has_a_default_account') . $checkDefaultAccount->AccountNo . trans('custom.for_selected_currency'), 500);
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
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.gl_code')]));
            }

            if ($chartOfAccount->isActive == 0) {
                return $this->sendError(trans('custom.please_select_a_active_gl_code'), 500);
            }

            if ($input['isTempBank'] != 1) {
                $checkAlreadyAssignGl = BankAccount::where('companySystemID', $input['companySystemID'])
                    ->where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])
                    ->where('isAccountActive', 1)
                    ->first();

                if($input['isAccountActive'] == 1){
                    if (!empty($checkAlreadyAssignGl)) {
                        return $this->sendError(trans('custom.selected_chart_of_account_code_is_already_linked_in') .' '. $checkAlreadyAssignGl->AccountNo . '.', 500);
                    }
                }
            }

            $input['glCodeLinked'] = $chartOfAccount->AccountCode;
        }

        $bankAccounts = $this->bankAccountRepository->create($input);

        return $this->sendResponse($bankAccounts->toArray(), trans('custom.save', ['attribute' => trans('custom.bank_accounts')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_accounts')]));
        }

        $bankAccount->amounts = $this->getBankAccountBalanceSummery($bankAccount);
        $bankAccount->accountIBAN = $bankAccount['accountIBAN#'];

        return $this->sendResponse($bankAccount->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_accounts')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_accounts')]));
        }

        $supplierInvoice = PaySupplierInvoiceMaster::where('BPVAccount', $id)->where('cancelYN', 0)->where('BPVbank', $bankAccount->bankmasterAutoID)->first();
        $custReceivePay = CustomerReceivePayment::where('bankAccount', $id)->where('cancelYN', 0)->where('bankID', $bankAccount->bankmasterAutoID)->first();
        $custInvoice = CustomerInvoice::where('bankAccountID', $id)->where('canceledYN', 0)->where('bankID', $bankAccount->bankmasterAutoID)->first();

        if($supplierInvoice || $custReceivePay|| $custInvoice){
            if($input['isManualActive'] == $bankAccount->isManualActive ){
                return $this->sendError(trans('custom.bank_account_in_transactions'),500);
            }
        }

        $checkDuplicateAccountNo = BankAccount::where('bankAccountAutoID', '!=', $id)
            ->where('bankAssignedAutoID', $input['bankAssignedAutoID'])
            ->where('companySystemID', $input['companySystemID'])
            ->where('AccountNo', $input['AccountNo'])
            ->first();

        if (!empty($checkDuplicateAccountNo)) {
            return $this->sendError(trans('custom.account_no') .' '. $checkDuplicateAccountNo->AccountNo .' '. trans('custom.already_exists'), 500);
        }

        if (isset($input['isDefault']) && $input['isDefault']) {
            $checkDefaultAccount = BankAccount::where('bankAccountAutoID', '!=', $id)
                ->where('bankAssignedAutoID', $input['bankAssignedAutoID'])
                ->where('accountCurrencyID', $input['accountCurrencyID'])
                ->where('isDefault', 1)
                ->first();

            if (!empty($checkDefaultAccount)) {
                return $this->sendError(trans('custom.you_cannot_make_this_account_as_default_account_this_bank_already_has_a_default_account') .' '. $checkDefaultAccount->AccountNo .' '. trans('custom.for_selected_currency'), 500);
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
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.gl_code')]));
            }

            if ($chartOfAccount->isActive == 0) {
                return $this->sendError(trans('custom.please_select_a_active_gl_code'), 500);
            }

            if (($bankAccount->isTempBank == $input['isTempBank']) && $input['isTempBank'] != 1) {
                $checkAlreadyAssignGl = BankAccount::where('bankAccountAutoID', '!=', $id)
                    ->where('companySystemID', $input['companySystemID'])
                    ->where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])
                    ->where('isAccountActive', 1)
                    ->first();
                if($input['isAccountActive'] == 1){
                    if (!empty($checkAlreadyAssignGl)) {
                        return $this->sendError(trans('custom.selected_chart_of_account_code_is_already_linked_in') .' '. $checkAlreadyAssignGl->AccountNo . '.', 500);
                    }
                }
            } 
            
            $input['glCodeLinked'] = $chartOfAccount->AccountCode;

            if ($bankAccount->isTempBank != $input['isTempBank']) {
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
                return $this->sendError(trans('custom.bank_account_should_be_activated_before_confirm'), 500);
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

        return $this->sendResponse($bankAccount->toArray(), trans('custom.update', ['attribute' => trans('custom.bank_accounts')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_accounts')]));
        }

        $bankAccount->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.bank_accounts')]));
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

        $bankmasterAutoID = $request['bankmasterAutoID'];
        $bankmasterAutoID = (array)$bankmasterAutoID;
        $bankmasterAutoID = collect($bankmasterAutoID)->pluck('id');

        $search = $request->input('search.value');

        $logistics = $this->bankAccountRepository->bankAccountListQuery($request, $input, $search, $bankmasterAutoID);

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

    public function getAllBankAccounts(Request $request)
    {
        
        $bankAccounts = $this->allBankAccountQry($request);
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
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

    public function allBankAccountQry($request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month', 'year'));


        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $bankAccounts = BankAccount::whereIn('companySystemID', $subCompanies)
                                   ->with(['currency', 'bank', 'company']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankAccounts = $bankAccounts->where(function ($query) use ($search) {
                $query->where('AccountNo', 'LIKE', "%{$search}%")
                    ->orWhere('bankBranch', 'LIKE', "%{$search}%")
                    ->orWhere('glCodeLinked', 'LIKE', "%{$search}%")
                    ->orWhere('accountSwiftCode', 'LIKE', "%{$search}%")
                    ->orWhereHas('bank', function ($query) use ($search) {
                        $query->where('bankShortCode', 'LIKE', "%{$search}%")
                              ->orWhere('bankName', 'LIKE', "%{$search}%");
                    })->orWhereHas('company', function ($query) use ($search) {
                        $query->where('CompanyID', 'LIKE', "%{$search}%");
                    });
            });
        }

        return $bankAccounts;
    }

     public function exportBankAccountMaster(Request $request)
    {
        $bankAccounts = $this->allBankAccountQry($request)->get();
        $data = array();
        $x = 0;
        foreach ($bankAccounts as $val) {
            $x++;
            $data[$x]['Company ID'] = (isset($val->company->CompanyID)) ? $val->company->CompanyID : "";
            $data[$x]['Bank Short Code'] = (isset($val->bank->bankShortCode)) ? $val->bank->bankShortCode : "";
            $data[$x]['Bank Name'] =  (isset($val->bank->bankName)) ? $val->bank->bankName : "";
            $data[$x]['Account No'] = $val->AccountNo;
            $data[$x]['Currency'] = (isset($val->currency->CurrencyCode)) ? $val->currency->CurrencyCode : "";
            $data[$x]['GL Code'] = $val->glCodeLinked;
            $data[$x]['Branch'] = $val->bankBranch;
            $data[$x]['Swift'] = $val->accountSwiftCode;
            $data[$x]['Is Temporary Acc'] = ($val->isTempBank == 1) ? "Yes" : "No";
            $data[$x]['Is Active'] = ($val->isAccountActive == 1) ? "Yes" : "No";
            $data[$x]['Is Default'] = ($val->isDefault == 1) ? "Yes" : "No";

            $status = "";
            if ($val->CancelledYN == -1) {
                $status = "Cancelled";
            } else if ($val->confirmedYN == 0 && $val->approvedYN == 0) {
                $status = " Not Confirmed";
            }
            else if ($val->confirmedYN == 1 && $val->approvedYN == 0 && $val->refferedBackYN == 0) {
                $status = "Pending Approval";
            } else if ($val->confirmedYN == 1 && $val->approvedYN == 0 && $val->refferedBackYN == -1) {
                $status = "Referred Back";
            }
            else if ($val->confirmedYN == 1 && ($val->approvedYN == -1 || $val->approvedYN == 1 )) {
                $status = "Fully Approved";
            }

            $data[$x]['Status'] = $status;
        }

         \Excel::create('bank_accounts', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download('csv');

        return $this->sendResponse([], trans('custom.supplier_masters_export_to_csv_successfully'));
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


    public function getBankBalance(Request $request)
    {

        $input =  (object)$request->all();
        
        $currency = $this->convertArrayToSelectedValue($request->all(), array('bank_currency','document_currency'));
        if(!empty($currency) && isset($currency['bank_currency'])){
            $bank_currency = $currency['bank_currency'];
        }else{
            return $this->sendError('Please select a Currency', 500);
        }
        
        $document_currency = $currency['document_currency'];
        $bankBalance = $this->getBankAccountBalanceSummery($input);

        $amount = $bankBalance['netBankBalance'];
        $currencies = CurrencyMaster::where('currencyID','=',$bank_currency)->first();

        $data['amount'] = $amount;
        $data['decimal'] = $currencies;
        
        return $this->sendResponse($data, trans('custom.retrieve', ['attribute' => trans('custom.record')]));

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
        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));

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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_accounts')]));
        }

        $bankAccount->docRefNo = \Helper::getCompanyDocRefNo($bankAccount->companySystemID, $bankAccount->documentSystemID);

        return $this->sendResponse($bankAccount->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_accounts')]));
    }

    public function bankAccountReopen(Request $request)
    {
        $input = $request->all();

        $id = $input['bankAccountAutoID'];
        $bankAccount = $this->bankAccountRepository->findWithoutFail($id);
        $emails = array();
        if (empty($bankAccount)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_accounts')]));
        }

        if ($bankAccount->approvedYN == -1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_bank_account_it_is_already_fully_approved'));
        }

        if ($bankAccount->RollLevForApp_curr > 1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_bank_account_it_is_already_partially_approved'));
        }

        if ($bankAccount->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_bank_account_it_is_not_confirmed'));
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
                    return ['success' => false, 'message' => trans('custom.policy_not_found_for_this_document')];
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

        return $this->sendResponse($bankAccount->toArray(), trans('custom.reopened', ['attribute' => trans('custom.bank_accounts')]));
    }

    public function bankAccountReferBack(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];

        $bankAccount = $this->bankAccountRepository->find($id);
        if (empty($bankAccount)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_accounts')]));
        }

        if ($bankAccount->refferedBackYN != -1) {
            return $this->sendError(trans('custom.you_cannot_refer_back_this_bank_account'));
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

        return $this->sendResponse($bankAccount->toArray(), trans('custom.bank_account_amend_successfully'));
    }

    public function getBankAccountsByBankID(Request $request)
    {
        $input = $request->all();

        $selectedCompanyId = $input['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $bankAccounts = BankAccount::whereIn('companySystemID', $subCompanies)
                                   ->where('bankmasterAutoID', $input['id'])
                                   ->get();

        return $this->sendResponse($bankAccounts, trans('custom.retrieve', ['attribute' => trans('custom.bank_accounts')]));
    }
}
