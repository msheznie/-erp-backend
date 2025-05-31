<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReportCustomColumnAPIRequest;
use App\Http\Requests\API\UpdateReportCustomColumnAPIRequest;
use App\Models\ReportCustomColumn;
use App\Repositories\ReportCustomColumnRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ReportCustomColumnController
 * @package App\Http\Controllers\API
 */

class ReportCustomColumnAPIController extends AppBaseController
{
    /** @var  ReportCustomColumnRepository */
    private $reportCustomColumnRepository;

    public function __construct(ReportCustomColumnRepository $reportCustomColumnRepo)
    {
        $this->reportCustomColumnRepository = $reportCustomColumnRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/reportCustomColumns",
     *      summary="getReportCustomColumnList",
     *      tags={"ReportCustomColumn"},
     *      description="Get all ReportCustomColumns",
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
     *                  @OA\Items(ref="#/definitions/ReportCustomColumn")
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
        $this->reportCustomColumnRepository->pushCriteria(new RequestCriteria($request));
        $this->reportCustomColumnRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reportCustomColumns = $this->reportCustomColumnRepository->all();

        return $this->sendResponse($reportCustomColumns->toArray(), 'Report Custom Columns retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/reportCustomColumns",
     *      summary="createReportCustomColumn",
     *      tags={"ReportCustomColumn"},
     *      description="Create ReportCustomColumn",
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
     *                  ref="#/definitions/ReportCustomColumn"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportCustomColumnAPIRequest $request)
    {
        $input = $request->all();

        $reportCustomColumn = $this->reportCustomColumnRepository->create($input);

        return $this->sendResponse($reportCustomColumn->toArray(), 'Report Custom Column saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/reportCustomColumns/{id}",
     *      summary="getReportCustomColumnItem",
     *      tags={"ReportCustomColumn"},
     *      description="Get ReportCustomColumn",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ReportCustomColumn",
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
     *                  ref="#/definitions/ReportCustomColumn"
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
        /** @var ReportCustomColumn $reportCustomColumn */
        $reportCustomColumn = $this->reportCustomColumnRepository->findWithoutFail($id);

        if (empty($reportCustomColumn)) {
            return $this->sendError('Report Custom Column not found');
        }

        return $this->sendResponse($reportCustomColumn->toArray(), 'Report Custom Column retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/reportCustomColumns/{id}",
     *      summary="updateReportCustomColumn",
     *      tags={"ReportCustomColumn"},
     *      description="Update ReportCustomColumn",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ReportCustomColumn",
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
     *                  ref="#/definitions/ReportCustomColumn"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportCustomColumnAPIRequest $request)
    {
        $input = $request->all();

        /** @var ReportCustomColumn $reportCustomColumn */
        $reportCustomColumn = $this->reportCustomColumnRepository->findWithoutFail($id);

        if (empty($reportCustomColumn)) {
            return $this->sendError('Report Custom Column not found');
        }

        $reportCustomColumn = $this->reportCustomColumnRepository->update($input, $id);

        return $this->sendResponse($reportCustomColumn->toArray(), 'ReportCustomColumn updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/reportCustomColumns/{id}",
     *      summary="deleteReportCustomColumn",
     *      tags={"ReportCustomColumn"},
     *      description="Delete ReportCustomColumn",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ReportCustomColumn",
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
        /** @var ReportCustomColumn $reportCustomColumn */
        $reportCustomColumn = $this->reportCustomColumnRepository->findWithoutFail($id);

        if (empty($reportCustomColumn)) {
            return $this->sendError('Report Custom Column not found');
        }

        $reportCustomColumn->delete();

        return $this->sendSuccess('Report Custom Column deleted successfully');
    }
}
