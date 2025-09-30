<?php
/**
=============================================
-- File Name : FinanceItemCategoryMasterAPIController.php
-- Project Name : ERP
-- Module Name :  Finance Item Category Master
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for Finance Item Category Master
-- REVISION HISTORY
-- Date: 14-March 2018 By: Fayas Description: Added new functions named as allItemFinanceCategories(),allItemFinanceSubCategoriesByMainCategory(),
getSubCategoryFormData(),
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFinanceItemCategoryMasterAPIRequest;
use App\Http\Requests\API\UpdateFinanceItemCategoryMasterAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\ItemCategoryTypeMaster;
use App\Models\ItemMaster;
use App\Models\YesNoSelection;
use App\Models\FinanceItemCategoryMaster;
use App\Models\FinanceItemCategorySub;
use App\Repositories\FinanceItemCategoryMasterRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\ErpAttributes;
use App\Models\ErpAttributesDropdown;
use App\Models\ErpAttributesFieldType;
use Error;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;
use App\Traits\AuditLogsTrait;

/**
 * Class FinanceItemCategoryMasterController
 * @package App\Http\Controllers\API
 */

class FinanceItemCategoryMasterAPIController extends AppBaseController
{
    /** @var  FinanceItemCategoryMasterRepository */
    private $financeItemCategoryMasterRepository;
    use AuditLogsTrait;
    
    public function __construct(FinanceItemCategoryMasterRepository $financeItemCategoryMasterRepo)
    {
        $this->financeItemCategoryMasterRepository = $financeItemCategoryMasterRepo;
    }

