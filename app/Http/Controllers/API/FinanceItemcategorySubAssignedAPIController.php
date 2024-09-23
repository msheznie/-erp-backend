<?php
/**
=============================================
-- File Name : FinanceItemcategorySubAssignedAPIController.php
-- Project Name : ERP
-- Module Name :  Finance Item Category Sub Assigned
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for Finance Item Category Sub Assigned
-- REVISION HISTORY
-- Date: 14-March 2018 By: Fayas Description: Added new functions named as assignedCompaniesBySubCategory(),getNotAssignedCompanies(),
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFinanceItemcategorySubAssignedAPIRequest;
use App\Http\Requests\API\UpdateFinanceItemcategorySubAssignedAPIRequest;
use App\Models\FinanceItemCategorySub;
use App\Models\Company;
use App\Models\FinanceItemcategorySubAssigned;
use App\Repositories\FinanceItemcategorySubAssignedRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use App\Traits\AuditLogsTrait;

/**
 * Class FinanceItemcategorySubAssignedController
 * @package App\Http\Controllers\API
 */
class FinanceItemcategorySubAssignedAPIController extends AppBaseController
{
    /** @var  FinanceItemcategorySubAssignedRepository */
    private $financeItemcategorySubAssignedRepository;
    use AuditLogsTrait;

    public function __construct(FinanceItemcategorySubAssignedRepository $financeItemcategorySubAssignedRepo)
    {
        $this->financeItemcategorySubAssignedRepository = $financeItemcategorySubAssignedRepo;
    }

    /**
     * Display a listing of the FinanceItemcategorySubAssigned.
     * GET|HEAD /financeItemcategorySubAssigneds
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->financeItemcategorySubAssignedRepository->pushCriteria(new RequestCriteria($request));
        $this->financeItemcategorySubAssignedRepository->pushCriteria(new LimitOffsetCriteria($request));
        $financeItemcategorySubAssigneds = $this->financeItemcategorySubAssignedRepository->all();

        return $this->sendResponse($financeItemcategorySubAssigneds->toArray(), 'Finance Itemcategory Sub Assigneds retrieved successfully');
    }

    /**
     *  Display a listing of the Finance Item category Sub Assigned.
     * Get /assignedCompaniesBySubCategory
     *
     * @param Request $request
     *
     * @return Response
     */
    public function assignedCompaniesBySubCategory(Request $request)
    {
        $input = $request->all();
        $selectedCompanyId = $input['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }

        $financeItemcategorySubAssigneds = FinanceItemcategorySubAssigned::where('itemCategorySubID', $request->get('itemCategorySubID'))
            ->with(['company'])
            ->whereIn('companySystemID', $subCompanies)
            ->orderBy('itemCategoryAssignedID', 'DESC')
            ->paginate(10);
        //->get();

        return $this->sendResponse($financeItemcategorySubAssigneds->toArray(), 'Finance Itemcategory Sub Assigneds retrieved successfully');
    }


    /**
     * Store a newly created FinanceItemcategorySubAssigned in storage.
     * POST /financeItemcategorySubAssigneds
     *
     * @param CreateFinanceItemcategorySubAssignedAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateFinanceItemcategorySubAssignedAPIRequest $request)
    {


        $input = $request->all();
        $companies = $input['companySystemID'];

        $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid'] : 'local';
        $db = isset($input['db']) ? $input['db'] : '';

        unset($input['companySystemID']);

        if(array_key_exists('tenant_uuid', $input)){
            unset($input['tenant_uuid']);
        }

        if(array_key_exists('db', $input)){
            unset($input['db']);
        }

        if (array_key_exists('Actions', $input)) {
            unset($input['Actions']);
        }

        if (array_key_exists('Index', $input)) {
            unset($input['Index']);
        }

        if (array_key_exists('company', $input)) {
            unset($input['company']);
        }
        if (array_key_exists('finance_gl_code_pl', $input)) {
            unset($input['finance_gl_code_pl']);
        }

        if (array_key_exists('finance_gl_code_bs', $input)) {
            unset($input['finance_gl_code_bs']);
        }

        if (array_key_exists('finance_gl_code_revenue', $input)) {
            unset($input['finance_gl_code_revenue']);
        }

        foreach ($input as $key => $value) {
            if (is_array($input[$key])) {
                if (count($input[$key]) > 0) {
                    $input[$key] = isset($input[$key][0]) ? $input[$key][0] : 0;
                } else {
                    $input[$key] = 0;
                }
            }
        }
        if(isset($input['financeGLcodebBSSystemID']) && !$input['financeGLcodebBSSystemID']){
            $input['financeGLcodebBSSystemID'] = null;
        }
        if(isset($input['financeGLcodePLSystemID']) && !$input['financeGLcodePLSystemID']){
            $input['financeGLcodebBSSystemID'] = null;
        }
        if(isset($input['financeGLcodeRevenueSystemID']) && !$input['financeGLcodeRevenueSystemID']){
            $input['financeGLcodeRevenueSystemID'] = null;
        }

       
        if (array_key_exists('itemCategoryAssignedID', $input)) {

            $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('itemCategoryAssignedID', $input['itemCategoryAssignedID'])->first();

            if (empty($financeItemCategorySubAssigned)) {
                return $this->sendError('company Assigned not found');
            }
            $previousValue = $financeItemCategorySubAssigned->toArray();

            foreach ($input as $key => $value) {

                if($key == 'isAssigned' && ($value == true || $value == 1)){
                    $value = -1;
                }

                $financeItemCategorySubAssigned->$key = $value;
            }
            $financeItemCategorySubAssigned->save();


            $masterData = $financeItemCategorySubAssigned->toArray();

            $this->auditLog($db, $input['itemCategoryAssignedID'],$uuid, "financeitemcategorysubassigned", "Company Assign ".$input['categoryDescription']." has been updated", "U", $masterData, $previousValue, $input['itemCategorySubID'], 'financeitemcategorysub');
        } else {

            foreach($companies as $companie)
            {

                $company = Company::where('companySystemID', $companie['id'])->first();
                $input['companyID'] = $company->CompanyID;
                $input['companySystemID'] = $companie['id'];
                $input['isAssigned'] = -1;
                $input['mainItemCategoryID'] = $input['itemCategoryID'];

                if(isset($input['finance_item_category_type'])){
                    unset($input['finance_item_category_type']);
                }
                if(isset($input['DT_Row_Index'])){
                    unset($input['DT_Row_Index']);
                }
                
                $financeItemCategorySubAssigned = $this->financeItemcategorySubAssignedRepository->create($input);
                
                $masterData = $financeItemCategorySubAssigned->toArray();


                $this->auditLog($db, $financeItemCategorySubAssigned->itemCategoryAssignedID,$uuid, "financeitemcategorysubassigned", "Company Assign ".$input['categoryDescription']." has been created", "C", $masterData, [], $financeItemCategorySubAssigned->itemCategorySubID, 'financeitemcategorysub');
            }

        }
        
        return $this->sendResponse($financeItemCategorySubAssigned->toArray(), 'Finance Item Category Sub Assigned saved successfully');
    }


    /**
     *  Display a listing of the companies not assigned for Finance Item category Sub.
     * Get /getNotAssignedCompanies
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getNotAssignedCompanies(Request $request)
    {
        $input = $request->all();
        $selectedCompanyId = $input['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }

        $itemCategorySubID = $request->get('itemCategorySubID');
        $companies = DB::table('companymaster AS c')
            ->where('isGroup', 0)
            ->whereIn('companySystemID', $subCompanies)
            ->whereNotExists( function ($query) use ($itemCategorySubID) {
                $query
                    ->select(DB::raw(1))
                    ->from('financeitemcategorysubassigned AS fc')
                    ->whereRaw('c.companySystemID = fc.companySystemID')
                    ->where('fc.itemCategorySubID', '=', $itemCategorySubID);
            })
            ->select('c.companySystemID',
                'c.CompanyID',
                'c.CompanyName'
                )
            ->get();

        return $this->sendResponse($companies->toArray(), 'Companies retrieved successfully');
    }

    /**
     * Display the specified FinanceItemcategorySubAssigned.
     * GET|HEAD /financeItemcategorySubAssigneds/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var FinanceItemcategorySubAssigned $financeItemcategorySubAssigned */
        $financeItemcategorySubAssigned = $this->financeItemcategorySubAssignedRepository->findWithoutFail($id);

        if (empty($financeItemcategorySubAssigned)) {
            return $this->sendError('Finance Itemcategory Sub Assigned not found');
        }

        return $this->sendResponse($financeItemcategorySubAssigned->toArray(), 'Finance Itemcategory Sub Assigned retrieved successfully');
    }

