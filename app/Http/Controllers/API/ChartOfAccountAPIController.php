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
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\ChartOfAccountsRefferedBack;
use App\Models\Company;
use App\Models\ControlAccount;
use App\Models\AccountsType;
use App\Models\DocumentApproved;
use App\Models\DocumentReferedHistory;
use App\Models\YesNoSelection;
use App\Repositories\ChartOfAccountRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use Illuminate\Validation\Rule;

/**
 * Class ChartOfAccountController
 * @package App\Http\Controllers\API
 */
class ChartOfAccountAPIController extends AppBaseController
{
    /** @var  ChartOfAccountRepository */
    private $chartOfAccountRepository;
    private $userRepository;

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

        $messages = array(
            'AccountCode.unique' => 'Account code' . $accountCode . ' already exists'
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

        $input['documentSystemID'] = 59;
        $input['documentID'] = 'CAM';

        $company = Company::where('companySystemID',$input['primaryCompanySystemID'])->first();

        if($company){
            $input['primaryCompanyID'] = $company->CompanyID;
        }

        if (array_key_exists('chartOfAccountSystemID', $input)) {

            $chartOfAccount = ChartOfAccount::where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])->first();

            if (empty($chartOfAccount)) {
                return $this->sendError('Chart of Account not found!', 404);
            }
            // $input = array_except($input,['currency_master']); // uses only in sub sub tables
            $input = $this->convertArrayToValue($input);

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


            $empName = $user->employee['empName'];
            $employeeSystemID = $user->employee['employeeSystemID'];

            if ($input['confirmedYN'] == 1 && $chartOfAccount->confirmedYN == 0) {
                $params = array('autoID' => $input['chartOfAccountSystemID'], 'company' => $input["primaryCompanySystemID"], 'document' => $input["documentSystemID"]);
                $confirm = \Helper::confirmDocument($params);
                if(!$confirm["success"]){
                    return $this->sendError($confirm["message"]);
                }
            }


            foreach ($input as $key => $value) {
                $chartOfAccount->$key = $value;
            }

            $chartOfAccount->save();

