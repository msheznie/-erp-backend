<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePoCutoffJobDataAPIRequest;
use App\Http\Requests\API\UpdatePoCutoffJobDataAPIRequest;
use App\Models\PoCutoffJobData;
use App\Repositories\PoCutoffJobDataRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PoCutoffJobDataController
 * @package App\Http\Controllers\API
 */

class PoCutoffJobDataAPIController extends AppBaseController
{
    /** @var  PoCutoffJobDataRepository */
    private $poCutoffJobDataRepository;

    public function __construct(PoCutoffJobDataRepository $poCutoffJobDataRepo)
    {
        $this->poCutoffJobDataRepository = $poCutoffJobDataRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/poCutoffJobDatas",
     *      summary="getPoCutoffJobDataList",
     *      tags={"PoCutoffJobData"},
     *      description="Get all PoCutoffJobDatas",
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
     *                  @OA\Items(ref="#/definitions/PoCutoffJobData")
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
        $this->poCutoffJobDataRepository->pushCriteria(new RequestCriteria($request));
        $this->poCutoffJobDataRepository->pushCriteria(new LimitOffsetCriteria($request));
        $poCutoffJobDatas = $this->poCutoffJobDataRepository->all();

        return $this->sendResponse($poCutoffJobDatas->toArray(), trans('custom.po_cutoff_job_datas_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/poCutoffJobDatas",
     *      summary="createPoCutoffJobData",
     *      tags={"PoCutoffJobData"},
     *      description="Create PoCutoffJobData",
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
     *                  ref="#/definitions/PoCutoffJobData"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePoCutoffJobDataAPIRequest $request)
    {
        $input = $request->all();

        $poCutoffJobData = $this->poCutoffJobDataRepository->create($input);

        return $this->sendResponse($poCutoffJobData->toArray(), trans('custom.po_cutoff_job_data_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/poCutoffJobDatas/{id}",
     *      summary="getPoCutoffJobDataItem",
     *      tags={"PoCutoffJobData"},
     *      description="Get PoCutoffJobData",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PoCutoffJobData",
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
     *                  ref="#/definitions/PoCutoffJobData"
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
        /** @var PoCutoffJobData $poCutoffJobData */
        $poCutoffJobData = $this->poCutoffJobDataRepository->findWithoutFail($id);

        if (empty($poCutoffJobData)) {
            return $this->sendError(trans('custom.po_cutoff_job_data_not_found'));
        }

        return $this->sendResponse($poCutoffJobData->toArray(), trans('custom.po_cutoff_job_data_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/poCutoffJobDatas/{id}",
     *      summary="updatePoCutoffJobData",
     *      tags={"PoCutoffJobData"},
     *      description="Update PoCutoffJobData",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PoCutoffJobData",
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
     *                  ref="#/definitions/PoCutoffJobData"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePoCutoffJobDataAPIRequest $request)
    {
        $input = $request->all();

        /** @var PoCutoffJobData $poCutoffJobData */
        $poCutoffJobData = $this->poCutoffJobDataRepository->findWithoutFail($id);

        if (empty($poCutoffJobData)) {
            return $this->sendError(trans('custom.po_cutoff_job_data_not_found'));
        }

        $poCutoffJobData = $this->poCutoffJobDataRepository->update($input, $id);

        return $this->sendResponse($poCutoffJobData->toArray(), trans('custom.pocutoffjobdata_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/poCutoffJobDatas/{id}",
     *      summary="deletePoCutoffJobData",
     *      tags={"PoCutoffJobData"},
     *      description="Delete PoCutoffJobData",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PoCutoffJobData",
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
        /** @var PoCutoffJobData $poCutoffJobData */
        $poCutoffJobData = $this->poCutoffJobDataRepository->findWithoutFail($id);

        if (empty($poCutoffJobData)) {
            return $this->sendError(trans('custom.po_cutoff_job_data_not_found'));
        }

        $poCutoffJobData->delete();

        return $this->sendSuccess('Po Cutoff Job Data deleted successfully');
    }
}
