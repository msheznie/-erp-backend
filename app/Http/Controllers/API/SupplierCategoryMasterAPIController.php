<?php
/**
=============================================
-- File Name : SupplierCategoryMasterAPIController.php
-- Project Name : ERP
-- Module Name :  Supplier Category Master
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for Supplier Assigned
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierCategoryMasterAPIRequest;
use App\Http\Requests\API\UpdateSupplierCategoryMasterAPIRequest;
use App\Models\SupplierBusinessCategoryAssign;
use App\Models\SupplierCategoryMaster;
use App\Models\SupplierCategorySub;
use App\Models\SupplierMaster;
use App\Models\YesNoSelection;
use App\Repositories\SupplierCategoryMasterRepository;
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
 * Class SupplierCategoryMasterController
 * @package App\Http\Controllers\API
 */

class SupplierCategoryMasterAPIController extends AppBaseController
{
    /** @var  SupplierCategoryMasterRepository */
    private $supplierCategoryMasterRepository;
    private $userRepository;

    public function __construct(SupplierCategoryMasterRepository $supplierCategoryMasterRepo,UserRepository $userRepo)
    {
        $this->supplierCategoryMasterRepository = $supplierCategoryMasterRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the SupplierCategoryMaster.
     * GET|HEAD /supplierCategoryMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->supplierCategoryMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierCategoryMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierCategoryMasters = $this->supplierCategoryMasterRepository->all();

        return $this->sendResponse($supplierCategoryMasters->toArray(), trans('custom.supplier_category_masters_retrieved_successfully'));
    }

    /**
     * Store a newly created SupplierCategoryMaster in storage.
     * POST /supplierCategoryMasters
     *
     * @param CreateSupplierCategoryMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierCategoryMasterAPIRequest $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'categoryCode' => 'required|unique:suppliercategorymaster',
            'categoryName' => 'required|unique:suppliercategorymaster',
            'categoryDescription' => 'required'
        ],[
            'categoryCode.unique'   => 'Category code already exists',
            'categoryName.unique'   => 'Category name already exists'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422 );
        }

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $empId = $user->employee['empID'];
        $input['createdUserID'] = $empId;
        $input['createdPcID'] = gethostname();
        $input['createdUserGroup'] = $user->employee['userGroupID'];

        $input['isActive'] = is_array($input['isActive']) ? $input['isActive'][0] : $input['isActive'];

        $supplierCategoryMaster = $this->supplierCategoryMasterRepository->create($input);

        return $this->sendResponse($supplierCategoryMaster->toArray(), trans('custom.save', ['attribute' => trans('custom.supplier_business_category')]));
    }

    /**
     * Display the specified SupplierCategoryMaster.
     * GET|HEAD /supplierCategoryMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var SupplierCategoryMaster $supplierCategoryMaster */
        $supplierCategoryMaster = $this->supplierCategoryMasterRepository->find($id);

        if (empty($supplierCategoryMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.supplier_business_category')]));
        }

        return $this->sendResponse($supplierCategoryMaster->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.supplier_business_category')]));
    }

    /**
     * Update the specified SupplierCategoryMaster in storage.
     * PUT/PATCH /supplierCategoryMasters/{id}
     *
     * @param  int $id
     * @param UpdateSupplierCategoryMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierCategoryMasterAPIRequest $request)
    {
        $input = $request->all();

        $supplierCategoryMaster = $this->supplierCategoryMasterRepository->findWithoutFail($id);

        if (empty($supplierCategoryMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.supplier_business_category')]));
        }

        if($supplierCategoryMaster->categoryCode != $request->categoryCode){
            $validator = Validator::make($input, [
                'categoryCode' => 'unique:suppliercategorymaster'
            ],[
                'categoryCode.unique'   => 'Category code already exists'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422 );
            }
        }

        if($supplierCategoryMaster->categoryName != $request->categoryName){
            $validator = Validator::make($input, [
                'categoryName' => 'unique:suppliercategorymaster'
            ],[
                'categoryName.unique'   => 'Category name already exists'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422 );
            }
        }

        $id = Auth::id();
        $input = $request->except(['createdUserID']);

        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['empID'];
        $input['modifiedUser'] = $empId;
        $input['modifiedPc'] = gethostname();

        $input['isActive'] = is_array($input['isActive']) ? $input['isActive'][0] : $input['isActive'];

        $supplierCategoryMaster->update($input);

        return $this->sendResponse($supplierCategoryMaster->refresh()->toArray(), trans('custom.update', ['attribute' => trans('custom.supplier_business_category')]));
    }

    /**
     * Remove the specified SupplierCategoryMaster from storage.
     * DELETE /supplierCategoryMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SupplierCategoryMaster $supplierCategoryMaster */
        $supplierCategoryMaster = $this->supplierCategoryMasterRepository->findWithoutFail($id);

        if (empty($supplierCategoryMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.supplier_business_category')]));
        }

        SupplierCategorySub::where('supMasterCategoryID',$supplierCategoryMaster->supCategoryMasterID)->delete();

        $supplierCategoryMaster->delete();

        return $this->sendResponse($id,trans('custom.delete', ['attribute' => trans('custom.supplier_business_category')]));
    }

    public function destroyCheck($id)
    {
        /** @var SupplierCategoryMaster $supplierCategoryMaster */
        $supplierCategoryMaster = $this->supplierCategoryMasterRepository->findWithoutFail($id);

        if (empty($supplierCategoryMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.supplier_business_category')]));
        }

        $supplierCategoryAssign = SupplierBusinessCategoryAssign::where('supCategoryMasterID', $id)->first();

        if ($supplierCategoryAssign) {
            return $this->sendError(trans('custom.this_category_has_already_been_pulled_to_supplier_'));
        }

        return $this->sendResponse($id,trans('custom.this_category_can_be_delete'));
    }

    public function getAllSupplierBusinessCategories(Request $request){
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $supplierBusinessCategories = SupplierCategoryMaster::select('*')->orderBy('supCategoryMasterID', 'desc');
        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $supplierBusinessCategories = $supplierBusinessCategories->where(function ($query) use ($search) {
                $query->where('categoryCode', 'LIKE', "%{$search}%")
                    ->orWhere('categoryName', 'LIKE', "%{$search}%")
                    ->orWhere('categoryDescription', 'LIKE', "%{$search}%");
            });
        }

        return DataTables::of($supplierBusinessCategories)
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions')
            ->make(true);
    }

    public function getSupplierBusinessCategoryFormData(Request $request)
    {
        $yesNoSelection = YesNoSelection::all();

        $output = array('yesNoSelection' => $yesNoSelection);

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function validateSupplierBusinessCategoryAmend(Request $request)
    {
        $input = $request->all();

        $supplierBusinessCategory = $this->supplierCategoryMasterRepository->find($input['id']);

        if (!$supplierBusinessCategory) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.supplier_business_category')]));
        }

        $errorMessages = null;
        $successMessages = null;
        $amendable = null;

        $supplierMaster = SupplierBusinessCategoryAssign::where('supCategoryMasterID', $input['id'])->first();

        if ($supplierMaster) {
            $errorMessages = "cannot be amended. Since, it has been used in supplier master";
            $amendable = false;
        } else {
            $successMessages = "Use of Supplier business category checking is done in supplier master";
            $amendable = true;
        }

        return $this->sendResponse(['errorMessages' => $errorMessages, 'successMessages' => $successMessages, 'amendable'=> $amendable], "validated successfully");
    }
}
