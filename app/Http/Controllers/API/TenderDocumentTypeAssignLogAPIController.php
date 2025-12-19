<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderDocumentTypeAssignLogAPIRequest;
use App\Http\Requests\API\UpdateTenderDocumentTypeAssignLogAPIRequest;
use App\Models\TenderDocumentTypeAssignLog;
use App\Repositories\TenderDocumentTypeAssignLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderDocumentTypeAssignLogController
 * @package App\Http\Controllers\API
 */

class TenderDocumentTypeAssignLogAPIController extends AppBaseController
{
    /** @var  TenderDocumentTypeAssignLogRepository */
    private $tenderDocumentTypeAssignLogRepository;

    public function __construct(TenderDocumentTypeAssignLogRepository $tenderDocumentTypeAssignLogRepo)
    {
        $this->tenderDocumentTypeAssignLogRepository = $tenderDocumentTypeAssignLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderDocumentTypeAssignLogs",
     *      summary="getTenderDocumentTypeAssignLogList",
     *      tags={"TenderDocumentTypeAssignLog"},
     *      description="Get all TenderDocumentTypeAssignLogs",
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
     *                  @OA\Items(ref="#/definitions/TenderDocumentTypeAssignLog")
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
        $this->tenderDocumentTypeAssignLogRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderDocumentTypeAssignLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderDocumentTypeAssignLogs = $this->tenderDocumentTypeAssignLogRepository->all();

        return $this->sendResponse($tenderDocumentTypeAssignLogs->toArray(), trans('custom.tender_document_type_assign_logs_retrieved_success'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/tenderDocumentTypeAssignLogs",
     *      summary="createTenderDocumentTypeAssignLog",
     *      tags={"TenderDocumentTypeAssignLog"},
     *      description="Create TenderDocumentTypeAssignLog",
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
     *                  ref="#/definitions/TenderDocumentTypeAssignLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderDocumentTypeAssignLogAPIRequest $request)
    {
        $input = $request->all();

        $tenderDocumentTypeAssignLog = $this->tenderDocumentTypeAssignLogRepository->create($input);

        return $this->sendResponse($tenderDocumentTypeAssignLog->toArray(), trans('custom.tender_document_type_assign_log_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderDocumentTypeAssignLogs/{id}",
     *      summary="getTenderDocumentTypeAssignLogItem",
     *      tags={"TenderDocumentTypeAssignLog"},
     *      description="Get TenderDocumentTypeAssignLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderDocumentTypeAssignLog",
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
     *                  ref="#/definitions/TenderDocumentTypeAssignLog"
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
        /** @var TenderDocumentTypeAssignLog $tenderDocumentTypeAssignLog */
        $tenderDocumentTypeAssignLog = $this->tenderDocumentTypeAssignLogRepository->findWithoutFail($id);

        if (empty($tenderDocumentTypeAssignLog)) {
            return $this->sendError(trans('custom.tender_document_type_assign_log_not_found'));
        }

        return $this->sendResponse($tenderDocumentTypeAssignLog->toArray(), trans('custom.tender_document_type_assign_log_retrieved_successf'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/tenderDocumentTypeAssignLogs/{id}",
     *      summary="updateTenderDocumentTypeAssignLog",
     *      tags={"TenderDocumentTypeAssignLog"},
     *      description="Update TenderDocumentTypeAssignLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderDocumentTypeAssignLog",
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
     *                  ref="#/definitions/TenderDocumentTypeAssignLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderDocumentTypeAssignLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderDocumentTypeAssignLog $tenderDocumentTypeAssignLog */
        $tenderDocumentTypeAssignLog = $this->tenderDocumentTypeAssignLogRepository->findWithoutFail($id);

        if (empty($tenderDocumentTypeAssignLog)) {
            return $this->sendError(trans('custom.tender_document_type_assign_log_not_found'));
        }

        $tenderDocumentTypeAssignLog = $this->tenderDocumentTypeAssignLogRepository->update($input, $id);

        return $this->sendResponse($tenderDocumentTypeAssignLog->toArray(), trans('custom.tenderdocumenttypeassignlog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/tenderDocumentTypeAssignLogs/{id}",
     *      summary="deleteTenderDocumentTypeAssignLog",
     *      tags={"TenderDocumentTypeAssignLog"},
     *      description="Delete TenderDocumentTypeAssignLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderDocumentTypeAssignLog",
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
        /** @var TenderDocumentTypeAssignLog $tenderDocumentTypeAssignLog */
        $tenderDocumentTypeAssignLog = $this->tenderDocumentTypeAssignLogRepository->findWithoutFail($id);

        if (empty($tenderDocumentTypeAssignLog)) {
            return $this->sendError(trans('custom.tender_document_type_assign_log_not_found'));
        }

        $tenderDocumentTypeAssignLog->delete();

        return $this->sendSuccess('Tender Document Type Assign Log deleted successfully');
    }
}
