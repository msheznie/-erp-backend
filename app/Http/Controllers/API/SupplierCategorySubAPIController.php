<?php
/**
=============================================
-- File Name : SupplierCategoryMasterAPIController.php
-- Project Name : ERP
-- Module Name :  Supplier Category Master
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for Supplier Category Master
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierCategorySubAPIRequest;
use App\Http\Requests\API\UpdateSupplierCategorySubAPIRequest;
use App\Models\SupplierCategoryMaster;
use App\Models\SupplierCategorySub;
use App\Models\SupplierMaster;
use App\Models\SupplierSubCategoryAssign;
use App\Models\YesNoSelection;
use App\Repositories\SupplierCategorySubRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Yajra\DataTables\DataTables;

/**
 * Class SupplierCategorySubController
 * @package App\Http\Controllers\API
 */

class SupplierCategorySubAPIController extends AppBaseController
{
    /** @var  SupplierCategorySubRepository */
    private $supplierCategorySubRepository;
    private $userRepository;

    public function __construct(SupplierCategorySubRepository $supplierCategorySubRepo,UserRepository $userRepo)
    {
        $this->supplierCategorySubRepository = $supplierCategorySubRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the SupplierCategorySub.
     * GET|HEAD /supplierCategorySubs
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->supplierCategorySubRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierCategorySubRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierCategorySubs = $this->supplierCategorySubRepository->all();

        return $this->sendResponse($supplierCategorySubs->toArray(), trans('custom.supplier_category_subs_retrieved_successfully'));
    }

    public function getSubCategoriesByMasterCategory(Request $request){
        $businessCategoryID = $request['businessCategoryID'];
        $businessSubCategories = SupplierCategorySub::where('supMasterCategoryID',$businessCategoryID)->where('isActive',1)->get();
        return $this->sendResponse($businessSubCategories, trans('custom.sub_category_retrieved_successfully'));
    }

    /**
     * Store a newly created SupplierCategorySub in storage.
     * POST /supplierCategorySubs
     *
     * @param CreateSupplierCategorySubAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierCategorySubAPIRequest $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'subCategoryCode' => 'required',
            'categoryName' => 'required',
            'categoryDescription' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422 );
        }

        $subCategoriesByMaster = SupplierCategorySub::where('supMasterCategoryID',$input['supMasterCategoryID'])->where('subCategoryCode',$input['subCategoryCode'])->first();
        if($subCategoriesByMaster){
            return $this->sendError('Sub Category Code ' . trans('custom.already_exists'),422);
        }

        $subCategoriesByMaster = SupplierCategorySub::where('supMasterCategoryID',$input['supMasterCategoryID'])->where('categoryName',$input['categoryName'])->first();
        if($subCategoriesByMaster){
            return $this->sendError('Sub Category Name ' . trans('custom.already_exists'),422);
        }

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $empId = $user->employee['empID'];
        $input['createdUserID'] = $empId;
        $input['createdPcID'] = gethostname();
        $input['createdUserGroup'] = $user->employee['userGroupID'];

        $input['isActive'] = is_array($input['isActive']) ? $input['isActive'][0] : $input['isActive'];

        $supplierSubCategoryMaster = $this->supplierCategorySubRepository->create($input);

        return $this->sendResponse($supplierSubCategoryMaster->toArray(), trans('custom.save', ['attribute' => trans('custom.supplier_business_sub_category')]));
    }

    /**
     * Display the specified SupplierCategorySub.
     * GET|HEAD /supplierCategorySubs/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var SupplierCategorySub $supplierCategorySub */
        $supplierCategorySub = $this->supplierCategorySubRepository->find($id);

        if (empty($supplierCategorySub)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.supplier_business_sub_category')]));
        }

        return $this->sendResponse($supplierCategorySub->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.supplier_business_sub_category')]));
    }

    /**
     * Update the specified SupplierCategorySub in storage.
     * PUT/PATCH /supplierCategorySubs/{id}
     *
     * @param  int $id
     * @param UpdateSupplierCategorySubAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierCategorySubAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierCategorySub $supplierCategorySub */
        $supplierCategorySub = $this->supplierCategorySubRepository->findWithoutFail($id);

        if (empty($supplierCategorySub)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.supplier_business_sub_category')]));
        }

        if($supplierCategorySub->subCategoryCode != $request->subCategoryCode){
            $subCategoriesByMaster = SupplierCategorySub::where('supMasterCategoryID',$input['supMasterCategoryID'])->where('subCategoryCode',$input['subCategoryCode'])->first();
            if($subCategoriesByMaster){
                return $this->sendError('Sub Category Code ' . trans('custom.already_exists'),422);
            }
        }

