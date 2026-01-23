<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomUserReportColumnsAPIRequest;
use App\Http\Requests\API\UpdateCustomUserReportColumnsAPIRequest;
use App\Models\CustomUserReportColumns;
use App\Repositories\CustomUserReportColumnsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomUserReportColumnsController
 * @package App\Http\Controllers\API
 */

class CustomUserReportColumnsAPIController extends AppBaseController
{
    /** @var  CustomUserReportColumnsRepository */
    private $customUserReportColumnsRepository;

    public function __construct(CustomUserReportColumnsRepository $customUserReportColumnsRepo)
    {
        $this->customUserReportColumnsRepository = $customUserReportColumnsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customUserReportColumns",
     *      summary="Get a listing of the CustomUserReportColumns.",
     *      tags={"CustomUserReportColumns"},
     *      description="Get all CustomUserReportColumns",
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
     *                  @SWG\Items(ref="#/definitions/CustomUserReportColumns")
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
        $this->customUserReportColumnsRepository->pushCriteria(new RequestCriteria($request));
        $this->customUserReportColumnsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customUserReportColumns = $this->customUserReportColumnsRepository->all();

        return $this->sendResponse($customUserReportColumns->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.custom_user_report_columns')]));
    }

    /**
     * @param CreateCustomUserReportColumnsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customUserReportColumns",
     *      summary="Store a newly created CustomUserReportColumns in storage",
     *      tags={"CustomUserReportColumns"},
     *      description="Store CustomUserReportColumns",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomUserReportColumns that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomUserReportColumns")
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
     *                  ref="#/definitions/CustomUserReportColumns"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomUserReportColumnsAPIRequest $request)
    {
        $input = $request->all();

        $customUserReportColumns = $this->customUserReportColumnsRepository->create($input);

        return $this->sendResponse($customUserReportColumns->toArray(), trans('custom.save', ['attribute' => trans('custom.custom_user_report_columns')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customUserReportColumns/{id}",
     *      summary="Display the specified CustomUserReportColumns",
     *      tags={"CustomUserReportColumns"},
     *      description="Get CustomUserReportColumns",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomUserReportColumns",
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
     *                  ref="#/definitions/CustomUserReportColumns"
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
        /** @var CustomUserReportColumns $customUserReportColumns */
        $customUserReportColumns = $this->customUserReportColumnsRepository->findWithoutFail($id);

        if (empty($customUserReportColumns)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_user_report_columns')]));
        }

        return $this->sendResponse($customUserReportColumns->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.custom_user_report_columns')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomUserReportColumnsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customUserReportColumns/{id}",
     *      summary="Update the specified CustomUserReportColumns in storage",
     *      tags={"CustomUserReportColumns"},
     *      description="Update CustomUserReportColumns",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomUserReportColumns",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomUserReportColumns that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomUserReportColumns")
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
     *                  ref="#/definitions/CustomUserReportColumns"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomUserReportColumnsAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomUserReportColumns $customUserReportColumns */
        $customUserReportColumns = $this->customUserReportColumnsRepository->findWithoutFail($id);

        if (empty($customUserReportColumns)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_user_report_columns')]));
        }

        $customUserReportColumns = $this->customUserReportColumnsRepository->update($input, $id);

        return $this->sendResponse($customUserReportColumns->toArray(), trans('custom.update', ['attribute' => trans('custom.custom_user_report_columns')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customUserReportColumns/{id}",
     *      summary="Remove the specified CustomUserReportColumns from storage",
     *      tags={"CustomUserReportColumns"},
     *      description="Delete CustomUserReportColumns",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomUserReportColumns",
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
        /** @var CustomUserReportColumns $customUserReportColumns */
        $customUserReportColumns = $this->customUserReportColumnsRepository->findWithoutFail($id);

        if (empty($customUserReportColumns)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_user_report_columns')]));
        }

        $customUserReportColumns->delete();

        return $this->sendSuccess(trans('custom.delete', ['attribute' => trans('custom.custom_user_report_columns')]));
    }
}
