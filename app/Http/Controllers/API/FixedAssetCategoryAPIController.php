<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateFixedAssetCategoryAPIRequest;
use App\Http\Requests\API\UpdateFixedAssetCategoryAPIRequest;
use App\Models\Company;
use App\Models\FixedAssetCategory;
use App\Models\ItemMaster;
use App\Models\FixedAssetMaster;
use App\Models\FixedAssetCategorySub;
use App\Models\YesNoSelection;
use App\Repositories\FixedAssetCategoryRepository;
use App\Scopes\ActiveScope;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FixedAssetCategoryController
 * @package App\Http\Controllers\API
 */

class FixedAssetCategoryAPIController extends AppBaseController
{
    /** @var  FixedAssetCategoryRepository */
    private $fixedAssetCategoryRepository;

    public function __construct(FixedAssetCategoryRepository $fixedAssetCategoryRepo)
    {
        $this->fixedAssetCategoryRepository = $fixedAssetCategoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetCategories",
     *      summary="Get a listing of the FixedAssetCategories.",
     *      tags={"FixedAssetCategory"},
     *      description="Get all FixedAssetCategories",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/FixedAssetCategory")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->fixedAssetCategoryRepository->pushCriteria(new RequestCriteria($request));
        $this->fixedAssetCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fixedAssetCategories = $this->fixedAssetCategoryRepository->all();

        return $this->sendResponse($fixedAssetCategories->toArray(), trans('custom.fixed_asset_categories_retrieved_successfully'));
    }

    /**
     * @param CreateFixedAssetCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/fixedAssetCategories",
     *      summary="Store a newly created FixedAssetCategory in storage",
     *      tags={"FixedAssetCategory"},
     *      description="Store FixedAssetCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetCategory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetCategory")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/FixedAssetCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFixedAssetCategoryAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'companySystemID' => 'required|numeric|min:1',
            'isActive' => 'required|numeric|min:0',
            'catDescription' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $assetCatCodeExist = FixedAssetCategory::select('faCatID')
            ->where('companySystemID', '=', $input['companySystemID'])
            ->where('catCode', '=', $input['catCode'])->first();
        if (!empty($assetCatCodeExist)) {
            return $this->sendError('Asset code ' . $input['catCode'] . ' already exists');
        }

        $assetCatDesExist = FixedAssetCategory::select('faCatID')
            ->where('companySystemID', '=', $input['companySystemID'])
            ->where('catDescription', '=', $input['catDescription'])->first();
        if (!empty($assetCatDesExist)) {
            return $this->sendError('Asset category description ' . $input['catDescription'] . ' already exists');
        }



        $company = Company::find($input['companySystemID']);

        if (empty($company)) {
            return $this->sendError(trans('custom.company_not_found'));
        }

        $input['companyID'] = $company->CompanyID;
        $input['createdPcID'] = gethostname();
        $input['createdUserSystemID'] = Helper::getEmployeeSystemID();
        $input['createdUserID'] = Helper::getEmployeeID();
        $fixedAssetCategories = $this->fixedAssetCategoryRepository->create($input);

        return $this->sendResponse($fixedAssetCategories->toArray(), trans('custom.asset_category_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetCategories/{id}",
     *      summary="Display the specified FixedAssetCategory",
     *      tags={"FixedAssetCategory"},
     *      description="Get FixedAssetCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/FixedAssetCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var FixedAssetCategory $fixedAssetCategory */
        $fixedAssetCategory = FixedAssetCategory::withoutGlobalScope(ActiveScope::class)->find($id);

        if (empty($fixedAssetCategory)) {
            return $this->sendError(trans('custom.asset_category_not_found'));
        }

