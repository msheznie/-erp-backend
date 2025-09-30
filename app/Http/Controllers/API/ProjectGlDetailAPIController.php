<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProjectGlDetailAPIRequest;
use App\Http\Requests\API\UpdateProjectGlDetailAPIRequest;
use App\Models\ProjectGlDetail;
use App\Repositories\ProjectGlDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ProjectGlDetailController
 * @package App\Http\Controllers\API
 */

class ProjectGlDetailAPIController extends AppBaseController
{
    /** @var  ProjectGlDetailRepository */
    private $projectGlDetailRepository;

    public function __construct(ProjectGlDetailRepository $projectGlDetailRepo)
    {
        $this->projectGlDetailRepository = $projectGlDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/projectGlDetails",
     *      summary="Get a listing of the ProjectGlDetails.",
     *      tags={"ProjectGlDetail"},
     *      description="Get all ProjectGlDetails",
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
     *                  @SWG\Items(ref="#/definitions/ProjectGlDetail")
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
        $this->projectGlDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->projectGlDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $projectGlDetails = $this->projectGlDetailRepository->all();

        return $this->sendResponse($projectGlDetails->toArray(), trans('custom.project_gl_details_retrieved_successfully'));
    }

    /**
     * @param CreateProjectGlDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/projectGlDetails",
     *      summary="Store a newly created ProjectGlDetail in storage",
     *      tags={"ProjectGlDetail"},
     *      description="Store ProjectGlDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ProjectGlDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ProjectGlDetail")
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
     *                  ref="#/definitions/ProjectGlDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateProjectGlDetailAPIRequest $request)
    {
        $input = $request->all();

        $projectGlDetail = $this->projectGlDetailRepository->create($input);

        return $this->sendResponse($projectGlDetail->toArray(), trans('custom.project_gl_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/projectGlDetails/{id}",
     *      summary="Display the specified ProjectGlDetail",
     *      tags={"ProjectGlDetail"},
     *      description="Get ProjectGlDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ProjectGlDetail",
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
     *                  ref="#/definitions/ProjectGlDetail"
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
        /** @var ProjectGlDetail $projectGlDetail */
        $projectGlDetail = $this->projectGlDetailRepository->findWithoutFail($id);

        if (empty($projectGlDetail)) {
            return $this->sendError(trans('custom.project_gl_detail_not_found'));
        }

        return $this->sendResponse($projectGlDetail->toArray(), trans('custom.project_gl_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateProjectGlDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/projectGlDetails/{id}",
     *      summary="Update the specified ProjectGlDetail in storage",
     *      tags={"ProjectGlDetail"},
     *      description="Update ProjectGlDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ProjectGlDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ProjectGlDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ProjectGlDetail")
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
     *                  ref="#/definitions/ProjectGlDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateProjectGlDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var ProjectGlDetail $projectGlDetail */
        $projectGlDetail = $this->projectGlDetailRepository->findWithoutFail($id);

        if (empty($projectGlDetail)) {
            return $this->sendError(trans('custom.project_gl_detail_not_found'));
        }

        $projectGlDetail = $this->projectGlDetailRepository->update($input, $id);

        return $this->sendResponse($projectGlDetail->toArray(), trans('custom.projectgldetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/projectGlDetails/{id}",
     *      summary="Remove the specified ProjectGlDetail from storage",
     *      tags={"ProjectGlDetail"},
     *      description="Delete ProjectGlDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ProjectGlDetail",
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
        /** @var ProjectGlDetail $projectGlDetail */
        $projectGlDetail = $this->projectGlDetailRepository->findWithoutFail($id);

        if (empty($projectGlDetail)) {
            return $this->sendError(trans('custom.project_gl_detail_not_found'));
        }

        $projectGlDetail->delete();

        // return $this->sendSuccess(trans('custom.project_gl_detail_deleted_successfully'));
        return $this->sendResponse([], trans('custom.project_gl_detail_deleted_successfully'));
    }
}
