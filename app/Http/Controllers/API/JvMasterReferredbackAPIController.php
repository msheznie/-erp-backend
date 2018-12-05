<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateJvMasterReferredbackAPIRequest;
use App\Http\Requests\API\UpdateJvMasterReferredbackAPIRequest;
use App\Models\JvMasterReferredback;
use App\Repositories\JvMasterReferredbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class JvMasterReferredbackController
 * @package App\Http\Controllers\API
 */

class JvMasterReferredbackAPIController extends AppBaseController
{
    /** @var  JvMasterReferredbackRepository */
    private $jvMasterReferredbackRepository;

    public function __construct(JvMasterReferredbackRepository $jvMasterReferredbackRepo)
    {
        $this->jvMasterReferredbackRepository = $jvMasterReferredbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/jvMasterReferredbacks",
     *      summary="Get a listing of the JvMasterReferredbacks.",
     *      tags={"JvMasterReferredback"},
     *      description="Get all JvMasterReferredbacks",
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
     *                  @SWG\Items(ref="#/definitions/JvMasterReferredback")
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
        $this->jvMasterReferredbackRepository->pushCriteria(new RequestCriteria($request));
        $this->jvMasterReferredbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $jvMasterReferredbacks = $this->jvMasterReferredbackRepository->all();

        return $this->sendResponse($jvMasterReferredbacks->toArray(), 'Jv Master Referredbacks retrieved successfully');
    }

    /**
     * @param CreateJvMasterReferredbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/jvMasterReferredbacks",
     *      summary="Store a newly created JvMasterReferredback in storage",
     *      tags={"JvMasterReferredback"},
     *      description="Store JvMasterReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JvMasterReferredback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/JvMasterReferredback")
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
     *                  ref="#/definitions/JvMasterReferredback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateJvMasterReferredbackAPIRequest $request)
    {
        $input = $request->all();

        $jvMasterReferredbacks = $this->jvMasterReferredbackRepository->create($input);

        return $this->sendResponse($jvMasterReferredbacks->toArray(), 'Jv Master Referredback saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/jvMasterReferredbacks/{id}",
     *      summary="Display the specified JvMasterReferredback",
     *      tags={"JvMasterReferredback"},
     *      description="Get JvMasterReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JvMasterReferredback",
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
     *                  ref="#/definitions/JvMasterReferredback"
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
        /** @var JvMasterReferredback $jvMasterReferredback */
        $jvMasterReferredback = $this->jvMasterReferredbackRepository->findWithoutFail($id);

        if (empty($jvMasterReferredback)) {
            return $this->sendError('Jv Master Referredback not found');
        }

        return $this->sendResponse($jvMasterReferredback->toArray(), 'Jv Master Referredback retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateJvMasterReferredbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/jvMasterReferredbacks/{id}",
     *      summary="Update the specified JvMasterReferredback in storage",
     *      tags={"JvMasterReferredback"},
     *      description="Update JvMasterReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JvMasterReferredback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JvMasterReferredback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/JvMasterReferredback")
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
     *                  ref="#/definitions/JvMasterReferredback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateJvMasterReferredbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var JvMasterReferredback $jvMasterReferredback */
        $jvMasterReferredback = $this->jvMasterReferredbackRepository->findWithoutFail($id);

        if (empty($jvMasterReferredback)) {
            return $this->sendError('Jv Master Referredback not found');
        }

        $jvMasterReferredback = $this->jvMasterReferredbackRepository->update($input, $id);

        return $this->sendResponse($jvMasterReferredback->toArray(), 'JvMasterReferredback updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/jvMasterReferredbacks/{id}",
     *      summary="Remove the specified JvMasterReferredback from storage",
     *      tags={"JvMasterReferredback"},
     *      description="Delete JvMasterReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JvMasterReferredback",
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
        /** @var JvMasterReferredback $jvMasterReferredback */
        $jvMasterReferredback = $this->jvMasterReferredbackRepository->findWithoutFail($id);

        if (empty($jvMasterReferredback)) {
            return $this->sendError('Jv Master Referredback not found');
        }

        $jvMasterReferredback->delete();

        return $this->sendResponse($id, 'Jv Master Referredback deleted successfully');
    }
}
