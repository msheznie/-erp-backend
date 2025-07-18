<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSrmTenderUserAccessEditLogAPIRequest;
use App\Http\Requests\API\UpdateSrmTenderUserAccessEditLogAPIRequest;
use App\Models\SrmTenderUserAccessEditLog;
use App\Repositories\SrmTenderUserAccessEditLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SrmTenderUserAccessEditLogController
 * @package App\Http\Controllers\API
 */

class SrmTenderUserAccessEditLogAPIController extends AppBaseController
{
    /** @var  SrmTenderUserAccessEditLogRepository */
    private $srmTenderUserAccessEditLogRepository;

    public function __construct(SrmTenderUserAccessEditLogRepository $srmTenderUserAccessEditLogRepo)
    {
        $this->srmTenderUserAccessEditLogRepository = $srmTenderUserAccessEditLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/srmTenderUserAccessEditLogs",
     *      summary="getSrmTenderUserAccessEditLogList",
     *      tags={"SrmTenderUserAccessEditLog"},
     *      description="Get all SrmTenderUserAccessEditLogs",
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
     *                  @OA\Items(ref="#/definitions/SrmTenderUserAccessEditLog")
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
        $this->srmTenderUserAccessEditLogRepository->pushCriteria(new RequestCriteria($request));
        $this->srmTenderUserAccessEditLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $srmTenderUserAccessEditLogs = $this->srmTenderUserAccessEditLogRepository->all();

        return $this->sendResponse($srmTenderUserAccessEditLogs->toArray(), 'Srm Tender User Access Edit Logs retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/srmTenderUserAccessEditLogs",
     *      summary="createSrmTenderUserAccessEditLog",
     *      tags={"SrmTenderUserAccessEditLog"},
     *      description="Create SrmTenderUserAccessEditLog",
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
     *                  ref="#/definitions/SrmTenderUserAccessEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSrmTenderUserAccessEditLogAPIRequest $request)
    {
        $input = $request->all();

        $srmTenderUserAccessEditLog = $this->srmTenderUserAccessEditLogRepository->create($input);

        return $this->sendResponse($srmTenderUserAccessEditLog->toArray(), 'Srm Tender User Access Edit Log saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/srmTenderUserAccessEditLogs/{id}",
     *      summary="getSrmTenderUserAccessEditLogItem",
     *      tags={"SrmTenderUserAccessEditLog"},
     *      description="Get SrmTenderUserAccessEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SrmTenderUserAccessEditLog",
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
     *                  ref="#/definitions/SrmTenderUserAccessEditLog"
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
        /** @var SrmTenderUserAccessEditLog $srmTenderUserAccessEditLog */
        $srmTenderUserAccessEditLog = $this->srmTenderUserAccessEditLogRepository->findWithoutFail($id);

        if (empty($srmTenderUserAccessEditLog)) {
            return $this->sendError('Srm Tender User Access Edit Log not found');
        }

        return $this->sendResponse($srmTenderUserAccessEditLog->toArray(), 'Srm Tender User Access Edit Log retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/srmTenderUserAccessEditLogs/{id}",
     *      summary="updateSrmTenderUserAccessEditLog",
     *      tags={"SrmTenderUserAccessEditLog"},
     *      description="Update SrmTenderUserAccessEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SrmTenderUserAccessEditLog",
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
     *                  ref="#/definitions/SrmTenderUserAccessEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSrmTenderUserAccessEditLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var SrmTenderUserAccessEditLog $srmTenderUserAccessEditLog */
        $srmTenderUserAccessEditLog = $this->srmTenderUserAccessEditLogRepository->findWithoutFail($id);

        if (empty($srmTenderUserAccessEditLog)) {
            return $this->sendError('Srm Tender User Access Edit Log not found');
        }

        $srmTenderUserAccessEditLog = $this->srmTenderUserAccessEditLogRepository->update($input, $id);

        return $this->sendResponse($srmTenderUserAccessEditLog->toArray(), 'SrmTenderUserAccessEditLog updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/srmTenderUserAccessEditLogs/{id}",
     *      summary="deleteSrmTenderUserAccessEditLog",
     *      tags={"SrmTenderUserAccessEditLog"},
     *      description="Delete SrmTenderUserAccessEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SrmTenderUserAccessEditLog",
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
        /** @var SrmTenderUserAccessEditLog $srmTenderUserAccessEditLog */
        $srmTenderUserAccessEditLog = $this->srmTenderUserAccessEditLogRepository->findWithoutFail($id);

        if (empty($srmTenderUserAccessEditLog)) {
            return $this->sendError('Srm Tender User Access Edit Log not found');
        }

        $srmTenderUserAccessEditLog->delete();

        return $this->sendSuccess('Srm Tender User Access Edit Log deleted successfully');
    }
}
