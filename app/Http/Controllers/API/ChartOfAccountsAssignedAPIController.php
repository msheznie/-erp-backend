<?php
/**
 * =============================================
 * -- File Name : ChartOfAccountsAssignedAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Chart Of Account
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Chart Of Account assign.
 * -- REVISION HISTORY
 * -- Date: 02-October 2018 By: Nazir Description: Added new functions named as getGLForJournalVoucherDirect()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateChartOfAccountsAssignedAPIRequest;
use App\Http\Requests\API\UpdateChartOfAccountsAssignedAPIRequest;
use App\Models\ChartOfAccountsAssigned;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\Tax;
use App\Models\TaxAuthority;
use App\Models\ProjectGlDetail;
use App\Repositories\ChartOfAccountsAssignedRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Repositories\UserRepository;

/**
 * Class ChartOfAccountsAssignedController
 * @package App\Http\Controllers\API
 */
class ChartOfAccountsAssignedAPIController extends AppBaseController
{
    /** @var  ChartOfAccountsAssignedRepository */
    private $chartOfAccountsAssignedRepository;
    private $userRepository;

    public function __construct(ChartOfAccountsAssignedRepository $chartOfAccountsAssignedRepo, UserRepository $userRepo)
    {
        $this->chartOfAccountsAssignedRepository = $chartOfAccountsAssignedRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the ChartOfAccountsAssigned.
     * GET|HEAD /chartOfAccountsAssigneds
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->chartOfAccountsAssignedRepository->pushCriteria(new RequestCriteria($request));
        $this->chartOfAccountsAssignedRepository->pushCriteria(new LimitOffsetCriteria($request));
        $chartOfAccountsAssigneds = $this->chartOfAccountsAssignedRepository->all();

        return $this->sendResponse($chartOfAccountsAssigneds->toArray(), 'Chart Of Accounts Assigneds retrieved successfully');
    }