    /**
     * Display a listing of the FinanceItemCategoryMaster.
     * GET|HEAD /financeItemCategoryMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->financeItemCategoryMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->financeItemCategoryMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $financeItemCategoryMasters = $this->financeItemCategoryMasterRepository->all();

        return $this->sendResponse($financeItemCategoryMasters->toArray(), trans('custom.finance_item_category_masters_retrieved_successful'));
    }

    /**
     * Display a listing of the FinanceItemCategoryMaster.
     * POST /allItemFinanceCategories
     *
     * @param Request $request
     * @return Response
     */
    public function allItemFinanceCategories(Request $request){

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $financeItemCategoryMasters = FinanceItemCategoryMaster::withCount(['item_sub_category as expiry_data_count' => function($query) {
                                                                            $query->where('expiryYN', 1);
                                                                        }])
                                                                        ->withCount(['item_sub_category as attributes_data_count' => function($query) {
                                                                            $query->where('attributesYN', 1);
                                                                        }]);

        $search = $request->input('search.value');
        if($search){
            $financeItemCategoryMasters =   $financeItemCategoryMasters->where('categoryDescription','LIKE',"%{$search}%")
                ->orWhere('itemCodeDef', 'LIKE', "%{$search}%");
        }

        return \DataTables::eloquent($financeItemCategoryMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('itemCategoryID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    /**
     * Display a listing of the Finance Item Category Sub by Category Master .
     * GET /allItemFinanceSubCategoriesByMainCategory
     *
     * @param Request $request
     * @return Response
     */

    public function allItemFinanceSubCategoriesByMainCategory(Request $request){

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $financeItemCategorySub = FinanceItemCategorySub::where('itemCategoryID',$request->get('itemCategoryID'))
                                                         ->with(['finance_item_category_master','finance_gl_code_bs','finance_gl_code_pl','finance_gl_code_revenue','cogs_gl_code_pl','finance_item_category_type'])
                                                         ->select('financeitemcategorysub.*');

        $search = $request->input('search.value');
        if($search){
            $financeItemCategorySub = $financeItemCategorySub->where(function ($query) use($search){
                $query->where('categoryDescription','LIKE',"%{$search}%")
                ->orWhere('financeGLcodePL','LIKE',"%{$search}%")
                ->orWhere('financeGLcodebBS','LIKE',"%{$search}%")
                ->orWhere('financeGLcodeRevenue','LIKE',"%{$search}%")
                ->orWhereHas('finance_gl_code_bs', function($q) use($search){
                    $q->where('AccountDescription','LIKE',"%{$search}%");
                })->orWhereHas('finance_gl_code_pl', function($q) use($search){
                        $q->where('AccountDescription','LIKE',"%{$search}%");
                    })->orWhereHas('finance_gl_code_revenue', function($q) use($search){
                        $q->where('AccountDescription','LIKE',"%{$search}%");
                    });
            });
        }

        return \DataTables::eloquent($financeItemCategorySub)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('itemCategorySubID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    /**
     * get form data for sub  category .
     * GET /getSubCategoryFormData
     *
     * @param Request $request
     * @return Response
     */
    public function getSubCategoryFormData(Request $request){

        /** all main categories*/
        $mainCategories = FinanceItemCategoryMaster::all();

        /** all chart of Account */
        $chartOfAccount = ChartOfAccount::all();

        /** expense chart of accounts */
        $expenseChartOfAccount = ChartOfAccount::whereIn('controlAccountsSystemID',[2,3,4])->where('isActive',1)->where('isApproved',1)->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        $fieldTypes = ErpAttributesFieldType::all();

        $itemCategoryTypes = ItemCategoryTypeMaster::all();

        $output = array('mainCategories' => $mainCategories,
            'chartOfAccount' => $chartOfAccount,
            'expenseChartOfAccount' => $expenseChartOfAccount,
            'yesNoSelection' => $yesNoSelection,
            'fieldTypes' => $fieldTypes,
            'itemCategoryTypes' => $itemCategoryTypes
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function getAttributesData(Request $request){

        $itemCategorySubID = $request[0];
        $attributes = ErpAttributes::with('field_type')->where('document_master_id',$itemCategorySubID)->get();

        return $this->sendResponse($attributes, trans('custom.record_retrieved_successfully_1'));
    }

    public function getAssetCostAttributesData(Request $request){
        $input = $request->all();

        $attributes = ErpAttributes::with('field_type')->where('document_id','ASSETCOST')->where('document_master_id', $input['document_master_id']);

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $attributes = $attributes->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($attributes)
                ->order(function ($query) use ($input) {
                    if (request()->has('order')) {
                        if ($input['order'][0]['column'] == 0) {
                            $query->orderBy('id', $input['order'][0]['dir']);
                        }
                    }
                })
                ->addIndexColumn()
                ->with('orderCondition', $sort)
                ->addColumn('Actions', 'Actions', "Actions")
                ->make(true);
    }


    public function getAttributesDataFormData(Request $request){

        $fieldTypes = ErpAttributesFieldType::all();

        $output = array(
            'fieldTypes' => $fieldTypes,
        );
        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function getDropdownValues(Request $request){

        $itemCategorySubID = $request['itemCategorySubID'];

        $dropDownvalues = ErpAttributesDropdown::where('attributes_id',$itemCategorySubID)->get();


         return $this->sendResponse($dropDownvalues, trans('custom.record_retrieved_successfully_1'));
    }

    public function addItemAttributes(Request $request){
            DB::beginTransaction();
            try {
                $input= $request->all();
                

                $descriptionValidate = ErpAttributes::where('description', $input['description'])
                                                    ->where('document_master_id', $input['document_master_id'])->get();
                if (count($descriptionValidate) > 0){
                    return $this->sendError(trans('custom.description_already_exists_1'));
                }

                $masterData = [
                    'description' => $input['description'],
                    'is_mendatory' => isset($input['is_mendatory']) ? $input['is_mendatory'] : false,
                    'document_id' => $input['document_id'],
                    'document_master_id' => $input['document_master_id'],
                    'field_type_id' => $input['field_type_id']
                ];
                $attributes = ErpAttributes::create($masterData);


                $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid'] : 'local';
                $db = isset($input['db']) ? $input['db'] : '';

                if ($input['document_id'] == "SUBCAT") {
                    $this->auditLog($db, $attributes['id'],$uuid, "erp_attributes", "Attribute ".$input['description']." has created", "C", $masterData, [], $input['document_master_id'], 'financeitemcategorysub');
                }

            DB::commit();
            return $this->sendResponse([], trans('custom.attributes_created_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * Store a newly created FinanceItemCategoryMaster in storage.
     * POST /financeItemCategoryMasters
     *
     * @param CreateFinanceItemCategoryMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateFinanceItemCategoryMasterAPIRequest $request)
    {
        $input = $request->all();

        $financeItemCategoryMasters = $this->financeItemCategoryMasterRepository->create($input);

        return $this->sendResponse($financeItemCategoryMasters->toArray(), trans('custom.finance_item_category_master_saved_successfully'));
    }

    /**
     * Display the specified FinanceItemCategoryMaster.
     * GET|HEAD /financeItemCategoryMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var FinanceItemCategoryMaster $financeItemCategoryMaster */
        $financeItemCategoryMaster = $this->financeItemCategoryMasterRepository->findWithoutFail($id);

        if (empty($financeItemCategoryMaster)) {
            return $this->sendError(trans('custom.finance_item_category_master_not_found'));
        }

        return $this->sendResponse($financeItemCategoryMaster->toArray(), trans('custom.finance_item_category_master_retrieved_successfull'));
    }

    /**
     * Update the specified FinanceItemCategoryMaster in storage.
     * PUT/PATCH /financeItemCategoryMasters/{id}
     *
     * @param  int $id
     * @param UpdateFinanceItemCategoryMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFinanceItemCategoryMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var FinanceItemCategoryMaster $financeItemCategoryMaster */
        $financeItemCategoryMaster = $this->financeItemCategoryMasterRepository->findWithoutFail($id);

        if (empty($financeItemCategoryMaster)) {
            return $this->sendError(trans('custom.finance_item_category_master_not_found'));
        }

        $financeItemCategoryMaster = $this->financeItemCategoryMasterRepository->update($input, $id);

        return $this->sendResponse($financeItemCategoryMaster->toArray(), trans('custom.financeitemcategorymaster_updated_successfully'));
    }

    /**
     * Remove the specified FinanceItemCategoryMaster from storage.
     * DELETE /financeItemCategoryMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var FinanceItemCategoryMaster $financeItemCategoryMaster */
        $financeItemCategoryMaster = $this->financeItemCategoryMasterRepository->findWithoutFail($id);

        if (empty($financeItemCategoryMaster)) {
            return $this->sendError(trans('custom.finance_item_category_master_not_found'));
        }

        $financeItemCategoryMaster->delete();

        return $this->sendResponse($id, trans('custom.finance_item_category_master_deleted_successfully'));
    }

    public function getFinanceItemCategoryMasterExpiryStatus(Request $request){
        $itemCategoryID = $request->all();

        $expiryStatus = FinanceItemCategoryMaster::select('exipryYN')->where('itemCategoryID',$itemCategoryID)->first();

        return $this->sendResponse($expiryStatus, trans('custom.record_retrieved_successfully_1'));
    }

    public function getFinanceItemCategoryMasterAttributesStatus(Request $request){
        $itemCategoryID = $request->all();

        return$expiryStatus = FinanceItemCategoryMaster::select('attributesYN')->where('itemCategoryID',$itemCategoryID)->first();

        return $this->sendResponse($expiryStatus, trans('custom.record_retrieved_successfully_1'));
    }

    public function usedFinanceSubCatByMainCat(Request $request): JsonResponse {
        $ids = (new ItemMaster)->usedFinanceSubCategory();

        $results = FinanceItemCategorySub::where('itemCategoryID',$request->get('itemCategoryID'))
            ->whereIn('itemCategorySubID',$ids)
            ->with([
                'finance_item_category_master',
                'finance_gl_code_bs',
                'finance_gl_code_pl',
                'finance_gl_code_revenue',
                'cogs_gl_code_pl',
                'finance_item_category_type'])
            ->select('financeitemcategorysub.*')->get();

        return response()->json($results);
    }
}
