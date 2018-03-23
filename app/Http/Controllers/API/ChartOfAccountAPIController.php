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

/**
 * Class ChartOfAccountController
 * @package App\Http\Controllers\API
 */
class ChartOfAccountAPIController extends AppBaseController
{
    /** @var  ChartOfAccountRepository */
    private $chartOfAccountRepository;

    public function __construct(ChartOfAccountRepository $chartOfAccountRepo)
    {
        $this->chartOfAccountRepository = $chartOfAccountRepo;
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



        if(array_key_exists ('catogaryBLorPLID' , $input )){
            $categoryBLorPL = AccountsType::where('accountsType',$input['catogaryBLorPLID'])->first();
            if($categoryBLorPL){
                $input['catogaryBLorPL'] = $categoryBLorPL->code ;
            }

        }

        $input['documentSystemID'] = 59 ;
        $input['documentID'] = 'CAM';

        $chartOfAccounts = $this->chartOfAccountRepository->create($input);

        return $this->sendResponse($chartOfAccounts->toArray(), 'Chart Of Account saved successfully');
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
        $chartOfAccount = ChartOfAccount::all('AccountCode','AccountDescription');


        $output = array('controlAccounts' => $controlAccounts,
            'accountsType' => $accountsType,
            'yesNoSelection' => $yesNoSelection,
            'chartOfAccount' => $chartOfAccount
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }


}
