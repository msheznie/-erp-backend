<?php

/**
 * =============================================
 * -- File Name : ChartOfAccountAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Chart Of Account
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Chart Of Account.
 * -- REVISION HISTORY
 * -- Date: 06-June 2018 By: Mubashir Description: Modified getChartOfAccount() to handle filters from local storage
 * -- Date: 18-December 2018 By: Fayas Description:  Added new function chartOfAccountReferBack()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateChartOfAccountAPIRequest;
use App\Http\Requests\API\UpdateChartOfAccountAPIRequest;
use App\Models\AllocationMaster;
use App\helper\ReopenDocument;
use App\Models\BankAccount;
use App\Models\CashFlowTemplateLink;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\ChartOfAccountsRefferedBack;
use App\Models\Company;
use App\Models\ControlAccount;
use App\Models\AccountsType;
use App\Models\DocumentApproved;
use App\Models\DocumentReferedHistory;
use App\Models\ReportTemplateLinks;
use App\Models\YesNoSelection;
use App\Models\ReportTemplateDetails;
use App\Models\GeneralLedger;
use App\Repositories\ChartOfAccountRepository;
use App\Traits\UserActivityLogger;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use Illuminate\Validation\Rule;
use App\helper\Helper;
use App\helper\DocumentCodeGenerate;
use Carbon\Carbon;
use App\helper\CreateExcel;
use App\Services\AuditLog\ChartOfAccountAuditService;
use App\Traits\AuditLogsTrait;

/**
 * Class ChartOfAccountController
 * @package App\Http\Controllers\API
 */
class ChartOfAccountAPIController extends AppBaseController
{
    /** @var  ChartOfAccountRepository */
    private $chartOfAccountRepository;
    private $userRepository;
    use AuditLogsTrait;

    public function __construct(ChartOfAccountRepository $chartOfAccountRepo, UserRepository $userRepo)
    {
        $this->chartOfAccountRepository = $chartOfAccountRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the ChartOfAccount.
     * GET|HEAD /chartOfAccounts
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->chartOfAccountRepository->pushCriteria(new RequestCriteria($request));
        $this->chartOfAccountRepository->pushCriteria(new LimitOffsetCriteria($request));
        $chartOfAccounts = $this->chartOfAccountRepository->all();

        return $this->sendResponse($chartOfAccounts->toArray(), 'Chart Of Accounts retrieved successfully');
    }


    /**
     * Store a newly created ChartOfAccount in storage.
     * POST /chartOfAccounts
     *
     * @param CreateChartOfAccountAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateChartOfAccountAPIRequest $request)
    {

        $input = $request->all();
        $input = array_except($input, ['final_approved_by']);

        /** Validation massage : Common for Add & Update */
        $accountCode = isset($input['AccountCode']) ? $input['AccountCode'] : '';

        if (!isset($input['reportTemplateCategory'])) {
            return $this->sendError("Report template category cannot be empty", 500);
        }

        if($input['AccountDescription'] == null){
            return $this->sendError(trans('custom.account_description_cannot_be_empty'),500);
        }

        $messages = array(
            'AccountCode.unique' => 'Account code ' . $accountCode . ' already exists'
        );


        if (array_key_exists('catogaryBLorPLID', $input)) {
            $categoryBLorPL = AccountsType::where('accountsType', $input['catogaryBLorPLID'])->first();
            if ($categoryBLorPL) {
                $input['catogaryBLorPL'] = $categoryBLorPL->code;
            }
        }


        if (array_key_exists('controlAccountsSystemID', $input)) {
            $controlAccount = ControlAccount::where('controlAccountsSystemID', $input['controlAccountsSystemID'])->first();
            if ($controlAccount) {
                $input['controlAccounts'] = $controlAccount->controlAccountsID;
            }
        }

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['empID'];
        $employee = \Helper::getEmployeeInfo();
        $input['documentSystemID'] = 59;
        $input['documentID'] = 'CAM';

        $validatorResult = \Helper::checkCompanyForMasters($input['primaryCompanySystemID']);
        if (!$validatorResult['success']) {
            return $this->sendError($validatorResult['message']);
        }

        $company = Company::where('companySystemID', $input['primaryCompanySystemID'])->first();

        if ($company) {
            $input['primaryCompanyID'] = $company->CompanyID;
        }

        if (isset($input['interCompanySystemID'])) {
            $interCompanySystemID = Company::where('companySystemID', $input['interCompanySystemID'])->first();

            if ($interCompanySystemID) {
                $input['interCompanyID'] = $interCompanySystemID->CompanyID;
            }
        }

