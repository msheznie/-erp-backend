<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateFixedAssetCategorySubAPIRequest;
use App\Http\Requests\API\UpdateFixedAssetCategorySubAPIRequest;
use App\Models\FixedAssetCategory;
use App\Models\FixedAssetCategorySub;
use App\Models\ItemMaster;
use App\Models\FixedAssetMaster;
use App\Repositories\FixedAssetCategorySubRepository;
use App\Scopes\ActiveScope;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FixedAssetCategorySubController
 * @package App\Http\Controllers\API
 */

class FixedAssetCategorySubAPIController extends AppBaseController
{
    /** @var  FixedAssetCategorySubRepository */
    private $fixedAssetCategorySubRepository;
    private $messages = [
        'faCatID.required' => 'Main Category field is required.',
    ];

    public function __construct(FixedAssetCategorySubRepository $fixedAssetCategorySubRepo)
    {
        $this->fixedAssetCategorySubRepository = $fixedAssetCategorySubRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetCategorySubs",
     *      summary="Get a listing of the FixedAssetCategorySubs.",
     *      tags={"FixedAssetCategorySub"},
     *      description="Get all FixedAssetCategorySubs",
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
     *                  @SWG\Items(ref="#/definitions/FixedAssetCategorySub")
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
        $this->fixedAssetCategorySubRepository->pushCriteria(new RequestCriteria($request));
        $this->fixedAssetCategorySubRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fixedAssetCategorySubs = $this->fixedAssetCategorySubRepository->all();

        return $this->sendResponse($fixedAssetCategorySubs->toArray(), trans('custom.fixed_asset_category_subs_retrieved_successfully'));
    }

    /**
     * @param CreateFixedAssetCategorySubAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/fixedAssetCategorySubs",
     *      summary="Store a newly created FixedAssetCategorySub in storage",
     *      tags={"FixedAssetCategorySub"},
     *      description="Store FixedAssetCategorySub",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetCategorySub that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetCategorySub")
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
     *                  ref="#/definitions/FixedAssetCategorySub"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFixedAssetCategorySubAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'isActive' => 'required|numeric|min:0',
            'catDescription' => 'required',
            'faCatID' => 'required',
        ], $this->messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $fixedAssetCategory = FixedAssetCategory::withoutGlobalScope(ActiveScope::class)->find($input['faCatID']);

        if (empty($fixedAssetCategory)) {
            return $this->sendError(trans('custom.asset_category_not_found'));
        }

        $assetCatCodeSubExist = FixedAssetCategorySub::select('faCatSubID')
            ->where('companySystemID', '=', $input['companySystemID'])
            ->where('suCatCode', '=', $input['suCatCode'])->first();
        if (!empty($assetCatCodeSubExist)) {
            return $this->sendError('Asset sub code ' . $input['suCatCode'] . ' already exists');
        }

        $assetCatDesSubExist = FixedAssetCategorySub::select('faCatSubID')
            ->where('companySystemID', '=', $input['companySystemID'])
            ->where('catDescription', '=', $input['catDescription'])->first();
        if (!empty($assetCatDesSubExist)) {
            return $this->sendError('Asset sub category description ' . $input['catDescription'] . ' already exists');
        }

        $input['mainCatDescription'] = $fixedAssetCategory->catDescription;
        $input['companySystemID'] = $fixedAssetCategory->companySystemID;
        $input['companyID'] = $fixedAssetCategory->companyID;
        $input['createdPcID'] = gethostname();
        $input['createdUserSystemID'] = Helper::getEmployeeSystemID();
        $input['createdUserID'] = Helper::getEmployeeID();

        $fixedAssetCategorySubs = $this->fixedAssetCategorySubRepository->create($input);

        return $this->sendResponse($fixedAssetCategorySubs->toArray(), trans('custom.fixed_asset_category_sub_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetCategorySubs/{id}",
     *      summary="Display the specified FixedAssetCategorySub",
     *      tags={"FixedAssetCategorySub"},
     *      description="Get FixedAssetCategorySub",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetCategorySub",
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
     *                  ref="#/definitions/FixedAssetCategorySub"
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
        /** @var FixedAssetCategorySub $fixedAssetCategorySub */
        $fixedAssetCategorySub =  FixedAssetCategorySub::withoutGlobalScope(ActiveScope::class)->find($id);

        if (empty($fixedAssetCategorySub)) {
            return $this->sendError(trans('custom.asset_category_sub_not_found'));
        }

