<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReportColumnTemplateDetailAPIRequest;
use App\Http\Requests\API\UpdateReportColumnTemplateDetailAPIRequest;
use App\Models\ReportColumnTemplateDetail;
use App\Repositories\ReportColumnTemplateDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ReportColumnTemplateDetailController
 * @package App\Http\Controllers\API
 */

class ReportColumnTemplateDetailAPIController extends AppBaseController
{
    /** @var  ReportColumnTemplateDetailRepository */
    private $reportColumnTemplateDetailRepository;

    public function __construct(ReportColumnTemplateDetailRepository $reportColumnTemplateDetailRepo)
    {
        $this->reportColumnTemplateDetailRepository = $reportColumnTemplateDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportColumnTemplateDetails",
     *      summary="Get a listing of the ReportColumnTemplateDetails.",
     *      tags={"ReportColumnTemplateDetail"},
     *      description="Get all ReportColumnTemplateDetails",
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
     *                  @SWG\Items(ref="#/definitions/ReportColumnTemplateDetail")
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
        $this->reportColumnTemplateDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->reportColumnTemplateDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reportColumnTemplateDetails = $this->reportColumnTemplateDetailRepository->all();

        return $this->sendResponse($reportColumnTemplateDetails->toArray(), trans('custom.report_column_template_details_retrieved_successfu'));
    }

    /**
     * @param CreateReportColumnTemplateDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/reportColumnTemplateDetails",
     *      summary="Store a newly created ReportColumnTemplateDetail in storage",
     *      tags={"ReportColumnTemplateDetail"},
     *      description="Store ReportColumnTemplateDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportColumnTemplateDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportColumnTemplateDetail")
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
     *                  ref="#/definitions/ReportColumnTemplateDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportColumnTemplateDetailAPIRequest $request)
    {
        $input = $request->all();

        $reportColumnTemplateDetail = $this->reportColumnTemplateDetailRepository->create($input);

        return $this->sendResponse($reportColumnTemplateDetail->toArray(), trans('custom.report_column_template_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportColumnTemplateDetails/{id}",
     *      summary="Display the specified ReportColumnTemplateDetail",
     *      tags={"ReportColumnTemplateDetail"},
     *      description="Get ReportColumnTemplateDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportColumnTemplateDetail",
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
     *                  ref="#/definitions/ReportColumnTemplateDetail"
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
        /** @var ReportColumnTemplateDetail $reportColumnTemplateDetail */
        $reportColumnTemplateDetail = $this->reportColumnTemplateDetailRepository->findWithoutFail($id);

        if (empty($reportColumnTemplateDetail)) {
            return $this->sendError(trans('custom.report_column_template_detail_not_found'));
        }

        return $this->sendResponse($reportColumnTemplateDetail->toArray(), trans('custom.report_column_template_detail_retrieved_successful'));
    }

    /**
     * @param int $id
     * @param UpdateReportColumnTemplateDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/reportColumnTemplateDetails/{id}",
     *      summary="Update the specified ReportColumnTemplateDetail in storage",
     *      tags={"ReportColumnTemplateDetail"},
     *      description="Update ReportColumnTemplateDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportColumnTemplateDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportColumnTemplateDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportColumnTemplateDetail")
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
     *                  ref="#/definitions/ReportColumnTemplateDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportColumnTemplateDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var ReportColumnTemplateDetail $reportColumnTemplateDetail */
        $reportColumnTemplateDetail = $this->reportColumnTemplateDetailRepository->findWithoutFail($id);

        if (empty($reportColumnTemplateDetail)) {
            return $this->sendError(trans('custom.report_column_template_detail_not_found'));
        }

        $reportColumnTemplateDetail = $this->reportColumnTemplateDetailRepository->update($input, $id);

        return $this->sendResponse($reportColumnTemplateDetail->toArray(), trans('custom.reportcolumntemplatedetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/reportColumnTemplateDetails/{id}",
     *      summary="Remove the specified ReportColumnTemplateDetail from storage",
     *      tags={"ReportColumnTemplateDetail"},
     *      description="Delete ReportColumnTemplateDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportColumnTemplateDetail",
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
        /** @var ReportColumnTemplateDetail $reportColumnTemplateDetail */
        $reportColumnTemplateDetail = $this->reportColumnTemplateDetailRepository->findWithoutFail($id);

        if (empty($reportColumnTemplateDetail)) {
            return $this->sendError(trans('custom.report_column_template_detail_not_found'));
        }

        $reportColumnTemplateDetail->delete();

        return $this->sendResponse($id, trans('custom.report_column_template_detail_deleted_successfully'));
    }
}
