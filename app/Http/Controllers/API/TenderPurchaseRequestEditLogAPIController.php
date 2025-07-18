<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderPurchaseRequestEditLogAPIRequest;
use App\Http\Requests\API\UpdateTenderPurchaseRequestEditLogAPIRequest;
use App\Models\TenderPurchaseRequestEditLog;
use App\Repositories\TenderPurchaseRequestEditLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderPurchaseRequestEditLogController
 * @package App\Http\Controllers\API
 */

class TenderPurchaseRequestEditLogAPIController extends AppBaseController
{
    /** @var  TenderPurchaseRequestEditLogRepository */
    private $tenderPurchaseRequestEditLogRepository;

    public function __construct(TenderPurchaseRequestEditLogRepository $tenderPurchaseRequestEditLogRepo)
    {
        $this->tenderPurchaseRequestEditLogRepository = $tenderPurchaseRequestEditLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderPurchaseRequestEditLogs",
     *      summary="getTenderPurchaseRequestEditLogList",
     *      tags={"TenderPurchaseRequestEditLog"},
     *      description="Get all TenderPurchaseRequestEditLogs",
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
     *                  @OA\Items(ref="#/definitions/TenderPurchaseRequestEditLog")
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
        $this->tenderPurchaseRequestEditLogRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderPurchaseRequestEditLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderPurchaseRequestEditLogs = $this->tenderPurchaseRequestEditLogRepository->all();

        return $this->sendResponse($tenderPurchaseRequestEditLogs->toArray(), 'Tender Purchase Request Edit Logs retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/tenderPurchaseRequestEditLogs",
     *      summary="createTenderPurchaseRequestEditLog",
     *      tags={"TenderPurchaseRequestEditLog"},
     *      description="Create TenderPurchaseRequestEditLog",
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
     *                  ref="#/definitions/TenderPurchaseRequestEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderPurchaseRequestEditLogAPIRequest $request)
    {
        $input = $request->all();

        $tenderPurchaseRequestEditLog = $this->tenderPurchaseRequestEditLogRepository->create($input);

        return $this->sendResponse($tenderPurchaseRequestEditLog->toArray(), 'Tender Purchase Request Edit Log saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderPurchaseRequestEditLogs/{id}",
     *      summary="getTenderPurchaseRequestEditLogItem",
     *      tags={"TenderPurchaseRequestEditLog"},
     *      description="Get TenderPurchaseRequestEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderPurchaseRequestEditLog",
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
     *                  ref="#/definitions/TenderPurchaseRequestEditLog"
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
        /** @var TenderPurchaseRequestEditLog $tenderPurchaseRequestEditLog */
        $tenderPurchaseRequestEditLog = $this->tenderPurchaseRequestEditLogRepository->findWithoutFail($id);

        if (empty($tenderPurchaseRequestEditLog)) {
            return $this->sendError('Tender Purchase Request Edit Log not found');
        }

        return $this->sendResponse($tenderPurchaseRequestEditLog->toArray(), 'Tender Purchase Request Edit Log retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/tenderPurchaseRequestEditLogs/{id}",
     *      summary="updateTenderPurchaseRequestEditLog",
     *      tags={"TenderPurchaseRequestEditLog"},
     *      description="Update TenderPurchaseRequestEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderPurchaseRequestEditLog",
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
     *                  ref="#/definitions/TenderPurchaseRequestEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderPurchaseRequestEditLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderPurchaseRequestEditLog $tenderPurchaseRequestEditLog */
        $tenderPurchaseRequestEditLog = $this->tenderPurchaseRequestEditLogRepository->findWithoutFail($id);

        if (empty($tenderPurchaseRequestEditLog)) {
            return $this->sendError('Tender Purchase Request Edit Log not found');
        }

        $tenderPurchaseRequestEditLog = $this->tenderPurchaseRequestEditLogRepository->update($input, $id);

        return $this->sendResponse($tenderPurchaseRequestEditLog->toArray(), 'TenderPurchaseRequestEditLog updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/tenderPurchaseRequestEditLogs/{id}",
     *      summary="deleteTenderPurchaseRequestEditLog",
     *      tags={"TenderPurchaseRequestEditLog"},
     *      description="Delete TenderPurchaseRequestEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderPurchaseRequestEditLog",
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
        /** @var TenderPurchaseRequestEditLog $tenderPurchaseRequestEditLog */
        $tenderPurchaseRequestEditLog = $this->tenderPurchaseRequestEditLogRepository->findWithoutFail($id);

        if (empty($tenderPurchaseRequestEditLog)) {
            return $this->sendError('Tender Purchase Request Edit Log not found');
        }

        $tenderPurchaseRequestEditLog->delete();

        return $this->sendSuccess('Tender Purchase Request Edit Log deleted successfully');
    }
}
