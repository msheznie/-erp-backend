<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSystemJobsAPIRequest;
use App\Http\Requests\API\UpdateSystemJobsAPIRequest;
use App\Models\SystemJobs;
use App\Repositories\SystemJobsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SystemJobsController
 * @package App\Http\Controllers\API
 */

class SystemJobsAPIController extends AppBaseController
{
    /** @var  SystemJobsRepository */
    private $systemJobsRepository;

    public function __construct(SystemJobsRepository $systemJobsRepo)
    {
        $this->systemJobsRepository = $systemJobsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/systemJobs",
     *      summary="Get a listing of the SystemJobs.",
     *      tags={"SystemJobs"},
     *      description="Get all SystemJobs",
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
     *                  @SWG\Items(ref="#/definitions/SystemJobs")
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
        $this->systemJobsRepository->pushCriteria(new RequestCriteria($request));
        $this->systemJobsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $systemJobs = $this->systemJobsRepository->all();

        return $this->sendResponse($systemJobs->toArray(), trans('custom.system_jobs_retrieved_successfully'));
    }

    /**
     * @param CreateSystemJobsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/systemJobs",
     *      summary="Store a newly created SystemJobs in storage",
     *      tags={"SystemJobs"},
     *      description="Store SystemJobs",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SystemJobs that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SystemJobs")
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
     *                  ref="#/definitions/SystemJobs"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSystemJobsAPIRequest $request)
    {
        $input = $request->all();

        $systemJobs = $this->systemJobsRepository->create($input);

        return $this->sendResponse($systemJobs->toArray(), trans('custom.system_jobs_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/systemJobs/{id}",
     *      summary="Display the specified SystemJobs",
     *      tags={"SystemJobs"},
     *      description="Get SystemJobs",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SystemJobs",
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
     *                  ref="#/definitions/SystemJobs"
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
        /** @var SystemJobs $systemJobs */
        $systemJobs = $this->systemJobsRepository->findWithoutFail($id);

        if (empty($systemJobs)) {
            return $this->sendError(trans('custom.system_jobs_not_found'));
        }

        return $this->sendResponse($systemJobs->toArray(), trans('custom.system_jobs_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSystemJobsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/systemJobs/{id}",
     *      summary="Update the specified SystemJobs in storage",
     *      tags={"SystemJobs"},
     *      description="Update SystemJobs",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SystemJobs",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SystemJobs that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SystemJobs")
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
     *                  ref="#/definitions/SystemJobs"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSystemJobsAPIRequest $request)
    {
        $input = $request->all();

        /** @var SystemJobs $systemJobs */
        $systemJobs = $this->systemJobsRepository->findWithoutFail($id);

        if (empty($systemJobs)) {
            return $this->sendError(trans('custom.system_jobs_not_found'));
        }

        $systemJobs = $this->systemJobsRepository->update($input, $id);

        return $this->sendResponse($systemJobs->toArray(), trans('custom.systemjobs_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/systemJobs/{id}",
     *      summary="Remove the specified SystemJobs from storage",
     *      tags={"SystemJobs"},
     *      description="Delete SystemJobs",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SystemJobs",
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
        /** @var SystemJobs $systemJobs */
        $systemJobs = $this->systemJobsRepository->findWithoutFail($id);

        if (empty($systemJobs)) {
            return $this->sendError(trans('custom.system_jobs_not_found'));
        }

        $systemJobs->delete();

        return $this->sendSuccess('System Jobs deleted successfully');
    }
}
