<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFixedAssetMasterAPIRequest;
use App\Http\Requests\API\UpdateFixedAssetMasterAPIRequest;
use App\Models\FixedAssetMaster;
use App\Repositories\FixedAssetMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FixedAssetMasterController
 * @package App\Http\Controllers\API
 */

class FixedAssetMasterAPIController extends AppBaseController
{
    /** @var  FixedAssetMasterRepository */
    private $fixedAssetMasterRepository;

    public function __construct(FixedAssetMasterRepository $fixedAssetMasterRepo)
    {
        $this->fixedAssetMasterRepository = $fixedAssetMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetMasters",
     *      summary="Get a listing of the FixedAssetMasters.",
     *      tags={"FixedAssetMaster"},
     *      description="Get all FixedAssetMasters",
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
     *                  @SWG\Items(ref="#/definitions/FixedAssetMaster")
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
        $this->fixedAssetMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->fixedAssetMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fixedAssetMasters = $this->fixedAssetMasterRepository->all();

        return $this->sendResponse($fixedAssetMasters->toArray(), 'Fixed Asset Masters retrieved successfully');
    }

    /**
     * @param CreateFixedAssetMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/fixedAssetMasters",
     *      summary="Store a newly created FixedAssetMaster in storage",
     *      tags={"FixedAssetMaster"},
     *      description="Store FixedAssetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetMaster")
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
     *                  ref="#/definitions/FixedAssetMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFixedAssetMasterAPIRequest $request)
    {
        $input = $request->all();

        $fixedAssetMasters = $this->fixedAssetMasterRepository->create($input);

        return $this->sendResponse($fixedAssetMasters->toArray(), 'Fixed Asset Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetMasters/{id}",
     *      summary="Display the specified FixedAssetMaster",
     *      tags={"FixedAssetMaster"},
     *      description="Get FixedAssetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetMaster",
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
     *                  ref="#/definitions/FixedAssetMaster"
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
        /** @var FixedAssetMaster $fixedAssetMaster */
        $fixedAssetMaster = $this->fixedAssetMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetMaster)) {
            return $this->sendError('Fixed Asset Master not found');
        }

        return $this->sendResponse($fixedAssetMaster->toArray(), 'Fixed Asset Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateFixedAssetMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fixedAssetMasters/{id}",
     *      summary="Update the specified FixedAssetMaster in storage",
     *      tags={"FixedAssetMaster"},
     *      description="Update FixedAssetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetMaster")
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
     *                  ref="#/definitions/FixedAssetMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFixedAssetMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var FixedAssetMaster $fixedAssetMaster */
        $fixedAssetMaster = $this->fixedAssetMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetMaster)) {
            return $this->sendError('Fixed Asset Master not found');
        }

        $fixedAssetMaster = $this->fixedAssetMasterRepository->update($input, $id);

        return $this->sendResponse($fixedAssetMaster->toArray(), 'FixedAssetMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fixedAssetMasters/{id}",
     *      summary="Remove the specified FixedAssetMaster from storage",
     *      tags={"FixedAssetMaster"},
     *      description="Delete FixedAssetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetMaster",
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
        /** @var FixedAssetMaster $fixedAssetMaster */
        $fixedAssetMaster = $this->fixedAssetMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetMaster)) {
            return $this->sendError('Fixed Asset Master not found');
        }

        $fixedAssetMaster->delete();

        return $this->sendResponse($id, 'Fixed Asset Master deleted successfully');
    }
}
