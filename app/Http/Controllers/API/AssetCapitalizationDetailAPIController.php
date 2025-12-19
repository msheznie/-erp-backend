<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetCapitalizationDetailAPIRequest;
use App\Http\Requests\API\UpdateAssetCapitalizationDetailAPIRequest;
use App\Models\AssetCapitalization;
use App\Models\AssetCapitalizationDetail;
use App\Models\Company;
use App\Models\FixedAssetDepreciationPeriod;
use App\Models\FixedAssetMaster;
use App\Repositories\AssetCapitalizationDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetCapitalizationDetailController
 * @package App\Http\Controllers\API
 */
class AssetCapitalizationDetailAPIController extends AppBaseController
{
    /** @var  AssetCapitalizationDetailRepository */
    private $assetCapitalizationDetailRepository;

    public function __construct(AssetCapitalizationDetailRepository $assetCapitalizationDetailRepo)
    {
        $this->assetCapitalizationDetailRepository = $assetCapitalizationDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetCapitalizationDetails",
     *      summary="Get a listing of the AssetCapitalizationDetails.",
     *      tags={"AssetCapitalizationDetail"},
     *      description="Get all AssetCapitalizationDetails",
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
     *                  @SWG\Items(ref="#/definitions/AssetCapitalizationDetail")
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
        $this->assetCapitalizationDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->assetCapitalizationDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetCapitalizationDetails = $this->assetCapitalizationDetailRepository->all();

        return $this->sendResponse($assetCapitalizationDetails->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_capitalization_details')]));
    }

    /**
     * @param CreateAssetCapitalizationDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetCapitalizationDetails",
     *      summary="Store a newly created AssetCapitalizationDetail in storage",
     *      tags={"AssetCapitalizationDetail"},
     *      description="Store AssetCapitalizationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetCapitalizationDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetCapitalizationDetail")
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
     *                  ref="#/definitions/AssetCapitalizationDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetCapitalizationDetailAPIRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $input = $this->convertArrayToValue($input);

            $master = AssetCapitalization::find($input["capitalizationID"]);

            if (empty($master)) {
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_capitalization')]));
            }

            $company = Company::find($input['companySystemID']);
            if (empty($company)) {
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company')]));
            }

            if ($master->faID == $input['faID']) {
                return $this->sendError(trans('custom.cannot_add_same_item_which_is_selected_in_header'));
            }

            $detail = $this->assetCapitalizationDetailRepository->findWhere(['capitalizationID' => $input['capitalizationID'], 'faID' => $input['faID']]);
            if (count($detail) > 0) {
                return $this->sendError(trans('custom.cannot_add_same_item'));
            }

            $input['companyID'] = $company->CompanyID;

            $assetMaster = FixedAssetMaster::find($input['faID']);

            $input['faCode'] = $assetMaster->faCode;
            $input['assetDescription'] = $assetMaster->assetDescription;
            $input['serviceLineSystemID'] = $assetMaster->serviceLineSystemID;
            $input['serviceLineCode'] = $assetMaster->serviceLineCode;
            $input['dateAQ'] = $assetMaster->dateAQ;
            $depreciationLocal = FixedAssetDepreciationPeriod::OfCompany([$input['companySystemID']])->OfAsset($input['faID'])->sum('depAmountLocal');
            $depreciationRpt = FixedAssetDepreciationPeriod::OfCompany([$input['companySystemID']])->OfAsset($input['faID'])->sum('depAmountRpt');

            $nbvRpt = $assetMaster->costUnitRpt - $depreciationRpt;
            $nbvLocal = $assetMaster->COSTUNIT - $depreciationLocal;

            $input['assetNBVLocal'] = $nbvLocal;
            $input['assetNBVRpt'] = $nbvRpt;

            $input['createdPcID'] = gethostname();
            $input['createdUserID'] = \Helper::getEmployeeID();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();

            $assetCapitalizationDetails = $this->assetCapitalizationDetailRepository->create($input);
            DB::commit();
            return $this->sendResponse($assetCapitalizationDetails->toArray(), trans('custom.save', ['attribute' => trans('custom.asset_capitalization_details')]));

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetCapitalizationDetails/{id}",
     *      summary="Display the specified AssetCapitalizationDetail",
     *      tags={"AssetCapitalizationDetail"},
     *      description="Get AssetCapitalizationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalizationDetail",
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
     *                  ref="#/definitions/AssetCapitalizationDetail"
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
        /** @var AssetCapitalizationDetail $assetCapitalizationDetail */
        $assetCapitalizationDetail = $this->assetCapitalizationDetailRepository->findWithoutFail($id);

        if (empty($assetCapitalizationDetail)) {
            return $this->sendError(trans('custom.asset_capitalization_detail_not_found'));
        }

        return $this->sendResponse($assetCapitalizationDetail->toArray(), trans('custom.not_found', ['attribute' => trans('custom.asset_capitalization_details')]));
    }

    /**
     * @param int $id
     * @param UpdateAssetCapitalizationDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetCapitalizationDetails/{id}",
     *      summary="Update the specified AssetCapitalizationDetail in storage",
     *      tags={"AssetCapitalizationDetail"},
     *      description="Update AssetCapitalizationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalizationDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetCapitalizationDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetCapitalizationDetail")
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
     *                  ref="#/definitions/AssetCapitalizationDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetCapitalizationDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetCapitalizationDetail $assetCapitalizationDetail */
        $assetCapitalizationDetail = $this->assetCapitalizationDetailRepository->findWithoutFail($id);

        if (empty($assetCapitalizationDetail)) {
            return $this->sendError(trans('custom.asset_capitalization_detail_not_found'));
        }

        $assetCapitalizationDetail = $this->assetCapitalizationDetailRepository->update($input, $id);

        return $this->sendResponse($assetCapitalizationDetail->toArray(), trans('custom.update', ['attribute' => trans('custom.asset_capitalization_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetCapitalizationDetails/{id}",
     *      summary="Remove the specified AssetCapitalizationDetail from storage",
     *      tags={"AssetCapitalizationDetail"},
     *      description="Delete AssetCapitalizationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalizationDetail",
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
        /** @var AssetCapitalizationDetail $assetCapitalizationDetail */
        DB::beginTransaction();
        try {
            $assetCapitalizationDetail = $this->assetCapitalizationDetailRepository->findWithoutFail($id);
            if (empty($assetCapitalizationDetail)) {
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_capitalization_details')]));
            }
            $assetCapitalizationDetail->delete();
            DB::commit();
            return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.asset_capitalization_details')]));

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function getCapitalizationDetails(Request $request)
    {
        $id = $request->capitalizationID;
        $assetCapitalizationDetail = $this->assetCapitalizationDetailRepository->with(['segment'])->findWhere(['capitalizationID' => $id]);
        return $this->sendResponse($assetCapitalizationDetail, trans('custom.retrieve', ['attribute' => trans('custom.details')]));
    }

    public function deleteAllAssetCapitalizationDet(Request $request)
    {
        $assetCapitalizationDetail = AssetCapitalizationDetail::where('capitalizationID',$request->capitalizationID)->get();
        if (empty($assetCapitalizationDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_capitalization_details')]));
        }
        $assetCapitalizationDetail = AssetCapitalizationDetail::where('capitalizationID',$request->capitalizationID)->delete();
        return $this->sendResponse($assetCapitalizationDetail, trans('custom.delete', ['attribute' => trans('custom.asset_capitalization_details')]));
    }
}