        if($supplierCategorySub->categoryName != $request->categoryName){
            $subCategoriesByMaster = SupplierCategorySub::where('supMasterCategoryID',$input['supMasterCategoryID'])->where('categoryName',$input['categoryName'])->first();
            if($subCategoriesByMaster){
                return $this->sendError('Sub Category Name ' . trans('custom.already_exists'),422);
            }
        }

        $id = Auth::id();
        $input = $request->except(['createdUserID']);

        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['empID'];
        $input['modifiedUser'] = $empId;
        $input['modifiedPc'] = gethostname();

        $input['isActive'] = is_array($input['isActive']) ? $input['isActive'][0] : $input['isActive'];

        $supplierCategorySub->update($input);

        return $this->sendResponse($supplierCategorySub->refresh()->toArray(), trans('custom.update', ['attribute' => trans('custom.supplier_business_sub_category')]));
    }

    /**
     * Remove the specified SupplierCategorySub from storage.
     * DELETE /supplierCategorySubs/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SupplierCategorySub $supplierCategorySub */
        $supplierCategorySub = $this->supplierCategorySubRepository->findWithoutFail($id);

        if (empty($supplierCategorySub)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.supplier_business_sub_category')]));
        }

        $supplierCategorySub->delete();

        return $this->sendResponse($id,trans('custom.delete', ['attribute' => trans('custom.supplier_business_sub_category')]));
    }

    public function destroyCheck($id)
    {
        /** @var SupplierCategoryMaster $supplierCategoryMaster */
        $supplierCategoryMaster = $this->supplierCategorySubRepository->findWithoutFail($id);

        if (empty($supplierCategoryMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.supplier_business_category')]));
        }

        $subCategoryAssign= SupplierSubCategoryAssign::where('supSubCategoryID', $id)->first();

        if ($subCategoryAssign) {
            return $this->sendError(trans('custom.this_category_has_already_been_pulled_to_supplier_'));
        }

        return $this->sendResponse($id,trans('custom.this_category_can_be_delete'));
    }

    public function getAllSupplierBusinessSubCategories(Request $request){
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $supplierBusinessSubCategories = SupplierCategorySub::select('*')->orderBy('supCategorySubID', 'desc');
        $supplierBusinessSubCategories = $supplierBusinessSubCategories->where('supMasterCategoryID',$input['supplierBusinessCategoryID']);
        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $supplierBusinessSubCategories = $supplierBusinessSubCategories->where(function ($query) use ($search) {
                $query->where('subCategoryCode', 'LIKE', "%{$search}%")
                    ->orWhere('categoryName', 'LIKE', "%{$search}%")
                    ->orWhere('categoryDescription', 'LIKE', "%{$search}%");
            });
        }

        return DataTables::of($supplierBusinessSubCategories)
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions')
            ->make(true);
    }

    public function getSupplierBusinessSubCategoryFormData(Request $request)
    {
        $yesNoSelection = YesNoSelection::all();

        $output = array('yesNoSelection' => $yesNoSelection);

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function validateSupplierBusinessSubCategoryAmend(Request $request)
    {
        $input = $request->all();

        $supplierBusinessSubCategory = $this->supplierCategorySubRepository->find($input['id']);

        if (!$supplierBusinessSubCategory) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.supplier_business_sub_category')]));
        }

        $errorMessages = null;
        $successMessages = null;
        $amendable = null;

        $subCategoryAssign = SupplierSubCategoryAssign::where('supSubCategoryID', $input['id'])->first();

        if ($subCategoryAssign) {
            $errorMessages = "cannot be amended. Since, it has been used in supplier master";
            $amendable = false;
        } else {
            $successMessages = "Use of Supplier business category checking is done in supplier master";
            $amendable = true;
        }

        return $this->sendResponse(['errorMessages' => $errorMessages, 'successMessages' => $successMessages, 'amendable'=> $amendable], "validated successfully");
    }


    public function getSubCategoriesByMultipleMasterCategory(Request $request){
        $input = $request->all();
        $businessSubCategories = SupplierCategorySub::whereIn('supMasterCategoryID',$input)->where('isActive',1)->get();
        return $this->sendResponse($businessSubCategories, trans('custom.sub_category_retrieved_successfully'));
    }


    public function getSupplierBusinessSubCategoriesByCategory(Request $request) {
        $categories = $request->input('categories');

        if(empty($categories)) {
            $this->sendError("Categories not found");
        }

        $categories = collect($categories)->pluck('id')->toArray();

        $subCategories = SupplierCategorySub::select(['categoryName','supCategorySubID','subCategoryCode'])->whereIn('supMasterCategoryID',$categories)->isActive()->get()->toArray();

        return $this->sendResponse($subCategories ?? [],'Data reterived');
    }
}
