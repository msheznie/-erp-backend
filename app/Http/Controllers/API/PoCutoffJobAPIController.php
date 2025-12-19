<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePoCutoffJobAPIRequest;
use App\Http\Requests\API\UpdatePoCutoffJobAPIRequest;
use App\Models\PoCutoffJob;
use App\Repositories\PoCutoffJobRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PoCutoffJobController
 * @package App\Http\Controllers\API
 */

class PoCutoffJobAPIController extends AppBaseController
{
    /** @var  PoCutoffJobRepository */
    private $poCutoffJobRepository;

    public function __construct(PoCutoffJobRepository $poCutoffJobRepo)
    {
        $this->poCutoffJobRepository = $poCutoffJobRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/poCutoffJobs",
     *      summary="getPoCutoffJobList",
     *      tags={"PoCutoffJob"},
     *      description="Get all PoCutoffJobs",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/PoCutoffJob")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->poCutoffJobRepository->pushCriteria(new RequestCriteria($request));
        $this->poCutoffJobRepository->pushCriteria(new LimitOffsetCriteria($request));
        $poCutoffJobs = $this->poCutoffJobRepository->all();

        return $this->sendResponse($poCutoffJobs->toArray(), trans('custom.po_cutoff_jobs_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/poCutoffJobs",
     *      summary="createPoCutoffJob",
     *      tags={"PoCutoffJob"},
     *      description="Create PoCutoffJob",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/PoCutoffJob"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePoCutoffJobAPIRequest $request)
    {
        $input = $request->all();

        $poCutoffJob = $this->poCutoffJobRepository->create($input);

        return $this->sendResponse($poCutoffJob->toArray(), trans('custom.po_cutoff_job_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/poCutoffJobs/{id}",
     *      summary="getPoCutoffJobItem",
     *      tags={"PoCutoffJob"},
     *      description="Get PoCutoffJob",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PoCutoffJob",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/PoCutoffJob"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var PoCutoffJob $poCutoffJob */
        $poCutoffJob = $this->poCutoffJobRepository->findWithoutFail($id);

        if (empty($poCutoffJob)) {
            return $this->sendError(trans('custom.po_cutoff_job_not_found'));
        }

        return $this->sendResponse($poCutoffJob->toArray(), trans('custom.po_cutoff_job_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/poCutoffJobs/{id}",
     *      summary="updatePoCutoffJob",
     *      tags={"PoCutoffJob"},
     *      description="Update PoCutoffJob",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PoCutoffJob",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/PoCutoffJob"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePoCutoffJobAPIRequest $request)
    {
        $input = $request->all();

        /** @var PoCutoffJob $poCutoffJob */
        $poCutoffJob = $this->poCutoffJobRepository->findWithoutFail($id);

        if (empty($poCutoffJob)) {
            return $this->sendError(trans('custom.po_cutoff_job_not_found'));
        }

        $poCutoffJob = $this->poCutoffJobRepository->update($input, $id);

        return $this->sendResponse($poCutoffJob->toArray(), trans('custom.pocutoffjob_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/poCutoffJobs/{id}",
     *      summary="deletePoCutoffJob",
     *      tags={"PoCutoffJob"},
     *      description="Delete PoCutoffJob",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PoCutoffJob",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var PoCutoffJob $poCutoffJob */
        $poCutoffJob = $this->poCutoffJobRepository->findWithoutFail($id);

        if (empty($poCutoffJob)) {
            return $this->sendError(trans('custom.po_cutoff_job_not_found'));
        }

        $poCutoffJob->delete();

        return $this->sendSuccess('Po Cutoff Job deleted successfully');
    }
}