    /**
     * Store a newly created ChartOfAccountsAssigned in storage.
     * POST /chartOfAccountsAssigneds
     *
     * @param CreateChartOfAccountsAssignedAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateChartOfAccountsAssignedAPIRequest $request)
    {

      
        $input = $request->all();
        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['empID'];
        $empName = $user->employee['empName'];
        $input = array_except($input, ['final_approved_by','company']);
        $companies = $input['companySystemID'];
        $input = $this->convertArrayToValue($input);
        if (array_key_exists('chartOfAccountsAssignedID', $input)) {
            $chartOfAccountsAssigned = ChartOfAccountsAssigned::find($input['chartOfAccountsAssignedID']);

            if (empty($chartOfAccountsAssigned)) {
                return $this->sendError('Chart of Account company assigned not found!', 404);
            }

            $chartofaccountData = ChartOfAccount::find($chartOfAccountsAssigned->chartOfAccountSystemID);

            if (!$chartofaccountData) {
                return $this->sendError('Chart of Account not found!', 404);
            }

            if ($chartofaccountData->isMasterAccount == 1) {
                if ($input['isAssigned'] == 0 || !$input['isAssigned']) {
                    $checkSubAccountIsAssigned = ChartOfAccount::where('masterAccount', $chartofaccountData->AccountCode)
                                                               ->where('isMasterAccount', 0)
                                                               ->whereHas('chartofaccount_assigned', function($query) use ($chartOfAccountsAssigned) {
                                                                    $query->where('companySystemID', $chartOfAccountsAssigned->companySystemID)
                                                                          ->where(function($query) {
                                                                            $query->where('isAssigned', -1)
                                                                                 ->orWhere('isActive', 1);
                                                                          });
                                                               })
                                                               ->first();

                    if ($checkSubAccountIsAssigned) {
                        return $this->sendError('A sub ledger account is assigned and active to this company, therefore you cannot unassign');
                    }
                }
            } 
            // else {
            //     if ($input['isActive'] == 1 || $input['isActive'] || $input['isAssigned']) {
            //         $checkMasterAccountIsAssigned = ChartOfAccount::where('AccountCode', $chartofaccountData->masterAccount)
            //                                                    ->where('isMasterAccount', 1)
            //                                                    ->whereHas('chartofaccount_assigned', function($query) use ($chartOfAccountsAssigned) {
            //                                                         $query->where('companySystemID', $chartOfAccountsAssigned->companySystemID)
            //                                                               ->where('isAssigned', -1)
            //                                                               ->where('isActive', 1);
            //                                                    })
            //                                                    ->first();

            //         if (!$checkMasterAccountIsAssigned) {
            //             return $this->sendError('Master account is not assigned or inactive to this company, therefore you cannot update');
            //         }
            //     }
            // }

            $input = $this->convertArrayToValue($input);

            foreach ($input as $key => $value) {

                if($key == 'isAssigned' && $value){
                    $value = -1;
                }
                $chartOfAccountsAssigned->$key = $value;
            }

            $chartOfAccountsAssigned->save();
        } else {
            unset($input['companySystemID']);
  
            foreach($companies as $companie)
            {
                
                $validatorResult = \Helper::checkCompanyForMasters($companie['id'], $input['chartOfAccountSystemID'], 'chartofaccounts');
                if (!$validatorResult['success']) {
                    return $this->sendError($validatorResult['message']);
                }

                $chartofaccountData = ChartOfAccount::find($input['chartOfAccountSystemID']);

                if (!$chartofaccountData) {
                    return $this->sendError('Chart of Account not found!', 404);
                }

                // if ($chartofaccountData->isMasterAccount == 0) {
                //     $checkMasterAccountIsAssigned = ChartOfAccount::where('AccountCode', $chartofaccountData->masterAccount)
                //                                                ->where('isMasterAccount', 1)
                //                                                ->whereHas('chartofaccount_assigned', function($query) use ($input) {
                //                                                     $query->where('companySystemID', $input['companySystemID'])
                //                                                           ->where('isAssigned', -1)
                //                                                           ->where('isActive', 1);
                //                                                })
                //                                                ->first();

                //     if (!$checkMasterAccountIsAssigned) {
                //         return $this->sendError('Master account is not assigned or inactive to this company, therefore you cannot assign');
                //     }
                // }


   
             
                $input = $this->convertArrayToValue($input);
                $company = Company::find($companie['id']);
                $input['companySystemID'] = $companie['id'];
                $input['companyID'] = $company->CompanyID;
                $input['isAssigned'] = -1;
                $input['isActive'] = 1;
                $chartOfAccountsAssigned = $this->chartOfAccountsAssignedRepository->create($input);
            }
    

        }


        return $this->sendResponse($chartOfAccountsAssigned->toArray(), 'Chart Of Accounts Assigned saved successfully');
    }

    /**
     * Display the specified ChartOfAccountsAssigned.
     * GET|HEAD /chartOfAccountsAssigneds/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ChartOfAccountsAssigned $chartOfAccountsAssigned */
        $chartOfAccountsAssigned = $this->chartOfAccountsAssignedRepository->findWithoutFail($id);

        if (empty($chartOfAccountsAssigned)) {
            return $this->sendError('Chart Of Accounts Assigned not found');
        }

