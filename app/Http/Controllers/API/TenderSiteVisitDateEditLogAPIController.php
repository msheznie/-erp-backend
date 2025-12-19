<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderSiteVisitDateEditLogAPIRequest;
use App\Http\Requests\API\UpdateTenderSiteVisitDateEditLogAPIRequest;
use App\Models\TenderSiteVisitDateEditLog;
use App\Repositories\TenderSiteVisitDateEditLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderSiteVisitDateEditLogController
 * @package App\Http\Controllers\API
 */

class TenderSiteVisitDateEditLogAPIController extends AppBaseController
{
    /** @var  TenderSiteVisitDateEditLogRepository */
    private $tenderSiteVisitDateEditLogRepository;

    public function __construct(TenderSiteVisitDateEditLogRepository $tenderSiteVisitDateEditLogRepo)
    {
        $this->tenderSiteVisitDateEditLogRepository = $tenderSiteVisitDateEditLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderSiteVisitDateEditLogs",
     *      summary="getTenderSiteVisitDateEditLogList",
     *      tags={"TenderSiteVisitDateEditLog"},
     *      description="Get all TenderSiteVisitDateEditLogs",
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
     *                  @OA\Items(ref="#/definitions/TenderSiteVisitDateEditLog")
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
        $this->tenderSiteVisitDateEditLogRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderSiteVisitDateEditLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderSiteVisitDateEditLogs = $this->tenderSiteVisitDateEditLogRepository->all();

        return $this->sendResponse($tenderSiteVisitDateEditLogs->toArray(), trans('custom.tender_site_visit_date_edit_logs_retrieved_success'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/tenderSiteVisitDateEditLogs",
     *      summary="createTenderSiteVisitDateEditLog",
     *      tags={"TenderSiteVisitDateEditLog"},
     *      description="Create TenderSiteVisitDateEditLog",
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
     *                  ref="#/definitions/TenderSiteVisitDateEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderSiteVisitDateEditLogAPIRequest $request)
    {
        $input = $request->all();

        $tenderSiteVisitDateEditLog = $this->tenderSiteVisitDateEditLogRepository->create($input);

        return $this->sendResponse($tenderSiteVisitDateEditLog->toArray(), trans('custom.tender_site_visit_date_edit_log_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderSiteVisitDateEditLogs/{id}",
     *      summary="getTenderSiteVisitDateEditLogItem",
     *      tags={"TenderSiteVisitDateEditLog"},
     *      description="Get TenderSiteVisitDateEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderSiteVisitDateEditLog",
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
     *                  ref="#/definitions/TenderSiteVisitDateEditLog"
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
        /** @var TenderSiteVisitDateEditLog $tenderSiteVisitDateEditLog */
        $tenderSiteVisitDateEditLog = $this->tenderSiteVisitDateEditLogRepository->findWithoutFail($id);

        if (empty($tenderSiteVisitDateEditLog)) {
            return $this->sendError(trans('custom.tender_site_visit_date_edit_log_not_found'));
        }

        return $this->sendResponse($tenderSiteVisitDateEditLog->toArray(), trans('custom.tender_site_visit_date_edit_log_retrieved_successf'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/tenderSiteVisitDateEditLogs/{id}",
     *      summary="updateTenderSiteVisitDateEditLog",
     *      tags={"TenderSiteVisitDateEditLog"},
     *      description="Update TenderSiteVisitDateEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderSiteVisitDateEditLog",
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
     *                  ref="#/definitions/TenderSiteVisitDateEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderSiteVisitDateEditLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderSiteVisitDateEditLog $tenderSiteVisitDateEditLog */
        $tenderSiteVisitDateEditLog = $this->tenderSiteVisitDateEditLogRepository->findWithoutFail($id);

        if (empty($tenderSiteVisitDateEditLog)) {
            return $this->sendError(trans('custom.tender_site_visit_date_edit_log_not_found'));
        }

        $tenderSiteVisitDateEditLog = $this->tenderSiteVisitDateEditLogRepository->update($input, $id);

        return $this->sendResponse($tenderSiteVisitDateEditLog->toArray(), trans('custom.tendersitevisitdateeditlog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/tenderSiteVisitDateEditLogs/{id}",
     *      summary="deleteTenderSiteVisitDateEditLog",
     *      tags={"TenderSiteVisitDateEditLog"},
     *      description="Delete TenderSiteVisitDateEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderSiteVisitDateEditLog",
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
        /** @var TenderSiteVisitDateEditLog $tenderSiteVisitDateEditLog */
        $tenderSiteVisitDateEditLog = $this->tenderSiteVisitDateEditLogRepository->findWithoutFail($id);

        if (empty($tenderSiteVisitDateEditLog)) {
            return $this->sendError(trans('custom.tender_site_visit_date_edit_log_not_found'));
        }

        $tenderSiteVisitDateEditLog->delete();

        return $this->sendSuccess('Tender Site Visit Date Edit Log deleted successfully');
    }
}
