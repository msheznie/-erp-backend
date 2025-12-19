<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderBudgetItemEditLogAPIRequest;
use App\Http\Requests\API\UpdateTenderBudgetItemEditLogAPIRequest;
use App\Models\TenderBudgetItemEditLog;
use App\Repositories\TenderBudgetItemEditLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderBudgetItemEditLogController
 * @package App\Http\Controllers\API
 */

class TenderBudgetItemEditLogAPIController extends AppBaseController
{
    /** @var  TenderBudgetItemEditLogRepository */
    private $tenderBudgetItemEditLogRepository;

    public function __construct(TenderBudgetItemEditLogRepository $tenderBudgetItemEditLogRepo)
    {
        $this->tenderBudgetItemEditLogRepository = $tenderBudgetItemEditLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderBudgetItemEditLogs",
     *      summary="getTenderBudgetItemEditLogList",
     *      tags={"TenderBudgetItemEditLog"},
     *      description="Get all TenderBudgetItemEditLogs",
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
     *                  @OA\Items(ref="#/definitions/TenderBudgetItemEditLog")
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
        $this->tenderBudgetItemEditLogRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderBudgetItemEditLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderBudgetItemEditLogs = $this->tenderBudgetItemEditLogRepository->all();

        return $this->sendResponse($tenderBudgetItemEditLogs->toArray(), trans('custom.tender_budget_item_edit_logs_retrieved_successfull'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/tenderBudgetItemEditLogs",
     *      summary="createTenderBudgetItemEditLog",
     *      tags={"TenderBudgetItemEditLog"},
     *      description="Create TenderBudgetItemEditLog",
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
     *                  ref="#/definitions/TenderBudgetItemEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderBudgetItemEditLogAPIRequest $request)
    {
        $input = $request->all();

        $tenderBudgetItemEditLog = $this->tenderBudgetItemEditLogRepository->create($input);

        return $this->sendResponse($tenderBudgetItemEditLog->toArray(), trans('custom.tender_budget_item_edit_log_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderBudgetItemEditLogs/{id}",
     *      summary="getTenderBudgetItemEditLogItem",
     *      tags={"TenderBudgetItemEditLog"},
     *      description="Get TenderBudgetItemEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderBudgetItemEditLog",
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
     *                  ref="#/definitions/TenderBudgetItemEditLog"
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
        /** @var TenderBudgetItemEditLog $tenderBudgetItemEditLog */
        $tenderBudgetItemEditLog = $this->tenderBudgetItemEditLogRepository->findWithoutFail($id);

        if (empty($tenderBudgetItemEditLog)) {
            return $this->sendError(trans('custom.tender_budget_item_edit_log_not_found'));
        }

        return $this->sendResponse($tenderBudgetItemEditLog->toArray(), trans('custom.tender_budget_item_edit_log_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/tenderBudgetItemEditLogs/{id}",
     *      summary="updateTenderBudgetItemEditLog",
     *      tags={"TenderBudgetItemEditLog"},
     *      description="Update TenderBudgetItemEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderBudgetItemEditLog",
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
     *                  ref="#/definitions/TenderBudgetItemEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderBudgetItemEditLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderBudgetItemEditLog $tenderBudgetItemEditLog */
        $tenderBudgetItemEditLog = $this->tenderBudgetItemEditLogRepository->findWithoutFail($id);

        if (empty($tenderBudgetItemEditLog)) {
            return $this->sendError(trans('custom.tender_budget_item_edit_log_not_found'));
        }

        $tenderBudgetItemEditLog = $this->tenderBudgetItemEditLogRepository->update($input, $id);

        return $this->sendResponse($tenderBudgetItemEditLog->toArray(), trans('custom.tenderbudgetitemeditlog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/tenderBudgetItemEditLogs/{id}",
     *      summary="deleteTenderBudgetItemEditLog",
     *      tags={"TenderBudgetItemEditLog"},
     *      description="Delete TenderBudgetItemEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderBudgetItemEditLog",
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
        /** @var TenderBudgetItemEditLog $tenderBudgetItemEditLog */
        $tenderBudgetItemEditLog = $this->tenderBudgetItemEditLogRepository->findWithoutFail($id);

        if (empty($tenderBudgetItemEditLog)) {
            return $this->sendError(trans('custom.tender_budget_item_edit_log_not_found'));
        }

        $tenderBudgetItemEditLog->delete();

        return $this->sendSuccess('Tender Budget Item Edit Log deleted successfully');
    }
}