    /**
     * Update the specified FinanceItemcategorySubAssigned in storage.
     * PUT/PATCH /financeItemcategorySubAssigneds/{id}
     *
     * @param  int $id
     * @param UpdateFinanceItemcategorySubAssignedAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFinanceItemcategorySubAssignedAPIRequest $request)
    {
        $input = $request->all();

        /** @var FinanceItemcategorySubAssigned $financeItemcategorySubAssigned */
        $financeItemcategorySubAssigned = $this->financeItemcategorySubAssignedRepository->findWithoutFail($id);

        if (empty($financeItemcategorySubAssigned)) {
            return $this->sendError('Finance Itemcategory Sub Assigned not found');
        }

        $financeItemcategorySubAssigned = $this->financeItemcategorySubAssignedRepository->update($input, $id);

        return $this->sendResponse($financeItemcategorySubAssigned->toArray(), 'FinanceItemcategorySubAssigned updated successfully');
    }

    /**
     * Remove the specified FinanceItemcategorySubAssigned from storage.
     * DELETE /financeItemcategorySubAssigneds/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var FinanceItemcategorySubAssigned $financeItemcategorySubAssigned */
        $financeItemcategorySubAssigned = $this->financeItemcategorySubAssignedRepository->findWithoutFail($id);
        if (empty($financeItemcategorySubAssigned)) {
            return $this->sendError('Finance Itemcategory Sub Assigned not found');
        }
        $masterData = $financeItemcategorySubAssigned->toArray();

        $financeItemcategorySubAssigned->delete();

        $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid'] : 'local';
        $db = isset($input['db']) ? $input['db'] : '';

        $this->auditLog($db, $id,$uuid, "financeitemcategorysubassigned", "Company Assign ".$financeItemcategorySubAssigned->categoryDescription." has been deleted", "D", [], $masterData, $financeItemcategorySubAssigned->itemCategorySubID, 'financeitemcategorysub');

        return $this->sendResponse($id, 'Finance Itemcategory Sub Assigned deleted successfully');
    }
}
