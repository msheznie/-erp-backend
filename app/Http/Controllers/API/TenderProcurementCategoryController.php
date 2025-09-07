<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Models\ProcumentActivity;
use App\Models\TenderMaster;
use App\Models\TenderProcurementCategory;
use Illuminate\Http\Request;
use App\Repositories\ProcurementCategoryRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Prettus\Validator\Exceptions\ValidatorException;

class TenderProcurementCategoryController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $procurementCategoryRepository;

    public function __construct(ProcurementCategoryRepository $procurementCategoryRepo)
    {
        $this->procurementCategoryRepository = $procurementCategoryRepo;
    }

    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return Response
     * @throws ValidatorException
     */
    public function store(Request $request)
    {
        $level = 0;
        $parent_id = 0;
        $input = $request->all();
        $successMessageContent = 'Procurement Category ';

        if(isset($input['level'])){
            $level = $input['level'];

            if($level == 0){
                $successMessageContent = 'Procurement Category ';
            } elseif ($level == 1) {
                $successMessageContent = 'Procurement sub Category ';
            } elseif ($level == 2){
                $successMessageContent = 'Procurement activity ';
            }
        }

        if(isset($input['parent_id'])){
            $parent_id = $input['parent_id'];
        }

        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'is_active' => 'required|numeric|min:0',
            'description' => 'required',
           // 'description_in_secondary' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if( $level == 0 && isset($input['createNewRecord']) && $input['createNewRecord'] == 0){
            $procurementCatCodeExist = TenderProcurementCategory::withTrashed()
                ->select('id', 'deleted_at')
                ->where('code', '=', $input['code'])
                ->where('level', '=', $level)->first();
        } else {
            $procurementCatCodeExist = TenderProcurementCategory::select('id')
                ->where('code', '=', $input['code'])
                ->where('parent_id', '=', $parent_id)
                ->where('level', '=', $level)->first();
        }

        if (!empty($procurementCatCodeExist)) {
            if(is_null($procurementCatCodeExist['deleted_at'])){
                return $this->sendError('Procurement code \'' . $input['code'] . '\' already exists');
            } else {
                $errorMessage = 'Procurement code \''. $input['code'] . '\'  already exist, Do you really want to restore this?';
                return $this->sendError([$procurementCatCodeExist['id'], $errorMessage], 409);
            }
        }

        if( $level == 0 && isset($input['createNewRecord']) && $input['createNewRecord'] == 0){
            $procurementCatDesExist = TenderProcurementCategory::withTrashed()
                ->select('id', 'deleted_at')
                ->where('description', '=', $input['description'])
                ->where('level', '=', $level)->first();
        } else {
            $procurementCatDesExist = TenderProcurementCategory::select('id')
                ->where('description', '=', $input['description'])
                ->where('parent_id', '=', $parent_id)
                ->where('level', '=', $level)->first();
        }

        if (!empty($procurementCatDesExist)) {
            if(is_null($procurementCatDesExist['deleted_at'])){
                return $this->sendError('Procurement category description \'' . $input['description'] . '\' already exists');
            } else {
                $errorMessage = 'Procurement category description \''. $input['description'] . '\' already exist, Do you really want to restore this?';
                return $this->sendError([$procurementCatDesExist['id'], $errorMessage], 409);
            }
        }

        $input['created_pc'] = gethostname();
        $input['created_by'] = Helper::getEmployeeSystemID();
        $input['parent_id'] = 0;
        $input['level'] = $level;
        $input['parent_id'] = $parent_id;
        $procurementCategories = $this->procurementCategoryRepository->create($input);

        return $this->sendResponse($procurementCategories->toArray(), $successMessageContent . ' saved successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        $procurementCategory = TenderProcurementCategory::find($id);

        if (empty($procurementCategory)) {
            return $this->sendError(trans('custom.procurement_category_not_found'));
        }

        return $this->sendResponse($procurementCategory->toArray(), trans('custom.procurement_category_retrieved_successfully'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        $level = 0;
        $parent_id = 0;
        $input = $request->all();
        if(isset($input['restore']) && $input['restore'] == true){
            return $this->restoreDeletedCategory($id, $request);
        }

        if(isset($input['level'])){
            $level = $input['level'];

            if($level == 0){
                $successMessageContent = 'Procurement Category ';
            } elseif ($level == 1) {
                $successMessageContent = 'Procurement sub Category ';
            } elseif ($level == 2){
                $successMessageContent = 'Procurement activity ';
            }
        }

        if(isset($input['parent_id'])){
            $parent_id = $input['parent_id'];
        }

        $procurementCategory = TenderProcurementCategory::find($id);

        if (empty($procurementCategory)) {
            return $this->sendError(trans('custom.procurement_category_not_found'));
        }

        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'is_active' => 'required|numeric|min:0',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if( $level == 0 && isset($input['createNewRecord']) &&  $input['createNewRecord'] == 0){
            $procurementCodeExist = TenderProcurementCategory::withTrashed()
                ->select('id', 'deleted_at')
                ->where('id', '!=', $id)
                ->where('code', '=', $input['code'])
                ->where('level', '=', $level)->first();
        } else {
            $procurementCodeExist = TenderProcurementCategory::select('id')
                ->where('id', '!=', $id)
                ->where('code', '=', $input['code'])
                ->where('parent_id', '=', $parent_id)
                ->where('level', '=', $level)->first();
        }

        if (!empty($procurementCodeExist)) {
            if(is_null($procurementCodeExist['deleted_at'])) {
                return $this->sendError('Procurement code \'' . $input['code'] . '\' already exists');
            } else {
                $errorMessage = 'Procurement code '. $input['code'] . '  already exist, Do you really want to restore this?';
                return $this->sendError([$procurementCodeExist['id'], $errorMessage], 409);
            }
        }

        if( $level == 0 && isset($input['createNewRecord']) &&  $input['createNewRecord'] == 0){
            $procurementDesExist = TenderProcurementCategory::withTrashed()
                ->select('id', 'deleted_at')
                ->where('id', '!=', $id)
                ->where('description', '=', $input['description'])
                ->where('level', '=', $level)->first();
        } else {
            $procurementDesExist = TenderProcurementCategory::select('id')
                ->where('id', '!=', $id)
                ->where('description', '=', $input['description'])
                ->where('parent_id', '=', $parent_id)
                ->where('level', '=', $level)->first();
        }

        if(!empty($procurementDesExist)) {
            if(is_null($procurementCodeExist['deleted_at'])) {
                return $this->sendError('Procurement category description \'' . $input['description'] . '\' already exists');
            } else {
                $errorMessage = 'Procurement category description '. $input['description'] . ' already exist, Do you really want to restore this?';
                return $this->sendError([$procurementCodeExist['id'], $errorMessage], 409);
            }
        }

        // Used for tender creation
        $editCondition = $this->checkEditCondition($id, $request, $level);
        if(isset($editCondition) && !$editCondition){
            return $this->sendError($successMessageContent .' is already used in tender creation');
        }

        $input['updated_pc'] = gethostname();
        $input['updated_by'] = Helper::getEmployeeSystemID();
        $input['parent_id'] = 0;
        $input['level'] = $level;
        $input['parent_id'] = $parent_id;

        $procurementCategory = TenderProcurementCategory::where('id', $id)->update($input);

        return $this->sendResponse($procurementCategory, $successMessageContent.' updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return Response
     */
    public function destroy($id)
    {
        $tenderProcurementCategory = TenderProcurementCategory::find($id);

        if (empty($tenderProcurementCategory)) {
            return $this->sendError(trans('custom.procurement_category_not_found'));
        }

        $level = $tenderProcurementCategory['level'];

        if($level == 0){
            $successMessageContent = 'Procurement Category ';
        } elseif ($level == 1) {
            $successMessageContent = 'Procurement sub Category ';

            $tenderMasterNotConfirmedCount = TenderMaster::where('procument_sub_cat_id', $tenderProcurementCategory['id'])->count();

            if($tenderMasterNotConfirmedCount > 0){
                return $this->sendError($successMessageContent. ' is already used');
            }
        } elseif ($level == 2){
            $successMessageContent = 'Procurement activity ';
        }

        $categoryHasTenders = TenderProcurementCategory::has('tenderMaster')->where('id', $id)->get();
        if(sizeof($categoryHasTenders) != 0){
            return $this->sendError($successMessageContent. ' is already used');
        }

        $categoryHasActivity = TenderProcurementCategory::has('procumentActivity')->where('id', $id)->get();
        if(sizeof($categoryHasActivity) != 0){
            return $this->sendError($successMessageContent . ' is already used');
        }

        $input['deleted_by'] = Helper::getEmployeeSystemID();
        $procurementCategoryDeleted = TenderProcurementCategory::where('id', $id)->update($input);

        if($procurementCategoryDeleted){
            $tenderProcurementCategory->delete();
        }

        return $this->sendResponse($id, $successMessageContent . ' deleted successfully');
    }

    public function getAllProcurementCategory(Request $request)
    {
        $level = 0;
        $input = $request->all();

        if(isset($input['extra']['level'])){
            $level = $input['extra']['level'];
        }

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $procurementCategories = TenderProcurementCategory::where('level', $level);

        if(isset($input['id']) && $input['id'] !== 0) {
            $procurementCategories->where('parent_id', $input['id']);
        }

        $procurementCategories->orderBy('id', $sort);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $procurementCategories = $procurementCategories->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%");
                $query->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($procurementCategories)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->make(true);
    }

    private function restoreDeletedCategory($id, Request $request)
    {
        $level = 0;
        $input = $request->all();

        if(isset($input['level'])){
            $level = $input['level'];
        }

        $procurementCatCodeExist = TenderProcurementCategory::select('id')
            ->where('code', '=', $input['code'])
            ->where('level', '=', $level)->first();

        if (!empty($procurementCatCodeExist)) {
            return $this->sendError('Procurement code ' . $input['code'] . ' already exists');
        }

        $procurementCatDesExist = TenderProcurementCategory::select('id')
            ->where('description', '=', $input['description'])
            ->where('level', '=', $level)->first();

        if (!empty($procurementCatDesExist)) {
            return $this->sendError('Procurement category description \'' . $input['description'] . '\' already exists');
        }
        
        $procurementCategory = TenderProcurementCategory::withTrashed()->find($id)->restore();

        if($procurementCategory) {
            TenderProcurementCategory::where('id', $id)->update(['is_active' => $request->input('is_active')]);
        }


        return $this->sendResponse($procurementCategory, trans('custom.procurement_category_restored_successfully'));
    }

    private function checkEditCondition($id, $request, $level){

        $input = $request->all();
        $procurementCategory = TenderProcurementCategory::find($id);

        // Check Description
        $description = $input['description'];
        $p_description = $procurementCategory['description'];
        if($description != $p_description){
            $isDescriptionChanged =  'Yes';
        } else {
            $isDescriptionChanged =  'No';
        }

        // Check Code
        $code = $input['code'];
        $p_code = $procurementCategory['code'];
        if($code != $p_code){
            $isCodeChanged =  'Yes';
        } else {
            $isCodeChanged =  'No';
        }

        // Check Status
        $is_active = $input['is_active'];
        if(isset($is_active[0])){
            $is_active = $is_active[0];
        }

        $p_is_active = $procurementCategory['is_active'];
        if($is_active === $p_is_active){
            $isActiveChanged =  'No';
        } else {
            $isActiveChanged =  'Yes';
        }

        if($level == 0 ){
            return $this->validateLevelOneEdit($isDescriptionChanged, $isCodeChanged, $isActiveChanged, $id);
        } elseif ($level == 1){
            return $this->validateLevelTwoEdit($isDescriptionChanged, $isCodeChanged, $isActiveChanged, $id);
        } elseif ($level == 2){
            return $this->validateLevelThreeEdit($isDescriptionChanged, $isCodeChanged, $isActiveChanged, $id);
        }
    }

    public function validateLevelOneEdit($isDescriptionChanged, $isCodeChanged, $isActiveChanged, $id)
    {
        $tenderMasterNotConfirmedCount = TenderMaster::where('procument_cat_id', $id)
            ->where('confirmed_yn', 0)->count();
        if($tenderMasterNotConfirmedCount > 0){
            $allowToEdit = false;
            return $allowToEdit;
        }

        $tenderMasterNotApproveCount = TenderMaster::where('procument_cat_id', $id)
            ->where('confirmed_yn', 1)
            ->where('approved', -1)
            ->count();

        $tenderMasterRecordCount = TenderMaster::where('procument_cat_id', $id)
            ->count();
        if($tenderMasterRecordCount > 0 && ($tenderMasterNotApproveCount == $tenderMasterRecordCount) && $isCodeChanged == 'No'){
            $allowToEdit = true;
            return $allowToEdit;
        }

        if($tenderMasterRecordCount > 0 && ($tenderMasterNotApproveCount == $tenderMasterRecordCount) && $isCodeChanged == 'Yes'){
            $allowToEdit = false;
            return $allowToEdit;
        }

        $tenderMasterConfirmedNotApproveCount = TenderMaster::where('procument_cat_id', $id)
            ->where('confirmed_yn', 1)
            ->where('approved', '!=', -1)
            ->count();
        if($isDescriptionChanged === 'Yes' && $isCodeChanged == 'No' && $isActiveChanged == 'No' &&  $tenderMasterConfirmedNotApproveCount > 0){
            $allowToEdit = true;
            return $allowToEdit;
        } elseif ($isActiveChanged == 'Yes' &&  $tenderMasterConfirmedNotApproveCount > 0){
            $allowToEdit = false;
            return $allowToEdit;
        } elseif ($isCodeChanged == 'Yes' &&  $tenderMasterConfirmedNotApproveCount > 0){
            $allowToEdit = false;
            return $allowToEdit;
        }
    }

    public function validateLevelTwoEdit($isDescriptionChanged, $isCodeChanged, $isActiveChanged, $id)
    {
        $tenderMasterNotConfirmedCount = TenderMaster::where('procument_sub_cat_id', $id)
            ->where('confirmed_yn', 0)->count();
        if($tenderMasterNotConfirmedCount > 0){
            $allowToEdit = false;
            return $allowToEdit;
        }

        $tenderMasterNotApproveCount = TenderMaster::where('procument_sub_cat_id', $id)
            ->where('confirmed_yn', 1)
            ->where('approved', -1)
            ->count();

        $tenderMasterRecordCount = TenderMaster::where('procument_sub_cat_id', $id)
            ->count();
        if($tenderMasterRecordCount > 0 && ($tenderMasterNotApproveCount == $tenderMasterRecordCount) && $isCodeChanged == 'No'){
            $allowToEdit = true;
            return $allowToEdit;
        }

        if($tenderMasterRecordCount > 0 && ($tenderMasterNotApproveCount == $tenderMasterRecordCount) && $isCodeChanged == 'Yes'){
            $allowToEdit = false;
            return $allowToEdit;
        }

        $tenderMasterConfirmedNotApproveCount = TenderMaster::where('procument_sub_cat_id', $id)
            ->where('confirmed_yn', 1)
            ->where('approved', '!=', -1)
            ->count();
        if($isDescriptionChanged === 'Yes' && $isCodeChanged == 'No' && $isActiveChanged == 'No' &&  $tenderMasterConfirmedNotApproveCount > 0){
            $allowToEdit = true;
            return $allowToEdit;
        } elseif ($isActiveChanged == 'Yes' &&  $tenderMasterConfirmedNotApproveCount > 0){
            $allowToEdit = false;
            return $allowToEdit;
        } elseif ($isCodeChanged == 'Yes' &&  $tenderMasterConfirmedNotApproveCount > 0){
            $allowToEdit = false;
            return $allowToEdit;
        }
    }

    public function validateLevelThreeEdit($isDescriptionChanged, $isCodeChanged, $isActiveChanged, $id)
    {
        $procurementCategoryActivity = ProcumentActivity::with('tender_procurement_category')->where('category_id', $id)->first();
        if(empty($procurementCategoryActivity)){
            $allowToEdit = true;
            return $allowToEdit;
        } else{
            $tenderMasterNotConfirmedCount = ProcumentActivity::with(['tender_master'])
                ->where('category_id', $id)
                ->whereHas('tender_master', function ($q){
                    $q->where('confirmed_yn', 0);
                })
                ->count();

            if($tenderMasterNotConfirmedCount > 0){
                $allowToEdit = false;
                return $allowToEdit;
            }

            $tenderMasterApproveCount = ProcumentActivity::with(['tender_master'])
                ->where('category_id', $id)
                ->whereHas('tender_master', function ($q){
                    $q->where('confirmed_yn', 1);
                    $q->where('approved', -1);
                })
                ->count();

            $tenderMasterRecordCount = ProcumentActivity::with(['tender_master'])
                ->where('category_id', $id)
                ->count();

            if(($tenderMasterApproveCount == $tenderMasterRecordCount) && $isCodeChanged == 'No'){
                $allowToEdit = true;
                return $allowToEdit;
            }

            if(($tenderMasterApproveCount == $tenderMasterRecordCount) && $isCodeChanged == 'Yes'){
                $allowToEdit = false;
                return $allowToEdit;
            }

            $tenderMasterConfirmedNotApproveCount = ProcumentActivity::with(['tender_master'])
                ->where('category_id', $id)
                ->whereHas('tender_master', function ($q){
                    $q->where('confirmed_yn', 1);
                    $q->where('approved', '!=', -1);
                })
                ->count();

            if($isDescriptionChanged === 'Yes' && $isCodeChanged == 'No' && $isActiveChanged == 'No' && $tenderMasterConfirmedNotApproveCount > 0){
                $allowToEdit = true;
                return $allowToEdit;
            } elseif ($isActiveChanged == 'Yes' &&  $tenderMasterConfirmedNotApproveCount > 0){
                $allowToEdit = false;
                return $allowToEdit;
            } elseif ($isCodeChanged == 'Yes' &&  $tenderMasterConfirmedNotApproveCount > 0){
                $allowToEdit = false;
                return $allowToEdit;
            }
        }
    }
}

