<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderCircularsEditLogAPIRequest;
use App\Http\Requests\API\UpdateTenderCircularsEditLogAPIRequest;
use App\Models\TenderCircularsEditLog;
use App\Repositories\TenderCircularsEditLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderCircularsEditLogController
 * @package App\Http\Controllers\API
 */

class TenderCircularsEditLogAPIController extends AppBaseController
{
    /** @var  TenderCircularsEditLogRepository */
    private $tenderCircularsEditLogRepository;

    public function __construct(TenderCircularsEditLogRepository $tenderCircularsEditLogRepo)
    {
        $this->tenderCircularsEditLogRepository = $tenderCircularsEditLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderCircularsEditLogs",
     *      summary="getTenderCircularsEditLogList",
     *      tags={"TenderCircularsEditLog"},
     *      description="Get all TenderCircularsEditLogs",
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
     *                  @OA\Items(ref="#/definitions/TenderCircularsEditLog")
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
        $this->tenderCircularsEditLogRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderCircularsEditLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderCircularsEditLogs = $this->tenderCircularsEditLogRepository->all();

        return $this->sendResponse($tenderCircularsEditLogs->toArray(), trans('custom.tender_circulars_edit_logs_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/tenderCircularsEditLogs",
     *      summary="createTenderCircularsEditLog",
     *      tags={"TenderCircularsEditLog"},
     *      description="Create TenderCircularsEditLog",
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
     *                  ref="#/definitions/TenderCircularsEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderCircularsEditLogAPIRequest $request)
    {
        $input = $request->all();

        $tenderCircularsEditLog = $this->tenderCircularsEditLogRepository->create($input);

        return $this->sendResponse($tenderCircularsEditLog->toArray(), trans('custom.tender_circulars_edit_log_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderCircularsEditLogs/{id}",
     *      summary="getTenderCircularsEditLogItem",
     *      tags={"TenderCircularsEditLog"},
     *      description="Get TenderCircularsEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderCircularsEditLog",
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
     *                  ref="#/definitions/TenderCircularsEditLog"
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
        /** @var TenderCircularsEditLog $tenderCircularsEditLog */
        $tenderCircularsEditLog = $this->tenderCircularsEditLogRepository->findWithoutFail($id);

        if (empty($tenderCircularsEditLog)) {
            return $this->sendError(trans('custom.tender_circulars_edit_log_not_found'));
        }

        return $this->sendResponse($tenderCircularsEditLog->toArray(), trans('custom.tender_circulars_edit_log_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/tenderCircularsEditLogs/{id}",
     *      summary="updateTenderCircularsEditLog",
     *      tags={"TenderCircularsEditLog"},
     *      description="Update TenderCircularsEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderCircularsEditLog",
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
     *                  ref="#/definitions/TenderCircularsEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderCircularsEditLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderCircularsEditLog $tenderCircularsEditLog */
        $tenderCircularsEditLog = $this->tenderCircularsEditLogRepository->findWithoutFail($id);

        if (empty($tenderCircularsEditLog)) {
            return $this->sendError(trans('custom.tender_circulars_edit_log_not_found'));
        }

        $tenderCircularsEditLog = $this->tenderCircularsEditLogRepository->update($input, $id);

        return $this->sendResponse($tenderCircularsEditLog->toArray(), trans('custom.tendercircularseditlog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/tenderCircularsEditLogs/{id}",
     *      summary="deleteTenderCircularsEditLog",
     *      tags={"TenderCircularsEditLog"},
     *      description="Delete TenderCircularsEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderCircularsEditLog",
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
        /** @var TenderCircularsEditLog $tenderCircularsEditLog */
        $tenderCircularsEditLog = $this->tenderCircularsEditLogRepository->findWithoutFail($id);

        if (empty($tenderCircularsEditLog)) {
            return $this->sendError(trans('custom.tender_circulars_edit_log_not_found'));
        }

        $tenderCircularsEditLog->delete();

        return $this->sendSuccess('Tender Circulars Edit Log deleted successfully');
    }
}
