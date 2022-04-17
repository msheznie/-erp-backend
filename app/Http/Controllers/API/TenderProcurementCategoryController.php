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

        $procurementCatCodeExist = TenderProcurementCategory::withTrashed()
            ->select('id', 'deleted_at')
            ->where('code', '=', $input['code'])
            ->where('level', '=', $level)->first();

        if (!empty($procurementCatCodeExist)) {
            if(is_null($procurementCatCodeExist['deleted_at'])){
                return $this->sendError('Procurement code ' . $input['code'] . ' already exists');
            } else {
                return $this->sendError($procurementCatCodeExist['id'], 409);
            }
        }

        $procurementCatDesExist = TenderProcurementCategory::select('id')
            ->where('description', '=', $input['description'])
            ->where('level', '=', $level)->first();
        if (!empty($procurementCatDesExist)) {
            return $this->sendError('Procurement category description ' . $input['description'] . ' already exists');
        }

        $input['created_pc'] = gethostname();
        $input['created_by'] = Helper::getEmployeeID();
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
            return $this->restoreDeletedCategory($id);
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

        $procurementCodeExist = TenderProcurementCategory::select('id')
            ->where('id', '!=', $id)
            ->where('code', '=', $input['code'])
            ->where('level', '=', $level)->first();
        if (!empty($procurementCodeExist)) {
            return $this->sendError('Procurement code ' . $input['code'] . ' already exists');
        }

        $procurementDesExist = TenderProcurementCategory::select('id')
            ->where('id', '!=', $id)
            ->where('description', '=', $input['description'])
            ->where('level', '=', $level)->first();
        if(!empty($procurementDesExist)) {
            return $this->sendError('Procurement category description ' . $input['description'] . ' already exists');
        }

        $input['created_pc'] = gethostname();
        $input['created_by'] = Helper::getEmployeeID();
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

        $tenderProcurementCategory->delete();

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

    private function restoreDeletedCategory($id){

        $procurementCategory = TenderProcurementCategory::withTrashed()->find($id)->restore();

        return $this->sendResponse($procurementCategory, 'Procurement Category updated successfully');
    }
}
