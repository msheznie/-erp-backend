<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderSupplierAssigneeEditLogAPIRequest;
use App\Http\Requests\API\UpdateTenderSupplierAssigneeEditLogAPIRequest;
use App\Models\TenderSupplierAssigneeEditLog;
use App\Repositories\TenderSupplierAssigneeEditLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderSupplierAssigneeEditLogController
 * @package App\Http\Controllers\API
 */

class TenderSupplierAssigneeEditLogAPIController extends AppBaseController
{
    /** @var  TenderSupplierAssigneeEditLogRepository */
    private $tenderSupplierAssigneeEditLogRepository;

    public function __construct(TenderSupplierAssigneeEditLogRepository $tenderSupplierAssigneeEditLogRepo)
    {
        $this->tenderSupplierAssigneeEditLogRepository = $tenderSupplierAssigneeEditLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderSupplierAssigneeEditLogs",
     *      summary="getTenderSupplierAssigneeEditLogList",
     *      tags={"TenderSupplierAssigneeEditLog"},
     *      description="Get all TenderSupplierAssigneeEditLogs",
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
     *                  @OA\Items(ref="#/definitions/TenderSupplierAssigneeEditLog")
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
        $this->tenderSupplierAssigneeEditLogRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderSupplierAssigneeEditLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderSupplierAssigneeEditLogs = $this->tenderSupplierAssigneeEditLogRepository->all();

        return $this->sendResponse($tenderSupplierAssigneeEditLogs->toArray(), trans('custom.tender_supplier_assignee_edit_logs_retrieved_succe'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/tenderSupplierAssigneeEditLogs",
     *      summary="createTenderSupplierAssigneeEditLog",
     *      tags={"TenderSupplierAssigneeEditLog"},
     *      description="Create TenderSupplierAssigneeEditLog",
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
     *                  ref="#/definitions/TenderSupplierAssigneeEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderSupplierAssigneeEditLogAPIRequest $request)
    {
        $input = $request->all();

        $tenderSupplierAssigneeEditLog = $this->tenderSupplierAssigneeEditLogRepository->create($input);

        return $this->sendResponse($tenderSupplierAssigneeEditLog->toArray(), trans('custom.tender_supplier_assignee_edit_log_saved_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderSupplierAssigneeEditLogs/{id}",
     *      summary="getTenderSupplierAssigneeEditLogItem",
     *      tags={"TenderSupplierAssigneeEditLog"},
     *      description="Get TenderSupplierAssigneeEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderSupplierAssigneeEditLog",
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
     *                  ref="#/definitions/TenderSupplierAssigneeEditLog"
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
        /** @var TenderSupplierAssigneeEditLog $tenderSupplierAssigneeEditLog */
        $tenderSupplierAssigneeEditLog = $this->tenderSupplierAssigneeEditLogRepository->findWithoutFail($id);

        if (empty($tenderSupplierAssigneeEditLog)) {
            return $this->sendError(trans('custom.tender_supplier_assignee_edit_log_not_found'));
        }

        return $this->sendResponse($tenderSupplierAssigneeEditLog->toArray(), trans('custom.tender_supplier_assignee_edit_log_retrieved_succes'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/tenderSupplierAssigneeEditLogs/{id}",
     *      summary="updateTenderSupplierAssigneeEditLog",
     *      tags={"TenderSupplierAssigneeEditLog"},
     *      description="Update TenderSupplierAssigneeEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderSupplierAssigneeEditLog",
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
     *                  ref="#/definitions/TenderSupplierAssigneeEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderSupplierAssigneeEditLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderSupplierAssigneeEditLog $tenderSupplierAssigneeEditLog */
        $tenderSupplierAssigneeEditLog = $this->tenderSupplierAssigneeEditLogRepository->findWithoutFail($id);

        if (empty($tenderSupplierAssigneeEditLog)) {
            return $this->sendError(trans('custom.tender_supplier_assignee_edit_log_not_found'));
        }

        $tenderSupplierAssigneeEditLog = $this->tenderSupplierAssigneeEditLogRepository->update($input, $id);

        return $this->sendResponse($tenderSupplierAssigneeEditLog->toArray(), trans('custom.tendersupplierassigneeeditlog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/tenderSupplierAssigneeEditLogs/{id}",
     *      summary="deleteTenderSupplierAssigneeEditLog",
     *      tags={"TenderSupplierAssigneeEditLog"},
     *      description="Delete TenderSupplierAssigneeEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderSupplierAssigneeEditLog",
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
        /** @var TenderSupplierAssigneeEditLog $tenderSupplierAssigneeEditLog */
        $tenderSupplierAssigneeEditLog = $this->tenderSupplierAssigneeEditLogRepository->findWithoutFail($id);

        if (empty($tenderSupplierAssigneeEditLog)) {
            return $this->sendError(trans('custom.tender_supplier_assignee_edit_log_not_found'));
        }

        $tenderSupplierAssigneeEditLog->delete();

        return $this->sendSuccess('Tender Supplier Assignee Edit Log deleted successfully');
    }
}