            //return $chartOfAccount;

        } else {

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
        }


        return $this->sendResponse($chartOfAccount->toArray(), 'Chart Of Account saved successfully');
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
        $data = ChartOfAccountsAssigned::where('chartOfAccountSystemID', '=', $chartOfAccountSystemID)->first();


        if ($data) {
            $itemCompanies = ChartOfAccountsAssigned::where('chartOfAccountSystemID',$chartOfAccountSystemID)->with(['company'])->get();
        } else {
            $itemCompanies = [];
        }

        return $this->sendResponse($itemCompanies, 'Companies retrieved successfully');
    }

    public function getNotAssignedCompaniesByChartOfAccount(Request $request){
        $chartOfAccountSystemID = $request->get('chartOfAccountSystemID');
        $selectedCompanyId = $request['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }


        $companies = Company::whereIn("companySystemID",$subCompanies)
            ->whereDoesntHave('chartOfAccountAssigned',function ($query) use ($chartOfAccountSystemID) {
                $query->where('chartOfAccountSystemID', '=', $chartOfAccountSystemID);
            })
            ->get(['companySystemID',
                'CompanyID',
                'CompanyName']);

        return $this->sendResponse($companies->toArray(), 'Companies retrieved successfully');
    }


    /**
     * Display the specified ChartOfAccount.
     * GET|HEAD /chartOfAccounts/{id}
     *
     * @param  int $id
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
     * @param  int $id
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
     * @param  int $id
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
        $input = $this->convertArrayToSelectedValue($input,array('controlAccountsSystemID','isBank','catogaryBLorPLID'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if($isGroup){
            $childCompanies = \Helper::getGroupCompany($companyId);
        }else{
            $childCompanies = [$companyId];
        }

        $chartOfAccount = ChartOfAccount::with(['controlAccount', 'accountType']);
                                       // ->whereIn('primaryCompanySystemID',$childCompanies);

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

        if (array_key_exists('catogaryBLorPLID', $input)) {
            if ($input['catogaryBLorPLID'] && !is_null($input['catogaryBLorPLID'])) {
                $chartOfAccount->where('catogaryBLorPLID', $input['catogaryBLorPLID']);
            }
        }
        $chartOfAccount->select('chartofaccounts.*');

        $search = $request->input('search.value');
        if($search){
            $chartOfAccount =   $chartOfAccount->where(function ($query) use($search) {
                $query->where('AccountCode','LIKE',"%{$search}%")
                    ->orWhere('AccountDescription', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($chartOfAccount)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('chartOfAccountSystemID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
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

        if($isGroup){
            $companyID = \Helper::getGroupCompany($companyId);
        }else{
            $companyID = [$companyId];
        }
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');

        $chartOfAccount = DB::table('erp_documentapproved')->select('chartofaccounts.*','controlaccounts.description as controlaccountdescription','accountstype.description as accountstypedescription','erp_documentapproved.documentApprovedID','rollLevelOrder','approvalLevelID','documentSystemCode')->join('employeesdepartments',function ($query) use ($companyID,$empID) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID')
                ->where('employeesdepartments.documentSystemID',59)
                ->whereIn('employeesdepartments.companySystemID',$companyID)
                ->where('employeesdepartments.employeeSystemID',$empID);
        })->join('chartofaccounts',function ($query) use ($companyID,$empID,$search) {
            $query->on('chartOfAccountSystemID','=','erp_documentapproved.documentSystemCode')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->whereIn('primaryCompanySystemID',$companyID)
                ->where('isApproved', 0)
                ->where('chartofaccounts.confirmedYN', 1)
                ->when($search != "", function ($q) use($search){
                    $q->where(function ($query) use($search) {
                        $query->where('AccountCode','LIKE',"%{$search}%")
                            ->orWhere('AccountDescription', 'LIKE', "%{$search}%");
                    });
                });
        })
            ->leftJoin('controlaccounts','controlaccounts.controlAccountsSystemID','=','chartofaccounts.controlAccountsSystemID')
            ->leftJoin('accountstype','catogaryBLorPLID','=','accountsType')
            ->where('erp_documentapproved.approvedYN', 0)
            ->where('erp_documentapproved.rejectedYN',0)
            ->where('erp_documentapproved.documentSystemID',59)
            ->whereIn('erp_documentapproved.companySystemID',$companyID);

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
            ->make(true);
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
        $accountsType = AccountsType::all();

        /** all Account Types */
        $chartOfAccount = ChartOfAccount::where('isMasterAccount', 1)->get(['AccountCode', 'AccountDescription']);
        //$chartOfAccount = ChartOfAccount::all('AccountCode', 'AccountDescription');

        $selectedCompanyId = $request['selectedCompanyId'];

        /** all Company  Drop Down */

        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){

           // $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
            $subCompanies  = \Helper::getSubCompaniesByGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }

        /**  Companies by group  Drop Down */
        $allCompanies = Company::whereIn("companySystemID",$subCompanies)->get();

        $output = array('controlAccounts' => $controlAccounts,
            'accountsType' => $accountsType,
            'yesNoSelection' => $yesNoSelection,
            'chartOfAccount' => $chartOfAccount,
            'allCompanies' => $allCompanies,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }


    public function approveChartOfAccount(Request $request){
        $approve = \Helper::approveDocument($request);
        if(!$approve["success"]){
            return $this->sendError($approve["message"]);
        }else{
            return $this->sendResponse(array(),$approve["message"]);
        }

    }

    public function rejectChartOfAccount(Request $request){
        $reject = \Helper::rejectDocument($request);
        if(!$reject["success"]){
            return $this->sendError($reject["message"]);
        }else{
            return $this->sendResponse(array(),$reject["message"]);
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

        $items = ChartOfAccount::where('isActive', 1);

        if (isset($input['controllAccountYN'])) {
            $items = $items->where('controllAccountYN', $input['controllAccountYN']);
        }

        if (isset($input['isBank'])) {
            $items = $items->where('isBank', $input['isBank']);
        }

        if (isset($input['catogaryBLorPL'])) {
            if($input['catogaryBLorPL']) {
                $items = $items->where('catogaryBLorPL', $input['catogaryBLorPL']);
            }
        }

        $items = $items->get();
        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');

    }
}