        if ((isset($input['relatedPartyYN']) && !$input['relatedPartyYN']) || !isset($input['relatedPartyYN'])) {
            $input['interCompanyID'] = null;
            $input['interCompanySystemID'] = null;
        }

        $isMasterAc = (isset($input['isMasterAccount']) && ($input['isMasterAccount'] == TRUE || $input['isMasterAccount'] == 1)) ? 1 : 0;

        if (isset($input['isMasterAccount']) && $input['isMasterAccount']) {
            $input['masterAccount'] = $accountCode;
        }


        DB::beginTransaction();
        try {
            if (array_key_exists('chartOfAccountSystemID', $input)) {


                $chartOfAccount = ChartOfAccount::where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])->first();

                if (empty($chartOfAccount)) {
                    return $this->sendError('Chart of Account not found!', 404);
                }

                $input = $this->convertArrayToValue($input);

                if ($chartOfAccount->reportTemplateCategory != $input['reportTemplateCategory']) {
                    $availability = FALSE;
                    while (!$availability) {
                        $accountCode = DocumentCodeGenerate::generateAccountCode($input['reportTemplateCategory'])['data'];
                        $chartOfAccountSystemID = isset($input['chartOfAccountSystemID']) ? $input['chartOfAccountSystemID'] : null;
                        $availability = ChartOfAccount::checkAccountCode($accountCode, $chartOfAccountSystemID);
                        if ($availability) {
                            DocumentCodeGenerate::updateChartOfAccountSerailNumber($input['reportTemplateCategory']);
                        } else {
                            break;
                        }
                    }


                    DocumentCodeGenerate::updateChartOfAccountSerailNumber($input['reportTemplateCategory']);
                    
                    $input['AccountCode'] = $accountCode;
                } else {
                    $input['AccountCode'] = $chartOfAccount->AccountCode;
                }


                $isActiveAc = ($input['isActive'] == TRUE || $input['isActive'] == 1) ? 1 : 0;

                $checkSubLedgerAccount = ChartOfAccount::where('chartOfAccountSystemID', '!=', $input['chartOfAccountSystemID'])
                    ->where('masterAccount', $chartOfAccount->AccountCode)
                    ->first();

                if ($checkSubLedgerAccount) {
                    if (($isMasterAc != $chartOfAccount->isMasterAccount) && $isMasterAc == 0) {
                        return $this->sendError('This account is already assigned to sub ledger accounts, therefore cannot change master account as sub ledger account');
                    }

                    if ($chartOfAccount->isMasterAccount && !$isActiveAc) {
                        $checkForDeactiveAccounts = ChartOfAccount::where('chartOfAccountSystemID', '!=', $input['chartOfAccountSystemID'])
                            ->where('masterAccount', $chartOfAccount->AccountCode)
                            ->where('isActive', 1)
                            ->first();

                        if ($checkForDeactiveAccounts) {
                            return $this->sendError('This account is already assigned to sub ledger accounts, therefore cannot deactivate this account');
                        }
                    }

                    if ($input['catogaryBLorPLID'] != $chartOfAccount->catogaryBLorPLID) {
                        return $this->sendError('This account is already assigned to sub ledger accounts, therefore cannot change the category');
                    }

                    if ($input['controlAccountsSystemID'] != $chartOfAccount->controlAccountsSystemID) {
                        return $this->sendError('This account is already assigned to sub ledger accounts, therefore cannot change the control account');
                    }
                }

                $previosDataValue = $chartOfAccount->toArray();
                $newDataValue = $input;

                $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid'] : 'local';
                $db = isset($input['db']) ? $input['db'] : '';

                if(isset($input['tenant_uuid']) ){
                    unset($input['tenant_uuid']);
                }

                if(isset($input['db']) ){
                    unset($input['db']);
                }

                if ($chartOfAccount->isApproved == 1) {

                    //check policy 8
                    $policy = Helper::checkRestrictionByPolicy($input['primaryCompanySystemID'], 8);

                    if ($policy) {

                        $updateData = [
                            'AccountDescription' => $input['AccountDescription'],
                            'isBank' => $input['isBank'],
                        ];
                        $updateDataNotAssigned = [
                            'isActive' => $input['isActive']
                        ];

                        ChartOfAccount::where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])->update($updateDataNotAssigned);

