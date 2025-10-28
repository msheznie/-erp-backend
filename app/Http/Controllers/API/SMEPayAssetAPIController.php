<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSMEPayAssetAPIRequest;
use App\Http\Requests\API\UpdateSMEPayAssetAPIRequest;
use App\Models\SMEPayAsset;
use App\Repositories\SMEPayAssetRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SMEPayAssetController
 * @package App\Http\Controllers\API
 */

class SMEPayAssetAPIController extends AppBaseController
{
    /** @var  SMEPayAssetRepository */
    private $sMEPayAssetRepository;

    public function __construct(SMEPayAssetRepository $sMEPayAssetRepo)
    {
        $this->sMEPayAssetRepository = $sMEPayAssetRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMEPayAssets",
     *      summary="Get a listing of the SMEPayAssets.",
     *      tags={"SMEPayAsset"},
     *      description="Get all SMEPayAssets",
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
     *                  @SWG\Items(ref="#/definitions/SMEPayAsset")
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
        $this->sMEPayAssetRepository->pushCriteria(new RequestCriteria($request));
        $this->sMEPayAssetRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sMEPayAssets = $this->sMEPayAssetRepository->all();

        return $this->sendResponse($sMEPayAssets->toArray(), trans('custom.s_m_e_pay_assets_retrieved_successfully'));
    }

    /**
     * @param CreateSMEPayAssetAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sMEPayAssets",
     *      summary="Store a newly created SMEPayAsset in storage",
     *      tags={"SMEPayAsset"},
     *      description="Store SMEPayAsset",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMEPayAsset that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMEPayAsset")
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
     *                  ref="#/definitions/SMEPayAsset"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSMEPayAssetAPIRequest $request)
    {
        $input = $request->all();

        $sMEPayAsset = $this->sMEPayAssetRepository->create($input);

        return $this->sendResponse($sMEPayAsset->toArray(), trans('custom.s_m_e_pay_asset_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMEPayAssets/{id}",
     *      summary="Display the specified SMEPayAsset",
     *      tags={"SMEPayAsset"},
     *      description="Get SMEPayAsset",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMEPayAsset",
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
     *                  ref="#/definitions/SMEPayAsset"
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
        /** @var SMEPayAsset $sMEPayAsset */
        $sMEPayAsset = $this->sMEPayAssetRepository->findWithoutFail($id);

        if (empty($sMEPayAsset)) {
            return $this->sendError(trans('custom.s_m_e_pay_asset_not_found'));
        }

        return $this->sendResponse($sMEPayAsset->toArray(), trans('custom.s_m_e_pay_asset_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSMEPayAssetAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sMEPayAssets/{id}",
     *      summary="Update the specified SMEPayAsset in storage",
     *      tags={"SMEPayAsset"},
     *      description="Update SMEPayAsset",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMEPayAsset",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMEPayAsset that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMEPayAsset")
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
     *                  ref="#/definitions/SMEPayAsset"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSMEPayAssetAPIRequest $request)
    {
        $input = $request->all();

        /** @var SMEPayAsset $sMEPayAsset */
        $sMEPayAsset = $this->sMEPayAssetRepository->findWithoutFail($id);

        if (empty($sMEPayAsset)) {
            return $this->sendError(trans('custom.s_m_e_pay_asset_not_found'));
        }

        $sMEPayAsset = $this->sMEPayAssetRepository->update($input, $id);

        return $this->sendResponse($sMEPayAsset->toArray(), trans('custom.smepayasset_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sMEPayAssets/{id}",
     *      summary="Remove the specified SMEPayAsset from storage",
     *      tags={"SMEPayAsset"},
     *      description="Delete SMEPayAsset",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMEPayAsset",
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
        /** @var SMEPayAsset $sMEPayAsset */
        $sMEPayAsset = $this->sMEPayAssetRepository->findWithoutFail($id);

        if (empty($sMEPayAsset)) {
            return $this->sendError(trans('custom.s_m_e_pay_asset_not_found'));
        }

        $sMEPayAsset->delete();

        return $this->sendSuccess('S M E Pay Asset deleted successfully');
    }
}
