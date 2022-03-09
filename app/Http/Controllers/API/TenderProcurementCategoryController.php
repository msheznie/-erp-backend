<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\UpdateFixedAssetCategoryAPIRequest;
use App\Models\FixedAssetCategory;
use App\Models\FixedAssetCategorySub;
use App\Scopes\ActiveScope;
use App\Models\TenderProcurementCategory;
use Illuminate\Http\Request;
use App\Repositories\ProcurementCategoryRepository;
use Illuminate\Support\Facades\Log;

class TenderProcurementCategoryController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(Request $request)
    {
        $level = 0;
        $input = $request->all();
        if(isset($input['level'])){
            $level = $input['level'];
        }

        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'is_active' => 'required|numeric|min:0',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $procurementCatCodeExist = TenderProcurementCategory::select('id')
            ->where('code', '=', $input['code'])->first();
        if (!empty($procurementCatCodeExist)) {
            return $this->sendError('Procurement code ' . $input['code'] . ' already exists');
        }

        $procurementCatDesExist = TenderProcurementCategory::select('id')
            ->where('description', '=', $input['description'])->first();
        if (!empty($procurementCatDesExist)) {
            return $this->sendError('Procurement category description ' . $input['description'] . ' already exists');
        }

        $input['created_pc'] = gethostname();
        $input['created_by'] = Helper::getEmployeeID();
        $input['parent_id'] = 0;
        $input['level'] = $level;
        $fixedAssetCategories = $this->procurementCategoryRepository->create($input);

        return $this->sendResponse($fixedAssetCategories->toArray(), 'Procurement Category saved successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TenderProcurementCategory  $tenderProcurementCategory
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        /** @var FixedAssetCategory $fixedAssetCategory */
        $fixedAssetCategory = TenderProcurementCategory::find($id);

        if (empty($fixedAssetCategory)) {
            return $this->sendError('Procurement Category not found');
        }

        return $this->sendResponse($fixedAssetCategory->toArray(), 'Procurement Category retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TenderProcurementCategory  $tenderProcurementCategory
     * @return \Illuminate\Http\Response
     */
    public function update($id, UpdateFixedAssetCategoryAPIRequest $request)
    {
        $level = 0;

        $input = $request->all();

        if(isset($input['level'])){
            $level = $input['level'];
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
            ->where('code', '=', $input['code'])->first();
        if (!empty($procurementCodeExist)) {
            return $this->sendError('Procurement code ' . $input['code'] . ' already exists');
        }

        $procurementDesExist = TenderProcurementCategory::select('id')
            ->where('id', '!=', $id)
            ->where('description', '=', $input['description'])->first();
        if (!empty($procurementDesExist)) {
            return $this->sendError('Procurement category description ' . $input['description'] . ' already exists');
        }

        $input['created_pc'] = gethostname();
        $input['created_by'] = Helper::getEmployeeID();
        $input['parent_id'] = 0;
        $input['level'] = $level;

        $fixedAssetCategory = TenderProcurementCategory::where('id', $id)->update($input);

        return $this->sendResponse($fixedAssetCategory, 'Procurement Category updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TenderProcurementCategory  $tenderProcurementCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(TenderProcurementCategory $tenderProcurementCategory)
    {
        /** @var FixedAssetCategory $fixedAssetCategory */
        $fixedAssetCategory = FixedAssetCategory::withoutGlobalScope(ActiveScope::class)->find($id);

        if (empty($fixedAssetCategory)) {
            return $this->sendError('Asset Category not found');
        }

        FixedAssetCategorySub::byFaCatID($id)->withoutGlobalScope(ActiveScope::class)->delete();

        $fixedAssetCategory->delete();

        return $this->sendResponse($id, 'Asset Category deleted successfully');
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

        $procurementCategories = TenderProcurementCategory::withoutGlobalScope(ActiveScope::class)
            ->where('level', $level)
            ->orderBy('id', $sort);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $procurementCategories = $procurementCategories->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($procurementCategories)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->make(true);
    }
}
