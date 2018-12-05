<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateJvDetailsReferredbackAPIRequest;
use App\Http\Requests\API\UpdateJvDetailsReferredbackAPIRequest;
use App\Models\JvDetailsReferredback;
use App\Repositories\JvDetailsReferredbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class JvDetailsReferredbackController
 * @package App\Http\Controllers\API
 */

class JvDetailsReferredbackAPIController extends AppBaseController
{
    /** @var  JvDetailsReferredbackRepository */
    private $jvDetailsReferredbackRepository;

    public function __construct(JvDetailsReferredbackRepository $jvDetailsReferredbackRepo)
    {
        $this->jvDetailsReferredbackRepository = $jvDetailsReferredbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/jvDetailsReferredbacks",
     *      summary="Get a listing of the JvDetailsReferredbacks.",
     *      tags={"JvDetailsReferredback"},
     *      description="Get all JvDetailsReferredbacks",
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
     *                  @SWG\Items(ref="#/definitions/JvDetailsReferredback")
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
        $this->jvDetailsReferredbackRepository->pushCriteria(new RequestCriteria($request));
        $this->jvDetailsReferredbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $jvDetailsReferredbacks = $this->jvDetailsReferredbackRepository->all();

        return $this->sendResponse($jvDetailsReferredbacks->toArray(), 'Jv Details Referredbacks retrieved successfully');
    }

    /**
     * @param CreateJvDetailsReferredbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/jvDetailsReferredbacks",
     *      summary="Store a newly created JvDetailsReferredback in storage",
     *      tags={"JvDetailsReferredback"},
     *      description="Store JvDetailsReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JvDetailsReferredback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/JvDetailsReferredback")
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
     *                  ref="#/definitions/JvDetailsReferredback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateJvDetailsReferredbackAPIRequest $request)
    {
        $input = $request->all();

        $jvDetailsReferredbacks = $this->jvDetailsReferredbackRepository->create($input);

        return $this->sendResponse($jvDetailsReferredbacks->toArray(), 'Jv Details Referredback saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/jvDetailsReferredbacks/{id}",
     *      summary="Display the specified JvDetailsReferredback",
     *      tags={"JvDetailsReferredback"},
     *      description="Get JvDetailsReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JvDetailsReferredback",
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
     *                  ref="#/definitions/JvDetailsReferredback"
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
        /** @var JvDetailsReferredback $jvDetailsReferredback */
        $jvDetailsReferredback = $this->jvDetailsReferredbackRepository->findWithoutFail($id);

        if (empty($jvDetailsReferredback)) {
            return $this->sendError('Jv Details Referredback not found');
        }

        return $this->sendResponse($jvDetailsReferredback->toArray(), 'Jv Details Referredback retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateJvDetailsReferredbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/jvDetailsReferredbacks/{id}",
     *      summary="Update the specified JvDetailsReferredback in storage",
     *      tags={"JvDetailsReferredback"},
     *      description="Update JvDetailsReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JvDetailsReferredback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JvDetailsReferredback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/JvDetailsReferredback")
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
     *                  ref="#/definitions/JvDetailsReferredback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateJvDetailsReferredbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var JvDetailsReferredback $jvDetailsReferredback */
        $jvDetailsReferredback = $this->jvDetailsReferredbackRepository->findWithoutFail($id);

        if (empty($jvDetailsReferredback)) {
            return $this->sendError('Jv Details Referredback not found');
        }

        $jvDetailsReferredback = $this->jvDetailsReferredbackRepository->update($input, $id);

        return $this->sendResponse($jvDetailsReferredback->toArray(), 'JvDetailsReferredback updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/jvDetailsReferredbacks/{id}",
     *      summary="Remove the specified JvDetailsReferredback from storage",
     *      tags={"JvDetailsReferredback"},
     *      description="Delete JvDetailsReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JvDetailsReferredback",
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
        /** @var JvDetailsReferredback $jvDetailsReferredback */
        $jvDetailsReferredback = $this->jvDetailsReferredbackRepository->findWithoutFail($id);

        if (empty($jvDetailsReferredback)) {
            return $this->sendError('Jv Details Referredback not found');
        }

        $jvDetailsReferredback->delete();

        return $this->sendResponse($id, 'Jv Details Referredback deleted successfully');
    }
}
