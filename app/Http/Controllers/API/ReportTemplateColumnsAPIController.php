<?php
/**
 * =============================================
 * -- File Name : ReportTemplateColumnsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report Template
 * -- Author : Mubashir
 * -- Create date : 27 - December 2018
 * -- Description :  This file contains the all CRUD for Report template detail
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReportTemplateColumnsAPIRequest;
use App\Http\Requests\API\UpdateReportTemplateColumnsAPIRequest;
use App\Models\ReportTemplateColumns;
use App\Repositories\ReportTemplateColumnsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ReportTemplateColumnsController
 * @package App\Http\Controllers\API
 */

class ReportTemplateColumnsAPIController extends AppBaseController
{
    /** @var  ReportTemplateColumnsRepository */
    private $reportTemplateColumnsRepository;

    public function __construct(ReportTemplateColumnsRepository $reportTemplateColumnsRepo)
    {
        $this->reportTemplateColumnsRepository = $reportTemplateColumnsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateColumns",
     *      summary="Get a listing of the ReportTemplateColumns.",
     *      tags={"ReportTemplateColumns"},
     *      description="Get all ReportTemplateColumns",
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
     *                  @SWG\Items(ref="#/definitions/ReportTemplateColumns")
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
        $this->reportTemplateColumnsRepository->pushCriteria(new RequestCriteria($request));
        $this->reportTemplateColumnsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reportTemplateColumns = $this->reportTemplateColumnsRepository->all();

        return $this->sendResponse($reportTemplateColumns->toArray(), trans('custom.report_template_columns_retrieved_successfully'));
    }

    /**
     * @param CreateReportTemplateColumnsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/reportTemplateColumns",
     *      summary="Store a newly created ReportTemplateColumns in storage",
     *      tags={"ReportTemplateColumns"},
     *      description="Store ReportTemplateColumns",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateColumns that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateColumns")
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
     *                  ref="#/definitions/ReportTemplateColumns"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportTemplateColumnsAPIRequest $request)
    {
        $input = $request->all();

        $reportTemplateColumns = $this->reportTemplateColumnsRepository->create($input);

        return $this->sendResponse($reportTemplateColumns->toArray(), trans('custom.report_template_columns_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateColumns/{id}",
     *      summary="Display the specified ReportTemplateColumns",
     *      tags={"ReportTemplateColumns"},
     *      description="Get ReportTemplateColumns",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateColumns",
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
     *                  ref="#/definitions/ReportTemplateColumns"
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
        /** @var ReportTemplateColumns $reportTemplateColumns */
        $reportTemplateColumns = $this->reportTemplateColumnsRepository->findWithoutFail($id);

        if (empty($reportTemplateColumns)) {
            return $this->sendError(trans('custom.report_template_columns_not_found'));
        }

        return $this->sendResponse($reportTemplateColumns->toArray(), trans('custom.report_template_columns_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateReportTemplateColumnsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/reportTemplateColumns/{id}",
     *      summary="Update the specified ReportTemplateColumns in storage",
     *      tags={"ReportTemplateColumns"},
     *      description="Update ReportTemplateColumns",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateColumns",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateColumns that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateColumns")
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
     *                  ref="#/definitions/ReportTemplateColumns"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportTemplateColumnsAPIRequest $request)
    {
        $input = $request->all();

        /** @var ReportTemplateColumns $reportTemplateColumns */
        $reportTemplateColumns = $this->reportTemplateColumnsRepository->findWithoutFail($id);

        if (empty($reportTemplateColumns)) {
            return $this->sendError(trans('custom.report_template_columns_not_found'));
        }

        $reportTemplateColumns = $this->reportTemplateColumnsRepository->update($input, $id);

        return $this->sendResponse($reportTemplateColumns->toArray(), trans('custom.reporttemplatecolumns_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/reportTemplateColumns/{id}",
     *      summary="Remove the specified ReportTemplateColumns from storage",
     *      tags={"ReportTemplateColumns"},
     *      description="Delete ReportTemplateColumns",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateColumns",
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
        /** @var ReportTemplateColumns $reportTemplateColumns */
        $reportTemplateColumns = $this->reportTemplateColumnsRepository->findWithoutFail($id);

        if (empty($reportTemplateColumns)) {
            return $this->sendError(trans('custom.report_template_columns_not_found'));
        }

        $reportTemplateColumns->delete();

        return $this->sendResponse($id, trans('custom.report_template_columns_deleted_successfully'));
    }
}
