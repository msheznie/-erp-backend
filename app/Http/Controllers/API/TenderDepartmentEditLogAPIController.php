<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderDepartmentEditLogAPIRequest;
use App\Http\Requests\API\UpdateTenderDepartmentEditLogAPIRequest;
use App\Models\TenderDepartmentEditLog;
use App\Repositories\TenderDepartmentEditLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderDepartmentEditLogController
 * @package App\Http\Controllers\API
 */

class TenderDepartmentEditLogAPIController extends AppBaseController
{
    /** @var  TenderDepartmentEditLogRepository */
    private $tenderDepartmentEditLogRepository;

    public function __construct(TenderDepartmentEditLogRepository $tenderDepartmentEditLogRepo)
    {
        $this->tenderDepartmentEditLogRepository = $tenderDepartmentEditLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderDepartmentEditLogs",
     *      summary="getTenderDepartmentEditLogList",
     *      tags={"TenderDepartmentEditLog"},
     *      description="Get all TenderDepartmentEditLogs",
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
     *                  @OA\Items(ref="#/definitions/TenderDepartmentEditLog")
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
        $this->tenderDepartmentEditLogRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderDepartmentEditLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderDepartmentEditLogs = $this->tenderDepartmentEditLogRepository->all();

        return $this->sendResponse($tenderDepartmentEditLogs->toArray(), 'Tender Department Edit Logs retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/tenderDepartmentEditLogs",
     *      summary="createTenderDepartmentEditLog",
     *      tags={"TenderDepartmentEditLog"},
     *      description="Create TenderDepartmentEditLog",
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
     *                  ref="#/definitions/TenderDepartmentEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderDepartmentEditLogAPIRequest $request)
    {
        $input = $request->all();

        $tenderDepartmentEditLog = $this->tenderDepartmentEditLogRepository->create($input);

        return $this->sendResponse($tenderDepartmentEditLog->toArray(), 'Tender Department Edit Log saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderDepartmentEditLogs/{id}",
     *      summary="getTenderDepartmentEditLogItem",
     *      tags={"TenderDepartmentEditLog"},
     *      description="Get TenderDepartmentEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderDepartmentEditLog",
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
     *                  ref="#/definitions/TenderDepartmentEditLog"
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
        /** @var TenderDepartmentEditLog $tenderDepartmentEditLog */
        $tenderDepartmentEditLog = $this->tenderDepartmentEditLogRepository->findWithoutFail($id);

        if (empty($tenderDepartmentEditLog)) {
            return $this->sendError('Tender Department Edit Log not found');
        }

        return $this->sendResponse($tenderDepartmentEditLog->toArray(), 'Tender Department Edit Log retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/tenderDepartmentEditLogs/{id}",
     *      summary="updateTenderDepartmentEditLog",
     *      tags={"TenderDepartmentEditLog"},
     *      description="Update TenderDepartmentEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderDepartmentEditLog",
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
     *                  ref="#/definitions/TenderDepartmentEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderDepartmentEditLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderDepartmentEditLog $tenderDepartmentEditLog */
        $tenderDepartmentEditLog = $this->tenderDepartmentEditLogRepository->findWithoutFail($id);

        if (empty($tenderDepartmentEditLog)) {
            return $this->sendError('Tender Department Edit Log not found');
        }

        $tenderDepartmentEditLog = $this->tenderDepartmentEditLogRepository->update($input, $id);

        return $this->sendResponse($tenderDepartmentEditLog->toArray(), 'TenderDepartmentEditLog updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/tenderDepartmentEditLogs/{id}",
     *      summary="deleteTenderDepartmentEditLog",
     *      tags={"TenderDepartmentEditLog"},
     *      description="Delete TenderDepartmentEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderDepartmentEditLog",
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
        /** @var TenderDepartmentEditLog $tenderDepartmentEditLog */
        $tenderDepartmentEditLog = $this->tenderDepartmentEditLogRepository->findWithoutFail($id);

        if (empty($tenderDepartmentEditLog)) {
            return $this->sendError('Tender Department Edit Log not found');
        }

        $tenderDepartmentEditLog->delete();

        return $this->sendSuccess('Tender Department Edit Log deleted successfully');
    }
}
