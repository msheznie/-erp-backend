<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetDisposalMasterAPIRequest;
use App\Http\Requests\API\UpdateAssetDisposalMasterAPIRequest;
use App\Models\AssetDisposalMaster;
use App\Repositories\AssetDisposalMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetDisposalMasterController
 * @package App\Http\Controllers\API
 */

class AssetDisposalMasterAPIController extends AppBaseController
{
    /** @var  AssetDisposalMasterRepository */
    private $assetDisposalMasterRepository;

    public function __construct(AssetDisposalMasterRepository $assetDisposalMasterRepo)
    {
        $this->assetDisposalMasterRepository = $assetDisposalMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetDisposalMasters",
     *      summary="Get a listing of the AssetDisposalMasters.",
     *      tags={"AssetDisposalMaster"},
     *      description="Get all AssetDisposalMasters",
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
     *                  @SWG\Items(ref="#/definitions/AssetDisposalMaster")
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
        $this->assetDisposalMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->assetDisposalMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetDisposalMasters = $this->assetDisposalMasterRepository->all();

        return $this->sendResponse($assetDisposalMasters->toArray(), 'Asset Disposal Masters retrieved successfully');
    }

    /**
     * @param CreateAssetDisposalMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetDisposalMasters",
     *      summary="Store a newly created AssetDisposalMaster in storage",
     *      tags={"AssetDisposalMaster"},
     *      description="Store AssetDisposalMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetDisposalMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetDisposalMaster")
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
     *                  ref="#/definitions/AssetDisposalMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetDisposalMasterAPIRequest $request)
    {
        $input = $request->all();

        $assetDisposalMasters = $this->assetDisposalMasterRepository->create($input);

        return $this->sendResponse($assetDisposalMasters->toArray(), 'Asset Disposal Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetDisposalMasters/{id}",
     *      summary="Display the specified AssetDisposalMaster",
     *      tags={"AssetDisposalMaster"},
     *      description="Get AssetDisposalMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalMaster",
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
     *                  ref="#/definitions/AssetDisposalMaster"
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
        /** @var AssetDisposalMaster $assetDisposalMaster */
        $assetDisposalMaster = $this->assetDisposalMasterRepository->findWithoutFail($id);

        if (empty($assetDisposalMaster)) {
            return $this->sendError('Asset Disposal Master not found');
        }

        return $this->sendResponse($assetDisposalMaster->toArray(), 'Asset Disposal Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAssetDisposalMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetDisposalMasters/{id}",
     *      summary="Update the specified AssetDisposalMaster in storage",
     *      tags={"AssetDisposalMaster"},
     *      description="Update AssetDisposalMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetDisposalMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetDisposalMaster")
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
     *                  ref="#/definitions/AssetDisposalMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetDisposalMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetDisposalMaster $assetDisposalMaster */
        $assetDisposalMaster = $this->assetDisposalMasterRepository->findWithoutFail($id);

        if (empty($assetDisposalMaster)) {
            return $this->sendError('Asset Disposal Master not found');
        }

        $assetDisposalMaster = $this->assetDisposalMasterRepository->update($input, $id);

        return $this->sendResponse($assetDisposalMaster->toArray(), 'AssetDisposalMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetDisposalMasters/{id}",
     *      summary="Remove the specified AssetDisposalMaster from storage",
     *      tags={"AssetDisposalMaster"},
     *      description="Delete AssetDisposalMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalMaster",
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
        /** @var AssetDisposalMaster $assetDisposalMaster */
        $assetDisposalMaster = $this->assetDisposalMasterRepository->findWithoutFail($id);

        if (empty($assetDisposalMaster)) {
            return $this->sendError('Asset Disposal Master not found');
        }

        $assetDisposalMaster->delete();

        return $this->sendResponse($id, 'Asset Disposal Master deleted successfully');
    }
}