        return $this->sendResponse($fixedAssetCategorySub->toArray(), trans('custom.asset_category_sub_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateFixedAssetCategorySubAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fixedAssetCategorySubs/{id}",
     *      summary="Update the specified FixedAssetCategorySub in storage",
     *      tags={"FixedAssetCategorySub"},
     *      description="Update FixedAssetCategorySub",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetCategorySub",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetCategorySub that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetCategorySub")
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
     *                  ref="#/definitions/FixedAssetCategorySub"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFixedAssetCategorySubAPIRequest $request)
    {
        $input = $request->all();

        /** @var FixedAssetCategorySub $fixedAssetCategorySub */
        $fixedAssetCategorySub =  FixedAssetCategorySub::withoutGlobalScope(ActiveScope::class)->find($id);

        if (empty($fixedAssetCategorySub)) {
            return $this->sendError(trans('custom.asset_sub_category_not_found'));
        }

        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'isActive' => 'required|numeric|min:0',
            'catDescription' => 'required',
            'faCatID' => 'required',
        ], $this->messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $fixedAssetCategory = FixedAssetCategory::withoutGlobalScope(ActiveScope::class)->find($input['faCatID']);

        if (empty($fixedAssetCategory)) {
            return $this->sendError(trans('custom.asset_category_not_found'));
        }

        $assetCatCodeSubExist = FixedAssetCategorySub::select('faCatSubID')
            ->where('faCatSubID', '!=', $id)
            ->where('companySystemID', '=', $input['companySystemID'])
            ->where('suCatCode', '=', $input['suCatCode'])->first();
        if (!empty($assetCatCodeSubExist)) {
            return $this->sendError('Asset sub code ' . $input['suCatCode'] . ' already exists');
        }

        $assetCatDesSubExist = FixedAssetCategorySub::select('faCatSubID')
            ->where('faCatSubID', '!=', $id)
            ->where('companySystemID', '=', $input['companySystemID'])
            ->where('catDescription', '=', $input['catDescription'])->first();
        if (!empty($assetCatDesSubExist)) {
            return $this->sendError('Asset sub category description ' . $input['catDescription'] . ' already exists');
        }

        $input['mainCatDescription'] = $fixedAssetCategory->catDescription;
        $input['companySystemID'] = $fixedAssetCategory->companySystemID;
        $input['companyID'] = $fixedAssetCategory->companyID;
        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = Helper::getEmployeeID();
        $fixedAssetCategorySub = FixedAssetCategorySub::withoutGlobalScope(ActiveScope::class)->where('faCatSubID', $id)->update($input);

        return $this->sendResponse($fixedAssetCategorySub, trans('custom.asset_sub_category_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fixedAssetCategorySubs/{id}",
     *      summary="Remove the specified FixedAssetCategorySub from storage",
     *      tags={"FixedAssetCategorySub"},
     *      description="Delete FixedAssetCategorySub",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetCategorySub",
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
        /** @var FixedAssetCategorySub $fixedAssetCategorySub */
        $fixedAssetCategorySub =  FixedAssetCategorySub::withoutGlobalScope(ActiveScope::class)->find($id);

        if (empty($fixedAssetCategorySub)) {
            return $this->sendError(trans('custom.asset_category_sub_not_found'));
        }

        $checkInItems = ItemMaster::where('faSubCatID', $id)->first();

        if ($checkInItems) {
            return $this->sendError(trans('custom.this_asset_sub_category_is_already_assigned_to_ass'));
        }

        $checkInCostings = FixedAssetMaster::where('faSubCatID', $id)->first();

        if ($checkInCostings) {
            return $this->sendError(trans('custom.this_asset_sub_category_is_already_assigned_to_ass'));
        }

        $fixedAssetCategorySub->delete();

        return $this->sendResponse($id, trans('custom.asset_sub_category_deleted_successfully'));
    }

    public function getAllAssetSubCategoryByMain(Request $request)
    {
        $input = $request->all();
        $selectedCompanyId = isset($input['companyId']) ? $input['companyId'] : 0;
        $id = isset($input['id']) ? $input['id'] : 0;

        $isDropdown = isset($input['isDropdown']) && $input['isDropdown'];

        if (!$isDropdown) {
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

            $assetCategories = FixedAssetCategorySub::byFaCatID($id)->withoutGlobalScope(ActiveScope::class)
                ->with(['company'])
                ->orderBy('faCatSubID', $sort);

            if (isset($input['isAll']) && !$input['isAll']) {
                $assetCategories = $assetCategories->ofCompany($subCompanies);
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
        else {
            $assetCategories = FixedAssetCategorySub::withoutGlobalScope(ActiveScope::class)
                ->where('companySystemID', $selectedCompanyId)
                ->when(is_array($input['id']), function($query) use ($input) {
                    return $query->whereIn('faCatID', $input['id']);
                }, function($query) use ($id) {
                    return $query->where('faCatID', $id);
                })
                ->get();

            return $this->sendResponse($assetCategories, trans('custom.asset_sub_category_fetched_successfully'));
        }
    }
}
