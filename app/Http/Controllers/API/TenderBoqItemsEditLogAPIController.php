<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderBoqItemsEditLogAPIRequest;
use App\Http\Requests\API\UpdateTenderBoqItemsEditLogAPIRequest;
use App\Models\TenderBoqItemsEditLog;
use App\Repositories\TenderBoqItemsEditLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderBoqItemsEditLogController
 * @package App\Http\Controllers\API
 */

class TenderBoqItemsEditLogAPIController extends AppBaseController
{
    /** @var  TenderBoqItemsEditLogRepository */
    private $tenderBoqItemsEditLogRepository;

    public function __construct(TenderBoqItemsEditLogRepository $tenderBoqItemsEditLogRepo)
    {
        $this->tenderBoqItemsEditLogRepository = $tenderBoqItemsEditLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderBoqItemsEditLogs",
     *      summary="getTenderBoqItemsEditLogList",
     *      tags={"TenderBoqItemsEditLog"},
     *      description="Get all TenderBoqItemsEditLogs",
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
     *                  @OA\Items(ref="#/definitions/TenderBoqItemsEditLog")
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
        $this->tenderBoqItemsEditLogRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderBoqItemsEditLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderBoqItemsEditLogs = $this->tenderBoqItemsEditLogRepository->all();

        return $this->sendResponse($tenderBoqItemsEditLogs->toArray(), trans('custom.tender_boq_items_edit_logs_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/tenderBoqItemsEditLogs",
     *      summary="createTenderBoqItemsEditLog",
     *      tags={"TenderBoqItemsEditLog"},
     *      description="Create TenderBoqItemsEditLog",
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
     *                  ref="#/definitions/TenderBoqItemsEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderBoqItemsEditLogAPIRequest $request)
    {
        $input = $request->all();

        $tenderBoqItemsEditLog = $this->tenderBoqItemsEditLogRepository->create($input);

        return $this->sendResponse($tenderBoqItemsEditLog->toArray(), trans('custom.tender_boq_items_edit_log_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderBoqItemsEditLogs/{id}",
     *      summary="getTenderBoqItemsEditLogItem",
     *      tags={"TenderBoqItemsEditLog"},
     *      description="Get TenderBoqItemsEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderBoqItemsEditLog",
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
     *                  ref="#/definitions/TenderBoqItemsEditLog"
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
        /** @var TenderBoqItemsEditLog $tenderBoqItemsEditLog */
        $tenderBoqItemsEditLog = $this->tenderBoqItemsEditLogRepository->findWithoutFail($id);

        if (empty($tenderBoqItemsEditLog)) {
            return $this->sendError(trans('custom.tender_boq_items_edit_log_not_found'));
        }

        return $this->sendResponse($tenderBoqItemsEditLog->toArray(), trans('custom.tender_boq_items_edit_log_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/tenderBoqItemsEditLogs/{id}",
     *      summary="updateTenderBoqItemsEditLog",
     *      tags={"TenderBoqItemsEditLog"},
     *      description="Update TenderBoqItemsEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderBoqItemsEditLog",
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
     *                  ref="#/definitions/TenderBoqItemsEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderBoqItemsEditLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderBoqItemsEditLog $tenderBoqItemsEditLog */
        $tenderBoqItemsEditLog = $this->tenderBoqItemsEditLogRepository->findWithoutFail($id);

        if (empty($tenderBoqItemsEditLog)) {
            return $this->sendError(trans('custom.tender_boq_items_edit_log_not_found'));
        }

        $tenderBoqItemsEditLog = $this->tenderBoqItemsEditLogRepository->update($input, $id);

        return $this->sendResponse($tenderBoqItemsEditLog->toArray(), trans('custom.tenderboqitemseditlog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/tenderBoqItemsEditLogs/{id}",
     *      summary="deleteTenderBoqItemsEditLog",
     *      tags={"TenderBoqItemsEditLog"},
     *      description="Delete TenderBoqItemsEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderBoqItemsEditLog",
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
        /** @var TenderBoqItemsEditLog $tenderBoqItemsEditLog */
        $tenderBoqItemsEditLog = $this->tenderBoqItemsEditLogRepository->findWithoutFail($id);

        if (empty($tenderBoqItemsEditLog)) {
            return $this->sendError(trans('custom.tender_boq_items_edit_log_not_found'));
        }

        $tenderBoqItemsEditLog->delete();

        return $this->sendSuccess('Tender Boq Items Edit Log deleted successfully');
    }
}
