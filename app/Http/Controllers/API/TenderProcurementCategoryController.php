<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Scopes\ActiveScope;
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

        if(isset($input['level'])){
            $level = $input['level'];
        }

        if(isset($input['parent_id'])){
            $parent_id = $input['parent_id'];
        }

        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'is_active' => 'required|numeric|min:0',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if( $level == 0 ){
            $procurementCatCodeExist = TenderProcurementCategory::withTrashed()
                ->select('id', 'deleted_at')
                ->where('code', '=', $input['code'])
                ->where('level', '=', $level)->first();
        } else {
            $procurementCatCodeExist = TenderProcurementCategory::select('id')
                ->where('code', '=', $input['code'])
                ->where('level', '=', $level)->first();
        }

        if (!empty($procurementCatCodeExist)) {
            if(is_null($procurementCatCodeExist['deleted_at'])){
                return $this->sendError('Procurement code ' . $input['code'] . ' already exists');
            } else {
                $errorMessage = 'Procurement code '. $input['code'] . '  already exist, Do you really want to restore this?';
                return $this->sendError([$procurementCatCodeExist['id'], $errorMessage], 409);
            }
        }

        if( $level == 0 ){
            $procurementCatDesExist = TenderProcurementCategory::withTrashed()
                ->select('id', 'deleted_at')
                ->where('description', '=', $input['description'])
                ->where('level', '=', $level)->first();
        } else {
            $procurementCatDesExist = TenderProcurementCategory::select('id')
                ->where('description', '=', $input['description'])
                ->where('level', '=', $level)->first();
        }

        if (!empty($procurementCatDesExist)) {
            if(is_null($procurementCatDesExist['deleted_at'])){
                return $this->sendError('Procurement category description ' . $input['description'] . ' already exists');
            } else {
                $errorMessage = 'Procurement category description '. $input['description'] . ' already exist, Do you really want to restore this?';
                return $this->sendError([$procurementCatDesExist['id'], $errorMessage], 409);
            }
        }

        $input['created_pc'] = gethostname();
        $input['created_by'] = Helper::getEmployeeSystemID();
        $input['parent_id'] = 0;
        $input['level'] = $level;
        $input['parent_id'] = $parent_id;
        $procurementCategories = $this->procurementCategoryRepository->create($input);

        return $this->sendResponse($procurementCategories->toArray(), 'Procurement Category saved successfully');
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
            return $this->sendError('Procurement Category not found');
        }

        return $this->sendResponse($procurementCategory->toArray(), 'Procurement Category retrieved successfully');
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
        }

        if(isset($input['parent_id'])){
            $parent_id = $input['parent_id'];
        }

        $procurementCategory = TenderProcurementCategory::find($id);

        if (empty($procurementCategory)) {
            return $this->sendError('Procurement Category not found');
        }

        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'is_active' => 'required|numeric|min:0',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if( $level == 0 ){
            $procurementCodeExist = TenderProcurementCategory::withTrashed()
                ->select('id', 'deleted_at')
                ->where('id', '!=', $id)
                ->where('code', '=', $input['code'])
                ->where('level', '=', $level)->first();
        } else {
            $procurementCodeExist = TenderProcurementCategory::select('id')
                ->where('id', '!=', $id)
                ->where('code', '=', $input['code'])
                ->where('level', '=', $level)->first();
        }

        if (!empty($procurementCodeExist)) {
            Log::info($procurementCodeExist);
            if(is_null($procurementCodeExist['deleted_at'])) {
                return $this->sendError('Procurement code ' . $input['code'] . ' already exists');
            } else {
                $errorMessage = 'Procurement code '. $input['code'] . '  already exist, Do you really want to restore this?';
                return $this->sendError([$procurementCodeExist['id'], $errorMessage], 409);
            }
        }

        if( $level == 0 ){
            $procurementDesExist = TenderProcurementCategory::withTrashed()
                ->select('id', 'deleted_at')
                ->where('id', '!=', $id)
                ->where('description', '=', $input['description'])
                ->where('level', '=', $level)->first();
        } else {
            $procurementDesExist = TenderProcurementCategory::select('id')
                ->where('id', '!=', $id)
                ->where('description', '=', $input['description'])
                ->where('level', '=', $level)->first();
        }

        if(!empty($procurementDesExist)) {
            if(is_null($procurementCodeExist['deleted_at'])) {
                return $this->sendError('Procurement category description ' . $input['description'] . ' already exists');
            } else {
                $errorMessage = 'Procurement category description '. $input['description'] . ' already exist, Do you really want to restore this?';
                return $this->sendError([$procurementCodeExist['id'], $errorMessage], 409);
            }
        }

        $input['updated_pc'] = gethostname();
        $input['updated_by'] = Helper::getEmployeeSystemID();
        $input['parent_id'] = 0;
        $input['level'] = $level;
        $input['parent_id'] = $parent_id;

        $procurementCategory = TenderProcurementCategory::where('id', $id)->update($input);

        return $this->sendResponse($procurementCategory, 'Procurement Category updated successfully');
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
            return $this->sendError('Procurement Category not found');
        }
        $input['deleted_by'] = Helper::getEmployeeSystemID();
        $procurementCategoryDeleted = TenderProcurementCategory::where('id', $id)->update($input);
        
        if($procurementCategoryDeleted){
            $tenderProcurementCategory->delete();
        }

        return $this->sendResponse($id, 'Procurement category deleted successfully');
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
        $procurementCategory = TenderProcurementCategory::withTrashed()->find($id)->restore();
        $input['is_active'] = $request->input('is_active');

        if($procurementCategory) {
            TenderProcurementCategory::where('id', $id)->update($input);
        }


        return $this->sendResponse($procurementCategory, 'Procurement Category updated successfully');
    }
}