                        $updateChartOfAccount = ChartOfAccount::where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])->first();
                        if ($updateChartOfAccount) {
                            $updateChartOfAccount->AccountDescription =  $input['AccountDescription'];
                            $updateChartOfAccount->save();
                            DB::commit();

                            $chartOfAccountOld = $chartOfAccount->toArray();
                            ChartOfAccountsAssigned::where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])->update($updateData);

                            ReportTemplateLinks::where('glAutoID', $input['chartOfAccountSystemID'])->update(['glDescription' => $input['AccountDescription']]);

                            CashFlowTemplateLink::where('glAutoID', $input['chartOfAccountSystemID'])->update(['glDescription' => $input['AccountDescription']]);

                            $old_array = array_only($chartOfAccountOld, ['AccountDescription']);
                            $modified_array = array_only($input, ['AccountDescription']);
                            // update in to user log table
                            foreach ($old_array as $key => $old) {
                                if ($old != $modified_array[$key]) {
                                    $description = $employee->empName . " Updated chart of account (" . $chartOfAccount->chartOfAccountSystemID . ") from " . $old . " To " . $modified_array[$key] . "";
                                    UserActivityLogger::createUserActivityLogArray($employee->employeeSystemID, $chartOfAccount->documentSystemID, $chartOfAccount->primaryCompanySystemID, $chartOfAccount->chartOfAccountSystemID, $description, $modified_array[$key], $old, $key);
                                }
                            }

                        }

                    }

                    //check policy 10
                    $policyCAc = Helper::checkRestrictionByPolicy($input['primaryCompanySystemID'], 10);
                    if ($policyCAc) {
                        $updateData = [
                            'controllAccountYN' => $input['controllAccountYN'],
                            'isBank' => $input['isBank'],
                        ];

                        $updateDataNotAssigned = [
                            'isActive' => $input['isActive']
                        ];

                        $updateChartOfAccount = ChartOfAccount::where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])->update($updateData);

                        ChartOfAccount::where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])->update($updateDataNotAssigned);
                        DB::commit();


                        if ($updateChartOfAccount) {
                            $chartOfAccountOld = $chartOfAccount->toArray();
                            ChartOfAccountsAssigned::where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])->update($updateData);
                            $old_array = array_only($chartOfAccountOld, ['controllAccountYN']);
                            $modified_array = array_only($input, ['controllAccountYN']);
                            // update in to user log table
                            foreach ($old_array as $key => $old) {
                                if ($old != $modified_array[$key]) {
                                    $description = $employee->empName . " Updated chart of account (" . $chartOfAccount->chartOfAccountSystemID . ") from " . $old . " To " . (($modified_array[$key]) ? 1 : 0) . "";
                                    UserActivityLogger::createUserActivityLogArray($employee->employeeSystemID, $chartOfAccount->documentSystemID, $chartOfAccount->primaryCompanySystemID, $chartOfAccount->chartOfAccountSystemID, $description, $modified_array[$key], $old, $key);
                                }
                            }
                        }

                    }


                    if ($policyCAc || $policy) {
                        $data = ChartOfAccountAuditService::validateFieldsByPolicy($newDataValue, $previosDataValue, $policy ,$policyCAc);
                        $previosValue = $data['allowedpreviousValues'];
                        $newValue = $data['allowednewValues'];
                        $this->auditLog($db, $input['chartOfAccountSystemID'],$uuid, "chartofaccounts", $previosDataValue['AccountCode']." has updated", "U", $newValue, $previosValue);
                        return $this->sendResponse([], 'Chart Of Account updated successfully done');
                    }

                    return $this->sendError('You cannot edit, This document already confirmed and approved.', 500);
                }

                // $input = array_except($input,['currency_master']); // uses only in sub sub tables


                /** Validation : Edit Unique */
                $validator = \Validator::make($input, [
                    'AccountCode' => Rule::unique('chartofaccounts')->ignore($input['chartOfAccountSystemID'], 'chartOfAccountSystemID')
                ], $messages);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                /** End of Validation */


                $input['modifiedPc'] = gethostname();
                $input['modifiedUser'] = $empId;
                $input = array_except($input, ['confirmedEmpSystemID', 'confirmedEmpID', 'confirmedEmpName', 'confirmedEmpDate']);

                if ($input['confirmedYN'] == 1 && $chartOfAccount->confirmedYN == 0) {

                    $primaryCompanySystemID = isset($input['primaryCompanySystemID'][0]) ? $input['primaryCompanySystemID'][0] : $input['primaryCompanySystemID'];

                    if ($input['relatedPartyYN'] == 1) {
                        $checkAlreadyInterCompanyCreated = ChartOfAccount::where('interCompanySystemID', $input['interCompanySystemID'])->where('primaryCompanySystemID',$primaryCompanySystemID)
                                                                         ->where('chartOfAccountSystemID', '!=', $input['chartOfAccountSystemID'])
                                                                         ->first();

                        if ($checkAlreadyInterCompanyCreated) {
                            return $this->sendError("Related party account is already created for this company.");
                        }                        
                    }

                    $params = array('autoID' => $input['chartOfAccountSystemID'], 'company' => $input["primaryCompanySystemID"], 'document' => $input["documentSystemID"]);
                    $confirm = \Helper::confirmDocument($params);
                    if (!$confirm["success"]) {
                        return $this->sendError($confirm["message"]);
                    }
                }
                $input = array_except($input, ['confirmedYN']);

               
                $this->chartOfAccountRepository->update($input, $input['chartOfAccountSystemID']);

                if ($isMasterAc != $chartOfAccount->isMasterAccount) {
                    $checkChartOfAcAssigned = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])
                        ->first();

                    if ($checkChartOfAcAssigned) {
                        ChartOfAccountsAssigned::where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])
                            ->update(['masterAccount' => $input['masterAccount']]);
                    }
                }
            } else {
                $availability = FALSE;
                while (!$availability) {
                    $accountCode = DocumentCodeGenerate::generateAccountCode($input['reportTemplateCategory'])['data'];
                    $chartOfAccountSystemID = isset($input['chartOfAccountSystemID']) ? $input['chartOfAccountSystemID'] : null;
                    $availability = ChartOfAccount::checkAccountCode($accountCode, $chartOfAccountSystemID);
                    if ($availability) {
                        DocumentCodeGenerate::updateChartOfAccountSerailNumber($input['reportTemplateCategory']);
                    } else {
                        break;
                    }
                }

                $input['AccountCode'] = $accountCode;

                /** Validation : Add Unique */
                $validator = \Validator::make($input, [
                    'AccountCode' => 'unique:chartofaccounts'
                ], $messages);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                /** End of Validation */


                $input['createdPcID'] = gethostname();
                $input['createdUserID'] = $empId;

    
                $chartOfAccount = $this->chartOfAccountRepository->create($input);

                DocumentCodeGenerate::updateChartOfAccountSerailNumber($input['reportTemplateCategory']);
            }

            DB::commit();
            return $this->sendReponseWithDetails($chartOfAccount->toArray(), 'Chart Of Account saved successfully',1,$confirm['data'] ?? null);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage() . " Line" . $exception->getLine(), 500);
        }


    }

    public function changeActive(Request $request){

        $chartOfAccountID = $request->chartOfAccountSystemID;
        $selectedCompanyId = $request->selectedCompanyId;

        $generalLedger = GeneralLedger::where('chartOfAccountSystemID', $chartOfAccountID)->where('companySystemID',$selectedCompanyId)->first();

        if($generalLedger){
            return $this->sendError(trans('custom.chart_of_account_has_a_balance_in_gl'),500);
        }


        return $this->sendResponse($generalLedger, 'General Ledger Have No Balance');

    }

    public function isBank($id){

        $isBank = BankAccount::where('chartOfAccountSystemID', $id)->get();

        $isGeneralLedger = GeneralLedger::where('chartOfAccountSystemID', $id)->get();

        return $this->sendResponse([$isBank,$isGeneralLedger], 'Data retrieved successfully');
    }

    /**
     * Display all assigned itemAssigned for specific Item Master.
     * GET|HEAD / assignedCompaniesByChartOfAccount }
     *
     * @param  $request
     *
     * @return Response
     */
    public function assignedCompaniesByChartOfAccount(Request $request)
    {
        $chartOfAccountSystemID = $request['chartOfAccountSystemID'];
        $selectedCompanyId = $request['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $data = ChartOfAccountsAssigned::where('chartOfAccountSystemID', '=', $chartOfAccountSystemID)
            ->whereIn("companySystemID", $subCompanies)
            ->first();


        if ($data) {
            $itemCompanies = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $chartOfAccountSystemID)
                ->with(['company'])
                ->whereIn("companySystemID", $subCompanies)
                ->get();
        } else {
            $itemCompanies = [];
        }

        return $this->sendResponse($itemCompanies, 'Companies retrieved successfully');
    }

    public function getNotAssignedCompaniesByChartOfAccount(Request $request)
    {
        $chartOfAccountSystemID = $request->get('chartOfAccountSystemID');
        $selectedCompanyId = $request['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }


        $companies = Company::whereIn("companySystemID", $subCompanies)
            ->whereDoesntHave('chartOfAccountAssigned', function ($query) use ($chartOfAccountSystemID) {
                $query->where('chartOfAccountSystemID', '=', $chartOfAccountSystemID);
            })->where('isGroup', 0)
            ->get(['companySystemID',
                'CompanyID',
                'CompanyName']);

        return $this->sendResponse($companies->toArray(), 'Companies retrieved successfully');
    }


    /**
     * Display the specified ChartOfAccount.
     * GET|HEAD /chartOfAccounts/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ChartOfAccount $chartOfAccount */
        $chartOfAccount = $this->chartOfAccountRepository->with(['finalApprovedBy'])->findWithoutFail($id);

        if (empty($chartOfAccount)) {
            return $this->sendError('Chart Of Account not found');
        }

        return $this->sendResponse($chartOfAccount->toArray(), 'Chart Of Account retrieved successfully');
    }

    /**
     * Update the specified ChartOfAccount in storage.
     * PUT/PATCH /chartOfAccounts/{id}
     *
     * @param int $id
     * @param UpdateChartOfAccountAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateChartOfAccountAPIRequest $request)
    {
        $input = $request->all();

        /** @var ChartOfAccount $chartOfAccount */
        $chartOfAccount = $this->chartOfAccountRepository->findWithoutFail($id);

        if (empty($chartOfAccount)) {
            return $this->sendError('Chart Of Account not found');
        }

        $chartOfAccount = $this->chartOfAccountRepository->update($input, $id);

        return $this->sendResponse($chartOfAccount->toArray(), 'ChartOfAccount updated successfully');
    }

    /**
     * Remove the specified ChartOfAccount from storage.
     * DELETE /chartOfAccounts/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ChartOfAccount $chartOfAccount */
        $chartOfAccount = $this->chartOfAccountRepository->findWithoutFail($id);

        if (empty($chartOfAccount)) {
            return $this->sendError('Chart Of Account not found');
        }

        $chartOfAccount->delete();

        return $this->sendResponse($id, 'Chart Of Account deleted successfully');
    }

    public function getChartOfAccount(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('controlAccountsSystemID', 'isBank', 'catogaryBLorPLID'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }
        if ($request['type'] == 'all') {
            $chartOfAccount = ChartOfAccount::with(['controlAccount', 'accountType', 'allocation','templateCategoryDetails'=>
                function($query){
                    $query->with(['master']);
                }
            ]);
            // ->whereIn('primaryCompanySystemID',$childCompanies);
        } else {
            $chartOfAccount = ChartOfAccountsAssigned::with(['controlAccount', 'accountType', 'allocation'])
                ->whereIn('CompanySystemID', $childCompanies)
                ->where('isAssigned', -1)
                ->where('isActive', 1);
            if (isset($input['isAllocation']) && $input['isAllocation'] == 1) {
                $chartOfAccount = $chartOfAccount->where('AllocationID', '!=', null);
            }
        }


        if (array_key_exists('controlAccountsSystemID', $input)) {
            if ($input['controlAccountsSystemID'] && !is_null($input['controlAccountsSystemID'])) {
                $chartOfAccount->where('controlAccountsSystemID', $input['controlAccountsSystemID']);
            }
        }

        if (array_key_exists('isBank', $input)) {
            if (($input['isBank'] == 0 || $input['isBank'] == 1) && !is_null($input['isBank'])) {
                $chartOfAccount->where('isBank', $input['isBank']);
            }
        }

        if (array_key_exists('isMasterAccount', $input)) {
            if (($input['isMasterAccount'] == 0 || $input['isMasterAccount'] == 1) && !is_null($input['isMasterAccount'])) {
                $chartOfAccount->where('isMasterAccount', $input['isMasterAccount']);
            }
        }

        if (array_key_exists('catogaryBLorPLID', $input)) {
            if ($input['catogaryBLorPLID'] && !is_null($input['catogaryBLorPLID'])) {
                $chartOfAccount->where('catogaryBLorPLID', $input['catogaryBLorPLID']);
            }
        }

        $search = $request->input('search.value');
        if ($search) {
            $chartOfAccount = $chartOfAccount->where(function ($query) use ($search) {
                $query->where('AccountCode', 'LIKE', "%{$search}%")
                    ->orWhere('AccountDescription', 'LIKE', "%{$search}%");
            });
        }
        $request->request->remove('search.value');
        return \DataTables::eloquent($chartOfAccount)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('chartOfAccountSystemID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(TRUE);
    }

    public function getAllChartOfAccountApproval(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $companyId = $request->selectedCompanyID;

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companyID = \Helper::getGroupCompany($companyId);
        } else {
            $companyID = [$companyId];
        }
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');

        $chartOfAccount = DB::table('erp_documentapproved')->select('employeesdepartments.approvalDeligated','chartofaccounts.*', 'controlaccounts.description as controlaccountdescription', 'accountstype.description as accountstypedescription', 'erp_documentapproved.documentApprovedID', 'rollLevelOrder', 'approvalLevelID', 'documentSystemCode')->join('employeesdepartments', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID')
                ->where('employeesdepartments.documentSystemID', 59)
                ->whereIn('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
        })->join('chartofaccounts', function ($query) use ($companyID, $empID, $search) {
            $query->on('chartOfAccountSystemID', '=', 'erp_documentapproved.documentSystemCode')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->whereIn('primaryCompanySystemID', $companyID)
                ->where('isApproved', 0)
                ->where('chartofaccounts.confirmedYN', 1)
                ->when($search != "", function ($q) use ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('AccountCode', 'LIKE', "%{$search}%")
                            ->orWhere('AccountDescription', 'LIKE', "%{$search}%");
                    });
                });
        })
            ->leftJoin('controlaccounts', 'controlaccounts.controlAccountsSystemID', '=', 'chartofaccounts.controlAccountsSystemID')
            ->leftJoin('accountstype', 'catogaryBLorPLID', '=', 'accountsType')
            ->where('erp_documentapproved.approvedYN', 0)
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 59)
            ->whereIn('erp_documentapproved.companySystemID', $companyID);

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $chartOfAccount = [];
        }
        $data['order'] = [];
        $data['search']['value'] = '';
        $request->merge($data);


        return \DataTables::of($chartOfAccount)
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
            ->make(TRUE);
    }

    /**
     * get form data for Chart of Account.
     * POST /getChartOfAccountFormData
     *
     */
    public function getChartOfAccountFormData(Request $request)
    {
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** get All Control Accounts */
        $controlAccounts = ControlAccount::all();

        /** all Account Types */
        $accountsType = AccountsType::whereNotIn('accountsType', [3, 4])->get();

        /** all allocation Types */
        $allocationType = AllocationMaster::where('isActive', 1)->get();

        /** all Account Types */
        $chartOfAccount = ChartOfAccount::where('isMasterAccount', 1)->get(['AccountCode', 'AccountDescription']);
        //$chartOfAccount = ChartOfAccount::all('AccountCode', 'AccountDescription');

        $selectedCompanyId = $request['selectedCompanyId'];

        /** all Company  Drop Down */

        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            // $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
            $subCompanies = \Helper::getSubCompaniesByGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        /**  Companies by group  Drop Down */
        $allCompanies = Company::whereIn("companySystemID", $subCompanies)->where("isGroup", 0)->get();

        $output = array('controlAccounts' => $controlAccounts,
            'accountsType' => $accountsType,
            'yesNoSelection' => $yesNoSelection,
            'chartOfAccount' => $chartOfAccount,
            'allCompanies' => $allCompanies,
            'allocationType' => $allocationType,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getInterCompanies(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        if (isset($input['primaryCompanySystemID'])) {
            $allCompanies = Company::where('isGroup', 0)
                ->where('isActive', 1)
                // ->where('companySystemID', '!=', $input['primaryCompanySystemID'])
                ->get();
        } else {
            $allCompanies = [];
        }


        return $this->sendResponse($allCompanies, 'Record retrieved successfully');
    }

    public function getMasterChartOfAccountData(Request $request)
    {
        $input = $request->all();
        $masterAccounts = [];
        if ((isset($input['primaryCompanySystemID']) && $input['primaryCompanySystemID'] > 0) && (isset($input['catogaryBLorPLID']) && $input['catogaryBLorPLID'] > 0) && (isset($input['controlAccountsSystemID']) && $input['controlAccountsSystemID'] > 0)) {
            $masterAccounts = ChartOfAccount::where('isMasterAccount', 1)
                ->where('isApproved', 1)
                ->where('catogaryBLorPLID', $input['catogaryBLorPLID'])
                ->where('controlAccountsSystemID', $input['controlAccountsSystemID'])
                ->whereHas('chartofaccount_assigned', function ($query) use ($input) {
                    $query->where('companySystemID', $input['primaryCompanySystemID'])
                        ->where('isActive', 1)
                        ->where('isAssigned', -1);
                })
                ->get(['AccountCode', 'AccountDescription']);
        }

        return $this->sendResponse($masterAccounts, 'Record retrieved successfully');
    }


    public function approveChartOfAccount(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectChartOfAccount(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }

    }

    public function chartOfAccountReferBack(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];

        $chartOfAccount = $this->chartOfAccountRepository->find($id);
        if (empty($chartOfAccount)) {
            return $this->sendError('Chart Of Account not found');
        }

        if ($chartOfAccount->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this Chart Of Account');
        }

        $chartOfAccountArray = $chartOfAccount->toArray();

        $storeHistory = ChartOfAccountsRefferedBack::insert($chartOfAccountArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $chartOfAccount->primaryCompanySystemID)
            ->where('documentSystemID', $chartOfAccount->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $chartOfAccount->timesReferred;
            }
        }

        $documentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentRefereedHistory = DocumentReferedHistory::insert($documentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $chartOfAccount->primaryCompanySystemID)
            ->where('documentSystemID', $chartOfAccount->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $updateArray = ['refferedBackYN' => 0,
                'confirmedYN' => 0,
                'confirmedEmpSystemID' => null,
                'confirmedEmpID' => null,
                'confirmedEmpName' => null,
                'confirmedEmpDate' => null,
                'RollLevForApp_curr' => 1];

            $this->chartOfAccountRepository->update($updateArray, $id);
        }

        return $this->sendResponse($chartOfAccount->toArray(), 'Chart Of Account Amend successfully');
    }

    public function getChartOfAccounts(request $request)
    {
        $input = $request->all();
        //$companyID = $input['companyID'];

        $items = ChartOfAccount::where('isActive', 1)->where('isApproved', 1);

        if (isset($input['controllAccountYN'])) {
            $items = $items->where('controllAccountYN', $input['controllAccountYN']);
        }

        if (isset($input['isBank'])) {
            $items = $items->where('isBank', $input['isBank']);
        }

        if (isset($input['catogaryBLorPL'])) {
            if ($input['catogaryBLorPL']) {
                $items = $items->where('catogaryBLorPL', $input['catogaryBLorPL']);
            }
        }

        if (isset($input['templateMasterID'])) {
            $tempDetail = ReportTemplateLinks::ofTemplate($input['templateMasterID'])->pluck('glAutoID')->toArray();
            $items = $items->whereNotIn('chartOfAccountSystemID', array_filter($tempDetail));
        }

        $items = $items->get();
        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');

    }


    public function exportChartOfAccounts(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('controlAccountsSystemID', 'isBank', 'catogaryBLorPLID'));
        $type = $input['type'];
        $companyId = $input['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }
        if ($request['type'] == 'all') {
            $chartOfAccount = ChartOfAccount::with(['controlAccount', 'accountType', 'allocation','templateCategoryDetails'=>
                function($query){
                    $query->with(['master']);
                }]);
        } else {
            $chartOfAccount = ChartOfAccountsAssigned::with(['controlAccount', 'accountType', 'allocation'])
                ->whereIn('CompanySystemID', $childCompanies)
                ->where('isAssigned', -1)
                ->where('isActive', 1);
            if (isset($input['isAllocation']) && $input['isAllocation'] == 1) {
                $chartOfAccount = $chartOfAccount->where('AllocationID', '!=', null);
            }
        }


        if (array_key_exists('controlAccountsSystemID', $input)) {
            if ($input['controlAccountsSystemID'] && !is_null($input['controlAccountsSystemID'])) {
                $chartOfAccount->where('controlAccountsSystemID', $input['controlAccountsSystemID']);
            }
        }

        if (array_key_exists('isBank', $input)) {
            if (($input['isBank'] == 0 || $input['isBank'] == 1) && !is_null($input['isBank'])) {
                $chartOfAccount->where('isBank', $input['isBank']);
            }
        }

        if (array_key_exists('isMasterAccount', $input)) {
            if (($input['isMasterAccount'] == 0 || $input['isMasterAccount'] == 1) && !is_null($input['isMasterAccount'])) {
                $chartOfAccount->where('isMasterAccount', $input['isMasterAccount']);
            }
        }

        if (array_key_exists('catogaryBLorPLID', $input)) {
            if ($input['catogaryBLorPLID'] && !is_null($input['catogaryBLorPLID'])) {
                $chartOfAccount->where('catogaryBLorPLID', $input['catogaryBLorPLID']);
            }
        }

        $search = $request->input('search.value');
        if ($search) {
            $chartOfAccount = $chartOfAccount->where(function ($query) use ($search) {
                $query->where('AccountCode', 'LIKE', "%{$search}%")
                    ->orWhere('AccountDescription', 'LIKE', "%{$search}%");
            });
        }


        $chartOfAccount = $chartOfAccount->get();

        if ($chartOfAccount) {
            $x = 0;
            $data = array();
            foreach ($chartOfAccount as $val) {
                if ($val->confirmedYN == 1 && $val->isApproved == 0 && $val->refferedBackYN == -1) {
                    $status = "Referred Back";
                } else if ($val->isActive == 0) {
                    $status = "Not Active";
                } else if (($val->isActive == 1 || $val->isActive == -1) && $val->confirmedYN == 0 && $val->isApproved == 0) {
                    $status = "Active Only";
                } else if (($val->isActive == 1 || $val->isActive == -1) && ($val->confirmedYN == 1 || $val->confirmedYN == -1) && $val->isApproved == 0) {
                    $status = "Not Approved";
                } else if (($val->isActive == 1 || $val->isActive == -1) && ($val->confirmedYN == 1 || $val->confirmedYN == -1) && ($val->isApproved == 1 || $val->isApproved == -1)) {
                    $status = "Fully Approved";
                }

                $data[$x]['Account Code'] = $val->AccountCode;
                $data[$x]['Account Description'] = $val->AccountDescription;
                // $data[$x]['Master Account'] = $val->masterAccount;
                $data[$x]['Control Account'] = isset($val->controlAccount->description) ? $val->controlAccount->description : '';
                $data[$x]['Category BL or PL'] = isset($val->accountType->description) ? $val->accountType->description : '';
                $data[$x]['Report Template'] = isset($val->templateCategoryDetails->master->description) ? $val->templateCategoryDetails->master->description : '';
                $data[$x]['Default Template Category'] = isset($val->templateCategoryDetails->description) ? $val->templateCategoryDetails->description : '';
                $data[$x]['isBank'] = ($val->isBank) ? "Yes" : 'No';
                $data[$x]['Allocation'] = isset($val->allocation->Desciption) ? $val->allocation->Desciption : '';
                $data[$x]['Status'] = $status;
                $x++;
            }
        } else {
            $data = array();
        }

        $companyMaster = Company::find($companyId);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $detail_array = array(
            'company_code'=>$companyCode,
        );

        $fileName = 'chart_of_accounts_';
        $path = 'system/chart_of_accounts_/excel/';
        $basePath = CreateExcel::process($data,$request->exte_type,$fileName,$path,$detail_array);

        if($basePath == '')
        {
             return $this->sendError('Unable to export excel');
        }
        else
        {
             return $this->sendResponse($basePath, trans('custom.success_export'));
        }
    }

    public function chartOfAccountReopen(Request $request)
    {
        $reopen = ReopenDocument::reopenDocument($request);
        if (!$reopen["success"]) {
            return $this->sendError($reopen["message"]);
        } else {
            return $this->sendResponse(array(), $reopen["message"]);
        }
    }
    public function getChartOfAccountDetails($id)
    {
        $chartOfAccount = $this->chartOfAccountRepository->with(['primaryCompany', 'controlAccount', 'allocation', 'accountType', 'templateCategoryDetails'])->findWithoutFail($id);

        return $this->sendResponse($chartOfAccount->toArray(), 'Chart Of Account retrieved successfully');
    }
    public function printChartOfAccount(Request $request)
    {
        $id = $request->get('id');
        $chartOfAccount = $this->chartOfAccountRepository->with(['primaryCompany', 'controlAccount', 'allocation', 'accountType', 'templateCategoryDetails'])->findWithoutFail($id);
        $array = [
            'chartOfAccount' => $chartOfAccount
        ];
        $time = strtotime("now");
        $fileName = 'chart_of_account_' . $id . '_' . $time . '.pdf';
        $html = view('print.chart_of_account', $array);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-P', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
        $mpdf->AddPage('P');
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->WriteHTML($html);
        return $mpdf->Output($fileName, 'I');
    }
}
