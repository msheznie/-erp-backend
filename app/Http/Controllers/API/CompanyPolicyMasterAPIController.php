<?php
/**
 * =============================================
 * -- File Name : CompanyPolicyMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Approval
 * -- Author : Mubashir
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Company Policy.
 * -- REVISION HISTORY
 * -- Date: 11- May 2018 By: Fayas Description: Added a new function getAllCompanyPolicy(),getCompanyPolicyFilterOptions.
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCompanyPolicyMasterAPIRequest;
use App\Http\Requests\API\UpdateCompanyPolicyMasterAPIRequest;
use App\Models\BankLedger;
use App\Models\Company;
use App\Models\CompanyPolicyCategory;
use App\Models\CompanyPolicyMaster;
use App\Models\DocumentEmailNotificationMaster;
use App\Repositories\CompanyPolicyMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CompanyPolicyMasterController
 * @package App\Http\Controllers\API
 */

class CompanyPolicyMasterAPIController extends AppBaseController
{
    /** @var  CompanyPolicyMasterRepository */
    private $companyPolicyMasterRepository;

    public function __construct(CompanyPolicyMasterRepository $companyPolicyMasterRepo)
    {
        $this->companyPolicyMasterRepository = $companyPolicyMasterRepo;
    }

    /**
     * Display a listing of the CompanyPolicyMaster.
     * GET|HEAD /companyPolicyMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->companyPolicyMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->companyPolicyMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $companyPolicyMasters = $this->companyPolicyMasterRepository->all();

        return $this->sendResponse($companyPolicyMasters->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.company_policy_masters')]));
    }

    /**
     * Display a listing of the CompanyPolicyMaster.
     * POST|HEAD /getAllCompanyPolicy
     *
     * @param Request $request
     * @return Response
     */
    public function getAllCompanyPolicy(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companySystemID'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if($isGroup){
            $childCompanies = \Helper::getGroupCompany($companyId);
        }else{
            $childCompanies = [$companyId];
        }
        $search = $request->input('search.value'); 
        $companyPolicyMasters = CompanyPolicyMaster::with(['company','policyCategory' => function($q) use($search){
            $q->where('isActive',-1)
              ->when($search, function ($q) use ($search) {
                return $q->where('companyPolicyCategoryDescription', 'LIKE', "%{$search}%");
            });
        }])
        ->whereHas('policyCategory',function ($q){
            $q->where('isActive',-1);
        })
        ->when($search,function ($q) use($search){
           return  $q->whereHas('policyCategory',function ($q) use($search){
               return $q->where('companyPolicyCategoryDescription', 'LIKE', "%{$search}%");
           });
        })
        ->whereIn('companySystemID',$childCompanies);


        if (array_key_exists('companyPolicyCategoryID', $input)) {
            $companyPolicyMasters = $companyPolicyMasters->where('companyPolicyCategoryID', $input['companyPolicyCategoryID']);
        }



        return \DataTables::eloquent($companyPolicyMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('companyPolicyMasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    /**
     * get company policy filter options.
     * GET /getCompanyPolicyFilterOptions
     *
     * @param Request $request
     * @return Response
     */
    public function getCompanyPolicyFilterOptions(Request $request)
    {

        $selectedCompanyId = $request['selectedCompanyId'];

        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }

        /**  Companies by group  Drop Down */
        $companies = Company::whereIn("companySystemID",$subCompanies)->get();

        $policyCategories = CompanyPolicyCategory::where('isActive',-1)->get();

        $emailPolicyCategories = DocumentEmailNotificationMaster::all();

        $output = array('companies' => $companies,
                        'policyCategories' => $policyCategories,
                        'emailPolicyCategories' => $emailPolicyCategories
                       );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));

    }


    /**
     * Store a newly created CompanyPolicyMaster in storage.
     * POST /companyPolicyMasters
     *
     * @param CreateCompanyPolicyMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCompanyPolicyMasterAPIRequest $request)
    {
        $input = $request->all();

        $companyPolicyMasters = $this->companyPolicyMasterRepository->create($input);

        return $this->sendResponse($companyPolicyMasters->toArray(), trans('custom.company_policy_master_saved_successfully'));
    }

    /**
     * Display the specified CompanyPolicyMaster.
     * GET|HEAD /companyPolicyMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CompanyPolicyMaster $companyPolicyMaster */
        $companyPolicyMaster = $this->companyPolicyMasterRepository->findWithoutFail($id);

        if (empty($companyPolicyMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company_policy_masters')]));
        }

        return $this->sendResponse($companyPolicyMaster->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.company_policy_masters')]));
    }

    /**
     * Update the specified CompanyPolicyMaster in storage.
     * PUT/PATCH /companyPolicyMasters/{id}
     *
     * @param  int $id
     * @param UpdateCompanyPolicyMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCompanyPolicyMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['company',
                                        'policy_category',
                                        'companySystemID',
                                        'companyID',
                                        'documentID',
                                        'policyValue',
                                        'createdByUserID',
                                        'createdByUserName',
                                        'createdByPCID',
                                        'createdDateTime']);

        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();
        $input['modifiedByUserID'] = $employee['employeeSystemID'];
        $input['timestamp'] = now();

        /** @var CompanyPolicyMaster $companyPolicyMaster */
        $companyPolicyMaster = $this->companyPolicyMasterRepository->findWithoutFail($id);

        if (empty($companyPolicyMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company_policy_masters')]));
        }

        $companyPolicyMaster = $this->companyPolicyMasterRepository->update($input, $id);

        return $this->sendResponse($companyPolicyMaster->toArray(), trans('custom.update', ['attribute' => trans('custom.company_policy_masters')]));
    }

    /**
     * Remove the specified CompanyPolicyMaster from storage.
     * DELETE /companyPolicyMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CompanyPolicyMaster $companyPolicyMaster */
        $companyPolicyMaster = $this->companyPolicyMasterRepository->findWithoutFail($id);

        if (empty($companyPolicyMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company_policy_masters')]));
        }

        $companyPolicyMaster->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.company_policy_masters')]));
    }

    public function checkPendingTreasuryClearance(Request $request)
    {
        $input = $request->all();
        $treasuryNotCleared = BankLedger::where('companySystemID', $input['selectedCompanyId'])
            ->where('trsClearedYN', 0)
            ->first();

        if(!empty($treasuryNotCleared)) {
            return $this->sendError('Pending treasury clearance documents exists');
        } else {
            return $this->sendResponse([], 'No pending documents available');
        }
    }
}
