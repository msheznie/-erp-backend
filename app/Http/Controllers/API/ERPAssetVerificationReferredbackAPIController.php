<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateERPAssetVerificationReferredbackAPIRequest;
use App\Http\Requests\API\UpdateERPAssetVerificationReferredbackAPIRequest;
use App\Models\ERPAssetVerificationReferredback;
use App\Repositories\ERPAssetVerificationReferredbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\ERPAssetVerificationDetailReferredback;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ERPAssetVerificationReferredbackController
 * @package App\Http\Controllers\API
 */

class ERPAssetVerificationReferredbackAPIController extends AppBaseController
{
    /** @var  ERPAssetVerificationReferredbackRepository */
    private $eRPAssetVerificationReferredbackRepository;

    public function __construct(ERPAssetVerificationReferredbackRepository $eRPAssetVerificationReferredbackRepo)
    {
        $this->eRPAssetVerificationReferredbackRepository = $eRPAssetVerificationReferredbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/eRPAssetVerificationReferredbacks",
     *      summary="Get a listing of the ERPAssetVerificationReferredbacks.",
     *      tags={"ERPAssetVerificationReferredback"},
     *      description="Get all ERPAssetVerificationReferredbacks",
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
     *                  @SWG\Items(ref="#/definitions/ERPAssetVerificationReferredback")
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
        $this->eRPAssetVerificationReferredbackRepository->pushCriteria(new RequestCriteria($request));
        $this->eRPAssetVerificationReferredbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $eRPAssetVerificationReferredbacks = $this->eRPAssetVerificationReferredbackRepository->all();

        return $this->sendResponse($eRPAssetVerificationReferredbacks->toArray(), trans('custom.e_r_p_asset_verification_referredbacks_retrieved_s'));
    }

    /**
     * @param CreateERPAssetVerificationReferredbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/eRPAssetVerificationReferredbacks",
     *      summary="Store a newly created ERPAssetVerificationReferredback in storage",
     *      tags={"ERPAssetVerificationReferredback"},
     *      description="Store ERPAssetVerificationReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ERPAssetVerificationReferredback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ERPAssetVerificationReferredback")
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
     *                  ref="#/definitions/ERPAssetVerificationReferredback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateERPAssetVerificationReferredbackAPIRequest $request)
    {
        $input = $request->all();

        $eRPAssetVerificationReferredback = $this->eRPAssetVerificationReferredbackRepository->create($input);

        return $this->sendResponse($eRPAssetVerificationReferredback->toArray(), trans('custom.e_r_p_asset_verification_referredback_saved_succes'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/eRPAssetVerificationReferredbacks/{id}",
     *      summary="Display the specified ERPAssetVerificationReferredback",
     *      tags={"ERPAssetVerificationReferredback"},
     *      description="Get ERPAssetVerificationReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ERPAssetVerificationReferredback",
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
     *                  ref="#/definitions/ERPAssetVerificationReferredback"
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
        /** @var ERPAssetVerificationReferredback $eRPAssetVerificationReferredback */
        $eRPAssetVerificationReferredback = $this->eRPAssetVerificationReferredbackRepository->findWithoutFail($id);

        if (empty($eRPAssetVerificationReferredback)) {
            return $this->sendError(trans('custom.e_r_p_asset_verification_referredback_not_found'));
        }

        return $this->sendResponse($eRPAssetVerificationReferredback->toArray(), trans('custom.e_r_p_asset_verification_referredback_retrieved_su'));
    }

    /**
     * @param int $id
     * @param UpdateERPAssetVerificationReferredbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/eRPAssetVerificationReferredbacks/{id}",
     *      summary="Update the specified ERPAssetVerificationReferredback in storage",
     *      tags={"ERPAssetVerificationReferredback"},
     *      description="Update ERPAssetVerificationReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ERPAssetVerificationReferredback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ERPAssetVerificationReferredback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ERPAssetVerificationReferredback")
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
     *                  ref="#/definitions/ERPAssetVerificationReferredback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateERPAssetVerificationReferredbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var ERPAssetVerificationReferredback $eRPAssetVerificationReferredback */
        $eRPAssetVerificationReferredback = $this->eRPAssetVerificationReferredbackRepository->findWithoutFail($id);

        if (empty($eRPAssetVerificationReferredback)) {
            return $this->sendError(trans('custom.e_r_p_asset_verification_referredback_not_found'));
        }

        $eRPAssetVerificationReferredback = $this->eRPAssetVerificationReferredbackRepository->update($input, $id);

        return $this->sendResponse($eRPAssetVerificationReferredback->toArray(), trans('custom.erpassetverificationreferredback_updated_successfu'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/eRPAssetVerificationReferredbacks/{id}",
     *      summary="Remove the specified ERPAssetVerificationReferredback from storage",
     *      tags={"ERPAssetVerificationReferredback"},
     *      description="Delete ERPAssetVerificationReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ERPAssetVerificationReferredback",
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
        /** @var ERPAssetVerificationReferredback $eRPAssetVerificationReferredback */
        $eRPAssetVerificationReferredback = $this->eRPAssetVerificationReferredbackRepository->findWithoutFail($id);

        if (empty($eRPAssetVerificationReferredback)) {
            return $this->sendError(trans('custom.e_r_p_asset_verification_referredback_not_found'));
        }

        $eRPAssetVerificationReferredback->delete();

        return $this->sendSuccess('E R P Asset Verification Referredback deleted successfully');
    }

    public function getAssetVerificationAmendHistory(Request $request)
    {
        $input = $request->all();
        $assetVerificationAutoID = $input['assetVerificationID'];
        $assetTransferAmendHistory = ERPAssetVerificationReferredback::where('id', $assetVerificationAutoID)
            ->get();
        return $this->sendResponse($assetTransferAmendHistory, trans('custom.asset_verification_retrieved_successfully_1'));
    }

    public function fetchAssetVerification($id)
    {
        $assetVerification = ERPAssetVerificationReferredback::where('assetVerificationMasterRefferedBackID', $id)->first(); 
        if (empty($assetVerification)) {
            return $this->sendError(trans('custom.asset_verification_not_found'));
        } 
        return $this->sendResponse($assetVerification, trans('custom.asset_verification_retrieved_successfully'));
    }
}
