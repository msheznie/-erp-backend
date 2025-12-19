<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProcumentActivityAPIRequest;
use App\Http\Requests\API\UpdateProcumentActivityAPIRequest;
use App\Models\ProcumentActivity;
use App\Repositories\ProcumentActivityRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ProcumentActivityController
 * @package App\Http\Controllers\API
 */

class ProcumentActivityAPIController extends AppBaseController
{
    /** @var  ProcumentActivityRepository */
    private $procumentActivityRepository;

    public function __construct(ProcumentActivityRepository $procumentActivityRepo)
    {
        $this->procumentActivityRepository = $procumentActivityRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/procumentActivities",
     *      summary="Get a listing of the ProcumentActivities.",
     *      tags={"ProcumentActivity"},
     *      description="Get all ProcumentActivities",
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
     *                  @SWG\Items(ref="#/definitions/ProcumentActivity")
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
        $this->procumentActivityRepository->pushCriteria(new RequestCriteria($request));
        $this->procumentActivityRepository->pushCriteria(new LimitOffsetCriteria($request));
        $procumentActivities = $this->procumentActivityRepository->all();

        return $this->sendResponse($procumentActivities->toArray(), trans('custom.procument_activities_retrieved_successfully'));
    }

    /**
     * @param CreateProcumentActivityAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/procumentActivities",
     *      summary="Store a newly created ProcumentActivity in storage",
     *      tags={"ProcumentActivity"},
     *      description="Store ProcumentActivity",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ProcumentActivity that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ProcumentActivity")
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
     *                  ref="#/definitions/ProcumentActivity"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateProcumentActivityAPIRequest $request)
    {
        $input = $request->all();

        $procumentActivity = $this->procumentActivityRepository->create($input);

        return $this->sendResponse($procumentActivity->toArray(), trans('custom.procument_activity_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/procumentActivities/{id}",
     *      summary="Display the specified ProcumentActivity",
     *      tags={"ProcumentActivity"},
     *      description="Get ProcumentActivity",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ProcumentActivity",
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
     *                  ref="#/definitions/ProcumentActivity"
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
        /** @var ProcumentActivity $procumentActivity */
        $procumentActivity = $this->procumentActivityRepository->findWithoutFail($id);

        if (empty($procumentActivity)) {
            return $this->sendError(trans('custom.procument_activity_not_found'));
        }

        return $this->sendResponse($procumentActivity->toArray(), trans('custom.procument_activity_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateProcumentActivityAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/procumentActivities/{id}",
     *      summary="Update the specified ProcumentActivity in storage",
     *      tags={"ProcumentActivity"},
     *      description="Update ProcumentActivity",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ProcumentActivity",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ProcumentActivity that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ProcumentActivity")
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
     *                  ref="#/definitions/ProcumentActivity"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateProcumentActivityAPIRequest $request)
    {
        $input = $request->all();

        /** @var ProcumentActivity $procumentActivity */
        $procumentActivity = $this->procumentActivityRepository->findWithoutFail($id);

        if (empty($procumentActivity)) {
            return $this->sendError(trans('custom.procument_activity_not_found'));
        }

        $procumentActivity = $this->procumentActivityRepository->update($input, $id);

        return $this->sendResponse($procumentActivity->toArray(), trans('custom.procumentactivity_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/procumentActivities/{id}",
     *      summary="Remove the specified ProcumentActivity from storage",
     *      tags={"ProcumentActivity"},
     *      description="Delete ProcumentActivity",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ProcumentActivity",
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
        /** @var ProcumentActivity $procumentActivity */
        $procumentActivity = $this->procumentActivityRepository->findWithoutFail($id);

        if (empty($procumentActivity)) {
            return $this->sendError(trans('custom.procument_activity_not_found'));
        }

        $procumentActivity->delete();

        return $this->sendSuccess('Procument Activity deleted successfully');
    }
}
