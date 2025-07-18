<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSrmTenderMasterEditLogAPIRequest;
use App\Http\Requests\API\UpdateSrmTenderMasterEditLogAPIRequest;
use App\Models\SrmTenderMasterEditLog;
use App\Repositories\SrmTenderMasterEditLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SrmTenderMasterEditLogController
 * @package App\Http\Controllers\API
 */

class SrmTenderMasterEditLogAPIController extends AppBaseController
{
    /** @var  SrmTenderMasterEditLogRepository */
    private $srmTenderMasterEditLogRepository;

    public function __construct(SrmTenderMasterEditLogRepository $srmTenderMasterEditLogRepo)
    {
        $this->srmTenderMasterEditLogRepository = $srmTenderMasterEditLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/srmTenderMasterEditLogs",
     *      summary="getSrmTenderMasterEditLogList",
     *      tags={"SrmTenderMasterEditLog"},
     *      description="Get all SrmTenderMasterEditLogs",
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
     *                  @OA\Items(ref="#/definitions/SrmTenderMasterEditLog")
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
        $this->srmTenderMasterEditLogRepository->pushCriteria(new RequestCriteria($request));
        $this->srmTenderMasterEditLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $srmTenderMasterEditLogs = $this->srmTenderMasterEditLogRepository->all();

        return $this->sendResponse($srmTenderMasterEditLogs->toArray(), 'Srm Tender Master Edit Logs retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/srmTenderMasterEditLogs",
     *      summary="createSrmTenderMasterEditLog",
     *      tags={"SrmTenderMasterEditLog"},
     *      description="Create SrmTenderMasterEditLog",
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
     *                  ref="#/definitions/SrmTenderMasterEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSrmTenderMasterEditLogAPIRequest $request)
    {
        $input = $request->all();

        $srmTenderMasterEditLog = $this->srmTenderMasterEditLogRepository->create($input);

        return $this->sendResponse($srmTenderMasterEditLog->toArray(), 'Srm Tender Master Edit Log saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/srmTenderMasterEditLogs/{id}",
     *      summary="getSrmTenderMasterEditLogItem",
     *      tags={"SrmTenderMasterEditLog"},
     *      description="Get SrmTenderMasterEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SrmTenderMasterEditLog",
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
     *                  ref="#/definitions/SrmTenderMasterEditLog"
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
        /** @var SrmTenderMasterEditLog $srmTenderMasterEditLog */
        $srmTenderMasterEditLog = $this->srmTenderMasterEditLogRepository->findWithoutFail($id);

        if (empty($srmTenderMasterEditLog)) {
            return $this->sendError('Srm Tender Master Edit Log not found');
        }

        return $this->sendResponse($srmTenderMasterEditLog->toArray(), 'Srm Tender Master Edit Log retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/srmTenderMasterEditLogs/{id}",
     *      summary="updateSrmTenderMasterEditLog",
     *      tags={"SrmTenderMasterEditLog"},
     *      description="Update SrmTenderMasterEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SrmTenderMasterEditLog",
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
     *                  ref="#/definitions/SrmTenderMasterEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSrmTenderMasterEditLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var SrmTenderMasterEditLog $srmTenderMasterEditLog */
        $srmTenderMasterEditLog = $this->srmTenderMasterEditLogRepository->findWithoutFail($id);

        if (empty($srmTenderMasterEditLog)) {
            return $this->sendError('Srm Tender Master Edit Log not found');
        }

        $srmTenderMasterEditLog = $this->srmTenderMasterEditLogRepository->update($input, $id);

        return $this->sendResponse($srmTenderMasterEditLog->toArray(), 'SrmTenderMasterEditLog updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/srmTenderMasterEditLogs/{id}",
     *      summary="deleteSrmTenderMasterEditLog",
     *      tags={"SrmTenderMasterEditLog"},
     *      description="Delete SrmTenderMasterEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SrmTenderMasterEditLog",
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
        /** @var SrmTenderMasterEditLog $srmTenderMasterEditLog */
        $srmTenderMasterEditLog = $this->srmTenderMasterEditLogRepository->findWithoutFail($id);

        if (empty($srmTenderMasterEditLog)) {
            return $this->sendError('Srm Tender Master Edit Log not found');
        }

        $srmTenderMasterEditLog->delete();

        return $this->sendSuccess('Srm Tender Master Edit Log deleted successfully');
    }
}
