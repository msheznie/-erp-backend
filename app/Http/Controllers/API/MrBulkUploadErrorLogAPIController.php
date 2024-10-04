<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMrBulkUploadErrorLogAPIRequest;
use App\Http\Requests\API\UpdateMrBulkUploadErrorLogAPIRequest;
use App\Models\MrBulkUploadErrorLog;
use App\Repositories\MrBulkUploadErrorLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MrBulkUploadErrorLogController
 * @package App\Http\Controllers\API
 */

class MrBulkUploadErrorLogAPIController extends AppBaseController
{
    /** @var  MrBulkUploadErrorLogRepository */
    private $mrBulkUploadErrorLogRepository;

    public function __construct(MrBulkUploadErrorLogRepository $mrBulkUploadErrorLogRepo)
    {
        $this->mrBulkUploadErrorLogRepository = $mrBulkUploadErrorLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/mrBulkUploadErrorLogs",
     *      summary="getMrBulkUploadErrorLogList",
     *      tags={"MrBulkUploadErrorLog"},
     *      description="Get all MrBulkUploadErrorLogs",
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
     *                  @OA\Items(ref="#/definitions/MrBulkUploadErrorLog")
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
        $this->mrBulkUploadErrorLogRepository->pushCriteria(new RequestCriteria($request));
        $this->mrBulkUploadErrorLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $mrBulkUploadErrorLogs = $this->mrBulkUploadErrorLogRepository->all();

        return $this->sendResponse($mrBulkUploadErrorLogs->toArray(), 'Mr Bulk Upload Error Logs retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/mrBulkUploadErrorLogs",
     *      summary="createMrBulkUploadErrorLog",
     *      tags={"MrBulkUploadErrorLog"},
     *      description="Create MrBulkUploadErrorLog",
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
     *                  ref="#/definitions/MrBulkUploadErrorLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMrBulkUploadErrorLogAPIRequest $request)
    {
        $input = $request->all();

        $mrBulkUploadErrorLog = $this->mrBulkUploadErrorLogRepository->create($input);

        return $this->sendResponse($mrBulkUploadErrorLog->toArray(), 'Mr Bulk Upload Error Log saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/mrBulkUploadErrorLogs/{id}",
     *      summary="getMrBulkUploadErrorLogItem",
     *      tags={"MrBulkUploadErrorLog"},
     *      description="Get MrBulkUploadErrorLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of MrBulkUploadErrorLog",
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
     *                  ref="#/definitions/MrBulkUploadErrorLog"
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
        /** @var MrBulkUploadErrorLog $mrBulkUploadErrorLog */
        $mrBulkUploadErrorLog = $this->mrBulkUploadErrorLogRepository->findWithoutFail($id);

        if (empty($mrBulkUploadErrorLog)) {
            return $this->sendError('Mr Bulk Upload Error Log not found');
        }

        return $this->sendResponse($mrBulkUploadErrorLog->toArray(), 'Mr Bulk Upload Error Log retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/mrBulkUploadErrorLogs/{id}",
     *      summary="updateMrBulkUploadErrorLog",
     *      tags={"MrBulkUploadErrorLog"},
     *      description="Update MrBulkUploadErrorLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of MrBulkUploadErrorLog",
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
     *                  ref="#/definitions/MrBulkUploadErrorLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMrBulkUploadErrorLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var MrBulkUploadErrorLog $mrBulkUploadErrorLog */
        $mrBulkUploadErrorLog = $this->mrBulkUploadErrorLogRepository->findWithoutFail($id);

        if (empty($mrBulkUploadErrorLog)) {
            return $this->sendError('Mr Bulk Upload Error Log not found');
        }

        $mrBulkUploadErrorLog = $this->mrBulkUploadErrorLogRepository->update($input, $id);

        return $this->sendResponse($mrBulkUploadErrorLog->toArray(), 'MrBulkUploadErrorLog updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/mrBulkUploadErrorLogs/{id}",
     *      summary="deleteMrBulkUploadErrorLog",
     *      tags={"MrBulkUploadErrorLog"},
     *      description="Delete MrBulkUploadErrorLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of MrBulkUploadErrorLog",
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
        /** @var MrBulkUploadErrorLog $mrBulkUploadErrorLog */
        $mrBulkUploadErrorLog = $this->mrBulkUploadErrorLogRepository->findWithoutFail($id);

        if (empty($mrBulkUploadErrorLog)) {
            return $this->sendError('Mr Bulk Upload Error Log not found');
        }

        $mrBulkUploadErrorLog->delete();

        return $this->sendSuccess('Mr Bulk Upload Error Log deleted successfully');
    }

    function getMrItemBulkUploadError(Request $request)
    {
        $requestId = $request['requestId'];
        $errorMsg = $this->mrBulkUploadErrorLogRepository->getBulkUploadErrors($requestId);

        return $this->sendResponse($errorMsg, 'Purchase order item upload error log status fetched successfully');
    }

    function deleteMrErrorLog($id)
    {
        MrBulkUploadErrorLog::where('documentSystemID', trim($id))->delete();
        return $this->sendResponse([], 'Po Bulk Upload Error Log deleted successfully');
    }
}
