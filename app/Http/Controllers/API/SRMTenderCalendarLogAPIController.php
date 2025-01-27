<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSRMTenderCalendarLogAPIRequest;
use App\Http\Requests\API\UpdateSRMTenderCalendarLogAPIRequest;
use App\Models\SRMTenderCalendarLog;
use App\Repositories\SRMTenderCalendarLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SRMTenderCalendarLogController
 * @package App\Http\Controllers\API
 */

class SRMTenderCalendarLogAPIController extends AppBaseController
{
    /** @var  SRMTenderCalendarLogRepository */
    private $sRMTenderCalendarLogRepository;

    public function __construct(SRMTenderCalendarLogRepository $sRMTenderCalendarLogRepo)
    {
        $this->sRMTenderCalendarLogRepository = $sRMTenderCalendarLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/sRMTenderCalendarLogs",
     *      summary="getSRMTenderCalendarLogList",
     *      tags={"SRMTenderCalendarLog"},
     *      description="Get all SRMTenderCalendarLogs",
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
     *                  @OA\Items(ref="#/definitions/SRMTenderCalendarLog")
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
        $this->sRMTenderCalendarLogRepository->pushCriteria(new RequestCriteria($request));
        $this->sRMTenderCalendarLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sRMTenderCalendarLogs = $this->sRMTenderCalendarLogRepository->all();

        return $this->sendResponse($sRMTenderCalendarLogs->toArray(), 'S R M Tender Calendar Logs retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/sRMTenderCalendarLogs",
     *      summary="createSRMTenderCalendarLog",
     *      tags={"SRMTenderCalendarLog"},
     *      description="Create SRMTenderCalendarLog",
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
     *                  ref="#/definitions/SRMTenderCalendarLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSRMTenderCalendarLogAPIRequest $request)
    {
        $input = $request->all();

        $sRMTenderCalendarLog = $this->sRMTenderCalendarLogRepository->create($input);

        return $this->sendResponse($sRMTenderCalendarLog->toArray(), 'S R M Tender Calendar Log saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/sRMTenderCalendarLogs/{id}",
     *      summary="getSRMTenderCalendarLogItem",
     *      tags={"SRMTenderCalendarLog"},
     *      description="Get SRMTenderCalendarLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMTenderCalendarLog",
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
     *                  ref="#/definitions/SRMTenderCalendarLog"
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
        /** @var SRMTenderCalendarLog $sRMTenderCalendarLog */
        $sRMTenderCalendarLog = $this->sRMTenderCalendarLogRepository->findWithoutFail($id);

        if (empty($sRMTenderCalendarLog)) {
            return $this->sendError('S R M Tender Calendar Log not found');
        }

        return $this->sendResponse($sRMTenderCalendarLog->toArray(), 'S R M Tender Calendar Log retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/sRMTenderCalendarLogs/{id}",
     *      summary="updateSRMTenderCalendarLog",
     *      tags={"SRMTenderCalendarLog"},
     *      description="Update SRMTenderCalendarLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMTenderCalendarLog",
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
     *                  ref="#/definitions/SRMTenderCalendarLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSRMTenderCalendarLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var SRMTenderCalendarLog $sRMTenderCalendarLog */
        $sRMTenderCalendarLog = $this->sRMTenderCalendarLogRepository->findWithoutFail($id);

        if (empty($sRMTenderCalendarLog)) {
            return $this->sendError('S R M Tender Calendar Log not found');
        }

        $sRMTenderCalendarLog = $this->sRMTenderCalendarLogRepository->update($input, $id);

        return $this->sendResponse($sRMTenderCalendarLog->toArray(), 'SRMTenderCalendarLog updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/sRMTenderCalendarLogs/{id}",
     *      summary="deleteSRMTenderCalendarLog",
     *      tags={"SRMTenderCalendarLog"},
     *      description="Delete SRMTenderCalendarLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMTenderCalendarLog",
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
        /** @var SRMTenderCalendarLog $sRMTenderCalendarLog */
        $sRMTenderCalendarLog = $this->sRMTenderCalendarLogRepository->findWithoutFail($id);

        if (empty($sRMTenderCalendarLog)) {
            return $this->sendError('S R M Tender Calendar Log not found');
        }

        $sRMTenderCalendarLog->delete();

        return $this->sendSuccess('S R M Tender Calendar Log deleted successfully');
    }
}