        return $this->sendResponse($chartOfAccountsAssigned->toArray(), 'Chart Of Accounts Assigned retrieved successfully');
    }

    /**
     * Update the specified ChartOfAccountsAssigned in storage.
     * PUT/PATCH /chartOfAccountsAssigneds/{id}
     *
     * @param  int $id
     * @param UpdateChartOfAccountsAssignedAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateChartOfAccountsAssignedAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input,['company']);
        /** @var ChartOfAccountsAssigned $chartOfAccountsAssigned */
        $chartOfAccountsAssigned = $this->chartOfAccountsAssignedRepository->findWithoutFail($id);

        if (empty($chartOfAccountsAssigned)) {
            return $this->sendError('Chart Of Accounts Assigned not found');
        }

        $chartOfAccountsAssigned = $this->chartOfAccountsAssignedRepository->update($input, $id);

        return $this->sendResponse($chartOfAccountsAssigned->toArray(), 'ChartOfAccountsAssigned updated successfully');
    }

    /**
     * Remove the specified ChartOfAccountsAssigned from storage.
     * DELETE /chartOfAccountsAssigneds/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ChartOfAccountsAssigned $chartOfAccountsAssigned */
        $chartOfAccountsAssigned = $this->chartOfAccountsAssignedRepository->findWithoutFail($id);

        if (empty($chartOfAccountsAssigned)) {
            return $this->sendError('Chart Of Accounts Assigned not found');
        }

        $chartofaccountData = ChartOfAccount::find($chartOfAccountsAssigned->chartOfAccountSystemID);

        if (!$chartofaccountData) {
            return $this->sendError('Chart of Account not found!', 404);
        }

        if ($chartofaccountData->isMasterAccount == 1) {
            $checkSubAccountIsAssigned = ChartOfAccount::where('masterAccount', $chartofaccountData->AccountCode)
                                                       ->where('isMasterAccount', 0)
                                                       ->whereHas('chartofaccount_assigned', function($query) use ($chartOfAccountsAssigned) {
                                                            $query->where('companySystemID', $chartOfAccountsAssigned->companySystemID)
                                                                          ->where(function($query) {
                                                                            $query->where('isAssigned', -1)
                                                                                 ->orWhere('isActive', 1);
                                                                          });
                                                       })
                                                       ->first();

            if ($checkSubAccountIsAssigned) {
                return $this->sendError('A sub ledger account is assigned and active to this company, therefore you cannot delete');
            }
        } 

        $checkGlIsSelectedForVAT = Tax::where('companySystemID', $chartOfAccountsAssigned->companySystemID)
                                      ->where(function($query) use ($chartOfAccountsAssigned) {
                                         $query->where('inputVatGLAccountAutoID', $chartOfAccountsAssigned->chartOfAccountSystemID)
                                               ->orWhere('outputVatGLAccountAutoID', $chartOfAccountsAssigned->chartOfAccountSystemID)
                                               ->orWhere('inputVatTransferGLAccountAutoID', $chartOfAccountsAssigned->chartOfAccountSystemID)
                                               ->orWhere('GLAutoID', $chartOfAccountsAssigned->chartOfAccountSystemID)
                                               ->orWhere('outputVatTransferGLAccountAutoID', $chartOfAccountsAssigned->chartOfAccountSystemID);
                                      })
                                      ->first();

        $checkGlInTaxAuthority = TaxAuthority::where('companySystemID', $chartOfAccountsAssigned->companySystemID)
                                             ->where('taxPayableGLAutoID', $chartOfAccountsAssigned->chartOfAccountSystemID)
                                             ->first();


        if ($checkGlIsSelectedForVAT || $checkGlInTaxAuthority) {
            return $this->sendError('Chart of account is selcted for VAT setup of this company, therefore you cannot delete');
        }


        $chartOfAccountsAssigned->delete();

        return $this->sendResponse($id, 'Chart Of Accounts Assigned deleted successfully');
    }

    public function getDirectInvoiceGL(request $request)
    {
        $input = $request->all();
        $companyID = $input['companyID'];


        $items = ChartOfAccountsAssigned::where('companySystemID', $companyID)
            ->where('controllAccountYN', 0)
            ->where('isAssigned', -1)
            ->where('isActive', 1);

        if (isset($input['controllAccountYN'])) {
            $items = $items->where('controllAccountYN', $input['controllAccountYN']);
        }

        if (isset($input['isBank'])) {
            $items = $items->where('isBank', $input['isBank']);
        }

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('AccountCode', 'LIKE', "%{$search}%")
                    ->orWhere('AccountDescription', 'LIKE', "%{$search}%");
            });
        }

        $items = $items->take(20)->get();
        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');

    }

    public function gl_code_search(request $request){
        $input = $request->all();
        $companyID = $input['companyID'];
        $from_master_tbl = $input['from_master_tbl'];
        $from_master_tbl =($from_master_tbl == 'true');


        if($from_master_tbl){
            $data = ChartOfAccount::where('isActive', 1);
        }
        else{
            $data = ChartOfAccountsAssigned::where('companySystemID', $companyID)
                ->where('isAssigned', -1)
                ->where('isActive', 1);
        }

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $data = $data->where(function ($query) use ($search) {
                $query->where('AccountCode', 'LIKE', "%{$search}%")
                    ->orWhere('AccountDescription', 'LIKE', "%{$search}%");
            });
        }

        $data = $data->get();
        return $this->sendResponse($data->toArray(), 'Data retrieved successfully');
    }

    public function getPaymentVoucherGL(request $request)
    {
        $input = $request->all();
        $companyID = $input['companyID'];


        $items = ChartOfAccountsAssigned::whereHas('chartofaccount', function ($q) {
            $q->where('isApproved', 1);
        })->where('companySystemID', $companyID)
            ->where('isAssigned', -1)
            ->where('controllAccountYN', 0)
            ->where('controlAccountsSystemID', '<>', 1)
            ->where('isActive', 1)
            ->when((isset($input['expenseClaimOrPettyCash']) && $input['expenseClaimOrPettyCash'] == 15), function ($query) {
                $query->where('isBank',1);
            })
            ->when((!isset($input['expenseClaimOrPettyCash']) || (isset($input['expenseClaimOrPettyCash']) && $input['expenseClaimOrPettyCash'] != 15)), function ($query) {
                $query->where('isBank',0);
            });

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('AccountCode', 'LIKE', "%{$search}%")
                    ->orWhere('AccountDescription', 'LIKE', "%{$search}%");
            });
        }

        $items = $items->take(20)->get();
        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');

    }

    public function getGLForJournalVoucherDirect(request $request)
    {
        $input = $request->all();
        $companyID = $input['companyID'];

        $items = ChartOfAccountsAssigned::whereHas('chartofaccount', function ($q) {
            $q->where('isApproved', 1);
        })->where('companySystemID', $companyID)
            ->where('controllAccountYN', 0)
            ->where('isBank', 0)
            ->where('isAssigned', -1)
            ->where('isActive', 1);

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('AccountCode', 'LIKE', "%{$search}%")
                    ->orWhere('AccountDescription', 'LIKE', "%{$search}%");
            });
        }

        $items = $items->take(20)->get();
        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');

    }

    public function getGlAccounts(request $request){
        $input = $request->all();
        $companyID = $input['companySystemID'];
        $projectID = $input['projectID'];
        $items = ChartOfAccountsAssigned::whereHas('chartofaccount', function ($q) {
            $q->where('isApproved', 1);
        })->where('companySystemID', $companyID)
            ->where('controllAccountYN', 0)
            ->where('isBank', 0)
            ->where('isAssigned', -1)
            ->where('isActive', 1)
            ->whereDoesntHave('project',function($q) use ($projectID) {
                $q->where('projectID',$projectID);
            });
        $items = $items->get();

        if (empty($items)) {
            return $this->sendError('Data not found');
        } 
        return $this->sendResponse($items, 'Data retrieved successfully');
    }

    public function getglDetails(request $request){
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $input['companySystemID'];
        $projectID = $input['projectID'];
        $glDetails = ProjectGlDetail::with('chartofaccounts')
                                    ->where('companySystemID', $companyID)
                                    ->where('projectID' , $projectID)->get();

        if (empty($glDetails)) {
                return $this->sendError('Data not found');
        } 

        return \DataTables::of($glDetails)
            ->addIndexColumn()
            ->make(true);
    }

    public function getAssignedChartOfAccounts(request $request)
    {
        $input = $request->all();
        $companyID = $input['companyID'];

        $items = ChartOfAccountsAssigned::where('companySystemID', $companyID)
            ->where('isAssigned', -1)
            ->where('isActive', 1);

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

    public function getCompanyWiseSubLedgerAccounts(request $request)
    {
        $input = $request->all();

        $chartofaccountData = ChartOfAccount::find($input['chartOfAccountSystemID']);

        if (!$chartofaccountData) {
            return $this->sendError('Chart of Account not found!', 404);
        }

        $checkSubAccountIsAssigned = ChartOfAccountsAssigned::with(['company'])
                                                             ->where('companySystemID', $input['selectedCompanyId'])
                                                             ->whereHas('chartofaccount', function($query) use ($chartofaccountData) {
                                                                  $query->where('masterAccount', $chartofaccountData->AccountCode)
                                                                        ->where('isMasterAccount', 0);
                                                             })
                                                             ->get();

        return $this->sendResponse($checkSubAccountIsAssigned, 'Data retrieved successfully');

    }

    public function getGLForRecurringVoucherDirect(request $request)
    {
        $input = $request->all();
        $companyID = $input['companyID'];

        $items = ChartOfAccountsAssigned::whereHas('chartofaccount', function ($q) {
            $q->where('isApproved', 1);
        })->where('companySystemID', $companyID)
            ->where('controllAccountYN', 0)
            ->where('isAssigned', -1)
            ->where('isActive', 1);

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('AccountCode', 'LIKE', "%{$search}%")
                    ->orWhere('AccountDescription', 'LIKE', "%{$search}%");
            });
        }

        $items = $items->take(20)->get();
        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');

    }
}
