<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Models\FixedAssetCategory;
use App\Scopes\ActiveScope;
use App\Models\TenderProcurementCategory;
use Illuminate\Http\Request;
use App\Repositories\ProcurementCategoryRepository;

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        $input = $request->all();
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
        $input['level'] = 0;
        $fixedAssetCategories = $this->procurementCategoryRepository->create($input);

        return $this->sendResponse($fixedAssetCategories->toArray(), 'Procurement Category saved successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TenderProcurementCategory  $tenderProcurementCategory
     * @return \Illuminate\Http\Response
     */
    public function show(TenderProcurementCategory $tenderProcurementCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TenderProcurementCategory  $tenderProcurementCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(TenderProcurementCategory $tenderProcurementCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TenderProcurementCategory  $tenderProcurementCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TenderProcurementCategory $tenderProcurementCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TenderProcurementCategory  $tenderProcurementCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(TenderProcurementCategory $tenderProcurementCategory)
    {
        //
    }

    public function getAllProcurementCategory(Request $request)
    {
        $input = $request->all();
        $selectedCompanyId = isset($input['companyId']) ? $input['companyId'] : 0;
        $isGroup = Helper::checkIsCompanyGroup($selectedCompanyId);
        if ($isGroup) {
            $subCompanies = Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $assetCategories = TenderProcurementCategory::withoutGlobalScope(ActiveScope::class)
            ->with(['company'])
            ->orderBy('faCatID', $sort);

        if (isset($input['isAll']) && !$input['isAll']) {
            $assetCategories = $assetCategories->whereIn('companySystemID', $subCompanies);
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCategories = $assetCategories->where(function ($query) use ($search) {
                $query->where('catDescription', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($assetCategories)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->make(true);
    }
}
