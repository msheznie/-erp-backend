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
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateChartOfAccountAPIRequest;
use App\Http\Requests\API\UpdateChartOfAccountAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\ControlAccount;
use App\Models\AccountsType;
use App\Models\YesNoSelection;
use App\Repositories\ChartOfAccountRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
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

        /** Validation massage : Common for Add & Update */
        $accountCode = isset($input['AccountCode']) ? $input['AccountCode'] : '';

        $messages = array(
            'AccountCode.unique' => 'The Account ' . $accountCode . ' code has already been taken'
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



            $chartOfAccount->modifiedPc = gethostname();
            $chartOfAccount->modifiedUser = $empId;


            $empName = $user->employee['empName'];
            $employeeSystemID = $user->employee['employeeSystemID'];

            if ($input['confirmedYN'] == 1) {
                $input['confirmedEmpSystemID'] = $employeeSystemID;
                $input['confirmedEmpID'] = $empId;
                $input['confirmedEmpName'] = $empName;
                $input['confirmedEmpDate'] = date('Y-m-d H:i:s');
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
        $chartOfAccount = $this->chartOfAccountRepository->findWithoutFail($id);

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

        //$companyId = $request['companyId'];

        $chartOfAccount = ChartOfAccount::with(['controlAccount', 'accountType']);

        if (array_key_exists('controlAccountsSystemID', $input)) {
            if ($request['controlAccountsSystemID']) {
                $chartOfAccount->where('controlAccountsSystemID', $input['controlAccountsSystemID']);
            }
        }

        if (array_key_exists('isBank', $input)) {
            if ($request['isBank'] == 0 || $input['isBank'] == 1) {
                $chartOfAccount->where('isBank', $input['isBank']);
            }
        }

        if (array_key_exists('catogaryBLorPLID', $input)) {
            if ($input['catogaryBLorPLID']) {
                $chartOfAccount->where('catogaryBLorPLID', $input['catogaryBLorPLID']);
            }
        }

        return \DataTables::eloquent($chartOfAccount)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    /**
     * get form data for Chart of Account.
     * POST /getChartOfAccountFormData
     *
     */
    public function getChartOfAccountFormData()
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


        $output = array('controlAccounts' => $controlAccounts,
            'accountsType' => $accountsType,
            'yesNoSelection' => $yesNoSelection,
            'chartOfAccount' => $chartOfAccount
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }


}
