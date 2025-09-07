<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateERPAssetTransferDetailsRefferedbackAPIRequest;
use App\Http\Requests\API\UpdateERPAssetTransferDetailsRefferedbackAPIRequest;
use App\Models\ERPAssetTransferDetailsRefferedback;
use App\Repositories\ERPAssetTransferDetailsRefferedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\AssetTransferReferredback;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ERPAssetTransferDetailsRefferedbackController
 * @package App\Http\Controllers\API
 */

class ERPAssetTransferDetailsRefferedbackAPIController extends AppBaseController
{
    /** @var  ERPAssetTransferDetailsRefferedbackRepository */
    private $eRPAssetTransferDetailsRefferedbackRepository;

    public function __construct(ERPAssetTransferDetailsRefferedbackRepository $eRPAssetTransferDetailsRefferedbackRepo)
    {
        $this->eRPAssetTransferDetailsRefferedbackRepository = $eRPAssetTransferDetailsRefferedbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/eRPAssetTransferDetailsRefferedbacks",
     *      summary="Get a listing of the ERPAssetTransferDetailsRefferedbacks.",
     *      tags={"ERPAssetTransferDetailsRefferedback"},
     *      description="Get all ERPAssetTransferDetailsRefferedbacks",
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
     *                  @SWG\Items(ref="#/definitions/ERPAssetTransferDetailsRefferedback")
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
        $this->eRPAssetTransferDetailsRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $this->eRPAssetTransferDetailsRefferedbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $eRPAssetTransferDetailsRefferedbacks = $this->eRPAssetTransferDetailsRefferedbackRepository->all();

        return $this->sendResponse($eRPAssetTransferDetailsRefferedbacks->toArray(), trans('custom.e_r_p_asset_transfer_details_refferedbacks_retriev'));
    }

    /**
     * @param CreateERPAssetTransferDetailsRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/eRPAssetTransferDetailsRefferedbacks",
     *      summary="Store a newly created ERPAssetTransferDetailsRefferedback in storage",
     *      tags={"ERPAssetTransferDetailsRefferedback"},
     *      description="Store ERPAssetTransferDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ERPAssetTransferDetailsRefferedback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ERPAssetTransferDetailsRefferedback")
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
     *                  ref="#/definitions/ERPAssetTransferDetailsRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateERPAssetTransferDetailsRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        $eRPAssetTransferDetailsRefferedback = $this->eRPAssetTransferDetailsRefferedbackRepository->create($input);

        return $this->sendResponse($eRPAssetTransferDetailsRefferedback->toArray(), trans('custom.e_r_p_asset_transfer_details_refferedback_saved_su'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/eRPAssetTransferDetailsRefferedbacks/{id}",
     *      summary="Display the specified ERPAssetTransferDetailsRefferedback",
     *      tags={"ERPAssetTransferDetailsRefferedback"},
     *      description="Get ERPAssetTransferDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ERPAssetTransferDetailsRefferedback",
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
     *                  ref="#/definitions/ERPAssetTransferDetailsRefferedback"
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
        /** @var ERPAssetTransferDetailsRefferedback $eRPAssetTransferDetailsRefferedback */
        $eRPAssetTransferDetailsRefferedback = $this->eRPAssetTransferDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($eRPAssetTransferDetailsRefferedback)) {
            return $this->sendError(trans('custom.e_r_p_asset_transfer_details_refferedback_not_foun'));
        }

        return $this->sendResponse($eRPAssetTransferDetailsRefferedback->toArray(), trans('custom.e_r_p_asset_transfer_details_refferedback_retrieve'));
    }

    /**
     * @param int $id
     * @param UpdateERPAssetTransferDetailsRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/eRPAssetTransferDetailsRefferedbacks/{id}",
     *      summary="Update the specified ERPAssetTransferDetailsRefferedback in storage",
     *      tags={"ERPAssetTransferDetailsRefferedback"},
     *      description="Update ERPAssetTransferDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ERPAssetTransferDetailsRefferedback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ERPAssetTransferDetailsRefferedback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ERPAssetTransferDetailsRefferedback")
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
     *                  ref="#/definitions/ERPAssetTransferDetailsRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateERPAssetTransferDetailsRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var ERPAssetTransferDetailsRefferedback $eRPAssetTransferDetailsRefferedback */
        $eRPAssetTransferDetailsRefferedback = $this->eRPAssetTransferDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($eRPAssetTransferDetailsRefferedback)) {
            return $this->sendError(trans('custom.e_r_p_asset_transfer_details_refferedback_not_foun'));
        }

        $eRPAssetTransferDetailsRefferedback = $this->eRPAssetTransferDetailsRefferedbackRepository->update($input, $id);

        return $this->sendResponse($eRPAssetTransferDetailsRefferedback->toArray(), trans('custom.erpassettransferdetailsrefferedback_updated_succes'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/eRPAssetTransferDetailsRefferedbacks/{id}",
     *      summary="Remove the specified ERPAssetTransferDetailsRefferedback from storage",
     *      tags={"ERPAssetTransferDetailsRefferedback"},
     *      description="Delete ERPAssetTransferDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ERPAssetTransferDetailsRefferedback",
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
        /** @var ERPAssetTransferDetailsRefferedback $eRPAssetTransferDetailsRefferedback */
        $eRPAssetTransferDetailsRefferedback = $this->eRPAssetTransferDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($eRPAssetTransferDetailsRefferedback)) {
            return $this->sendError(trans('custom.e_r_p_asset_transfer_details_refferedback_not_foun'));
        }

        $eRPAssetTransferDetailsRefferedback->delete();

        return $this->sendSuccess('E R P Asset Transfer Details Refferedback deleted successfully');
    }

    public function get_employee_asset_transfer_details_amend($id){ 
        $data['assetMaster'] = AssetTransferReferredback::where('assetTransferMasterRefferedBackID', $id)->first();
        $timesReferred = $data['assetMaster']->timesReferred;
        $assetTransferID = $data['assetMaster']->id;
        if ($data['assetMaster']->type == 1) {
            $data['assetRequestDetails'] = ERPAssetTransferDetailsRefferedback::with(['assetRequestDetail', 'assetMaster','assetRequestMaster'])
            ->where('erp_fa_fa_asset_transfer_id', $assetTransferID)
            ->where('timesReferred', $timesReferred)
            ->get();
        } else {
            $data['assetRequestDetails'] = ERPAssetTransferDetailsRefferedback::with(['fromLocation', 'toLocation', 'assetMaster'])
            ->where('erp_fa_fa_asset_transfer_id', $assetTransferID)
            ->where('timesReferred', $timesReferred)
            ->get();
        }
        return $this->sendResponse($data, 'Asset Transfer Referredback data details');
    }
}
