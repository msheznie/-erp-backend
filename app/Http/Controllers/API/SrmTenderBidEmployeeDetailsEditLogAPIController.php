<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSrmTenderBidEmployeeDetailsEditLogAPIRequest;
use App\Http\Requests\API\UpdateSrmTenderBidEmployeeDetailsEditLogAPIRequest;
use App\Models\SrmTenderBidEmployeeDetailsEditLog;
use App\Repositories\SrmTenderBidEmployeeDetailsEditLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SrmTenderBidEmployeeDetailsEditLogController
 * @package App\Http\Controllers\API
 */

class SrmTenderBidEmployeeDetailsEditLogAPIController extends AppBaseController
{
    /** @var  SrmTenderBidEmployeeDetailsEditLogRepository */
    private $srmTenderBidEmployeeDetailsEditLogRepository;

    public function __construct(SrmTenderBidEmployeeDetailsEditLogRepository $srmTenderBidEmployeeDetailsEditLogRepo)
    {
        $this->srmTenderBidEmployeeDetailsEditLogRepository = $srmTenderBidEmployeeDetailsEditLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/srmTenderBidEmployeeDetailsEditLogs",
     *      summary="getSrmTenderBidEmployeeDetailsEditLogList",
     *      tags={"SrmTenderBidEmployeeDetailsEditLog"},
     *      description="Get all SrmTenderBidEmployeeDetailsEditLogs",
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
     *                  @OA\Items(ref="#/definitions/SrmTenderBidEmployeeDetailsEditLog")
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
        $this->srmTenderBidEmployeeDetailsEditLogRepository->pushCriteria(new RequestCriteria($request));
        $this->srmTenderBidEmployeeDetailsEditLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $srmTenderBidEmployeeDetailsEditLogs = $this->srmTenderBidEmployeeDetailsEditLogRepository->all();

        return $this->sendResponse($srmTenderBidEmployeeDetailsEditLogs->toArray(), 'Srm Tender Bid Employee Details Edit Logs retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/srmTenderBidEmployeeDetailsEditLogs",
     *      summary="createSrmTenderBidEmployeeDetailsEditLog",
     *      tags={"SrmTenderBidEmployeeDetailsEditLog"},
     *      description="Create SrmTenderBidEmployeeDetailsEditLog",
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
     *                  ref="#/definitions/SrmTenderBidEmployeeDetailsEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSrmTenderBidEmployeeDetailsEditLogAPIRequest $request)
    {
        $input = $request->all();

        $srmTenderBidEmployeeDetailsEditLog = $this->srmTenderBidEmployeeDetailsEditLogRepository->create($input);

        return $this->sendResponse($srmTenderBidEmployeeDetailsEditLog->toArray(), 'Srm Tender Bid Employee Details Edit Log saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/srmTenderBidEmployeeDetailsEditLogs/{id}",
     *      summary="getSrmTenderBidEmployeeDetailsEditLogItem",
     *      tags={"SrmTenderBidEmployeeDetailsEditLog"},
     *      description="Get SrmTenderBidEmployeeDetailsEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SrmTenderBidEmployeeDetailsEditLog",
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
     *                  ref="#/definitions/SrmTenderBidEmployeeDetailsEditLog"
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
        /** @var SrmTenderBidEmployeeDetailsEditLog $srmTenderBidEmployeeDetailsEditLog */
        $srmTenderBidEmployeeDetailsEditLog = $this->srmTenderBidEmployeeDetailsEditLogRepository->findWithoutFail($id);

        if (empty($srmTenderBidEmployeeDetailsEditLog)) {
            return $this->sendError('Srm Tender Bid Employee Details Edit Log not found');
        }

        return $this->sendResponse($srmTenderBidEmployeeDetailsEditLog->toArray(), 'Srm Tender Bid Employee Details Edit Log retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/srmTenderBidEmployeeDetailsEditLogs/{id}",
     *      summary="updateSrmTenderBidEmployeeDetailsEditLog",
     *      tags={"SrmTenderBidEmployeeDetailsEditLog"},
     *      description="Update SrmTenderBidEmployeeDetailsEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SrmTenderBidEmployeeDetailsEditLog",
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
     *                  ref="#/definitions/SrmTenderBidEmployeeDetailsEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSrmTenderBidEmployeeDetailsEditLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var SrmTenderBidEmployeeDetailsEditLog $srmTenderBidEmployeeDetailsEditLog */
        $srmTenderBidEmployeeDetailsEditLog = $this->srmTenderBidEmployeeDetailsEditLogRepository->findWithoutFail($id);

        if (empty($srmTenderBidEmployeeDetailsEditLog)) {
            return $this->sendError('Srm Tender Bid Employee Details Edit Log not found');
        }

        $srmTenderBidEmployeeDetailsEditLog = $this->srmTenderBidEmployeeDetailsEditLogRepository->update($input, $id);

        return $this->sendResponse($srmTenderBidEmployeeDetailsEditLog->toArray(), 'SrmTenderBidEmployeeDetailsEditLog updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/srmTenderBidEmployeeDetailsEditLogs/{id}",
     *      summary="deleteSrmTenderBidEmployeeDetailsEditLog",
     *      tags={"SrmTenderBidEmployeeDetailsEditLog"},
     *      description="Delete SrmTenderBidEmployeeDetailsEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SrmTenderBidEmployeeDetailsEditLog",
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
        /** @var SrmTenderBidEmployeeDetailsEditLog $srmTenderBidEmployeeDetailsEditLog */
        $srmTenderBidEmployeeDetailsEditLog = $this->srmTenderBidEmployeeDetailsEditLogRepository->findWithoutFail($id);

        if (empty($srmTenderBidEmployeeDetailsEditLog)) {
            return $this->sendError('Srm Tender Bid Employee Details Edit Log not found');
        }

        $srmTenderBidEmployeeDetailsEditLog->delete();

        return $this->sendSuccess('Srm Tender Bid Employee Details Edit Log deleted successfully');
    }
}