        return $this->sendResponse($fixedAssetCategory->toArray(), trans('custom.fixed_asset_category_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateFixedAssetCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fixedAssetCategories/{id}",
     *      summary="Update the specified FixedAssetCategory in storage",
     *      tags={"FixedAssetCategory"},
     *      description="Update FixedAssetCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetCategory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetCategory")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/FixedAssetCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFixedAssetCategoryAPIRequest $request)
    {
        $input = $request->all();


        /** @var FixedAssetCategory $fixedAssetCategory */
        $fixedAssetCategory = FixedAssetCategory::withoutGlobalScope(ActiveScope::class)->find($id);

        if (empty($fixedAssetCategory)) {
            return $this->sendError(trans('custom.fixed_asset_category_not_found'));
        }

        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'companySystemID' => 'required|numeric|min:1',
            'isActive' => 'required|numeric|min:0',
            'catDescription' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $assetCatCodeExist = FixedAssetCategory::select('faCatID')
            ->where('faCatID', '!=', $id)
            ->where('companySystemID', '=', $input['companySystemID'])
            ->where('catCode', '=', $input['catCode'])->first();
        if (!empty($assetCatCodeExist)) {
            return $this->sendError('Asset code ' . $input['catCode'] . ' already exists');
        }

        $assetCatDesExist = FixedAssetCategory::select('faCatID')
            ->where('faCatID', '!=', $id)
            ->where('companySystemID', '=', $input['companySystemID'])
            ->where('catDescription', '=', $input['catDescription'])->first();
        if (!empty($assetCatDesExist)) {
            return $this->sendError('Asset category description ' . $input['catDescription'] . ' already exists');
        }


        $company = Company::find($input['companySystemID']);

        if (empty($company)) {
            return $this->sendError(trans('custom.company_not_found'));
        }

        $input['companyID'] = $company->CompanyID;
        $input['modifiedPc'] = gethostname();
        $input['modifiedUserSystemID'] = Helper::getEmployeeSystemID();
        $input['modifiedUser'] = Helper::getEmployeeID();

        $fixedAssetCategory = FixedAssetCategory::withoutGlobalScope(ActiveScope::class)->where('faCatID', $id)->update($input);

        return $this->sendResponse($fixedAssetCategory, trans('custom.asset_category_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fixedAssetCategories/{id}",
     *      summary="Remove the specified FixedAssetCategory from storage",
     *      tags={"FixedAssetCategory"},
     *      description="Delete FixedAssetCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var FixedAssetCategory $fixedAssetCategory */
        $fixedAssetCategory = FixedAssetCategory::withoutGlobalScope(ActiveScope::class)->find($id);

        if (empty($fixedAssetCategory)) {
            return $this->sendError(trans('custom.asset_category_not_found'));
        }


        $checkInItems = ItemMaster::where('faCatID', $id)->first();

        if ($checkInItems) {
            return $this->sendError(trans('custom.this_asset_category_is_already_assigned_to_assets_'));
        }

        $checkInCostings = FixedAssetMaster::where('faCatID', $id)->first();

        if ($checkInCostings) {
            return $this->sendError(trans('custom.this_asset_category_is_already_assigned_to_assets_'));
        }


        FixedAssetCategorySub::byFaCatID($id)->withoutGlobalScope(ActiveScope::class)->delete();

        $fixedAssetCategory->delete();

        return $this->sendResponse($id, trans('custom.asset_category_deleted_successfully'));
    }

    public function getAllAssetCategory(Request $request)
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

        $assetCategories = FixedAssetCategory::withoutGlobalScope(ActiveScope::class)
            ->with(['company'])
            ->orderBy('faCatID', $sort);

        if (!$isGroup) {
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

    public function getAssetCategoryFormData(Request $request)
    {
        $yesNoSelection = YesNoSelection::selectRaw('idyesNoselection as value,YesNo as label')->get();
        $selectedCompanyId = $request['companySystemID'];
        $isGroup = Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $companies = Company::whereIn('companySystemID', $subCompanies)
            ->selectRaw('companySystemID as value,CONCAT(CompanyID, " - " ,CompanyName) as label')
            ->get();

        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'companies' => $companies,
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }
}
