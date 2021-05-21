<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomReportColumnsAPIRequest;
use App\Http\Requests\API\UpdateCustomReportColumnsAPIRequest;
use App\Models\CustomReportColumns;
use App\Repositories\CustomReportColumnsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomReportColumnsController
 * @package App\Http\Controllers\API
 */

class CustomReportColumnsAPIController extends AppBaseController
{
    /** @var  CustomReportColumnsRepository */
    private $customReportColumnsRepository;

    public function __construct(CustomReportColumnsRepository $customReportColumnsRepo)
    {
        $this->customReportColumnsRepository = $customReportColumnsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customReportColumns",
     *      summary="Get a listing of the CustomReportColumns.",
     *      tags={"CustomReportColumns"},
     *      description="Get all CustomReportColumns",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/CustomReportColumns")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->customReportColumnsRepository->pushCriteria(new RequestCriteria($request));
        $this->customReportColumnsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customReportColumns = $this->customReportColumnsRepository->all();

        return $this->sendResponse($customReportColumns->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.custom_report_columns')]));
    }

    /**
     * @param CreateCustomReportColumnsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customReportColumns",
     *      summary="Store a newly created CustomReportColumns in storage",
     *      tags={"CustomReportColumns"},
     *      description="Store CustomReportColumns",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomReportColumns that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomReportColumns")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/CustomReportColumns"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomReportColumnsAPIRequest $request)
    {
        $input = $request->all();

        $customReportColumns = $this->customReportColumnsRepository->create($input);

        return $this->sendResponse($customReportColumns->toArray(), trans('custom.save', ['attribute' => trans('custom.custom_report_columns')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customReportColumns/{id}",
     *      summary="Display the specified CustomReportColumns",
     *      tags={"CustomReportColumns"},
     *      description="Get CustomReportColumns",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomReportColumns",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/CustomReportColumns"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var CustomReportColumns $customReportColumns */
        $customReportColumns = $this->customReportColumnsRepository->findWithoutFail($id);

        if (empty($customReportColumns)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_report_columns')]));
        }

        return $this->sendResponse($customReportColumns->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.custom_report_columns')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomReportColumnsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customReportColumns/{id}",
     *      summary="Update the specified CustomReportColumns in storage",
     *      tags={"CustomReportColumns"},
     *      description="Update CustomReportColumns",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomReportColumns",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomReportColumns that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomReportColumns")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/CustomReportColumns"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomReportColumnsAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomReportColumns $customReportColumns */
        $customReportColumns = $this->customReportColumnsRepository->findWithoutFail($id);

        if (empty($customReportColumns)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_report_columns')]));
        }

        $customReportColumns = $this->customReportColumnsRepository->update($input, $id);

        return $this->sendResponse($customReportColumns->toArray(), trans('custom.update', ['attribute' => trans('custom.custom_report_columns')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customReportColumns/{id}",
     *      summary="Remove the specified CustomReportColumns from storage",
     *      tags={"CustomReportColumns"},
     *      description="Delete CustomReportColumns",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomReportColumns",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var CustomReportColumns $customReportColumns */
        $customReportColumns = $this->customReportColumnsRepository->findWithoutFail($id);

        if (empty($customReportColumns)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_report_columns')]));
        }

        $customReportColumns->delete();

        return $this->sendSuccess(trans('custom.delete', ['attribute' => trans('custom.custom_report_columns')]));
    }
}
