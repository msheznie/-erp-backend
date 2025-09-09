<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReportColumnTemplateAPIRequest;
use App\Http\Requests\API\UpdateReportColumnTemplateAPIRequest;
use App\Models\ReportColumnTemplate;
use App\Repositories\ReportColumnTemplateRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ReportColumnTemplateController
 * @package App\Http\Controllers\API
 */

class ReportColumnTemplateAPIController extends AppBaseController
{
    /** @var  ReportColumnTemplateRepository */
    private $reportColumnTemplateRepository;

    public function __construct(ReportColumnTemplateRepository $reportColumnTemplateRepo)
    {
        $this->reportColumnTemplateRepository = $reportColumnTemplateRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportColumnTemplates",
     *      summary="Get a listing of the ReportColumnTemplates.",
     *      tags={"ReportColumnTemplate"},
     *      description="Get all ReportColumnTemplates",
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
     *                  @SWG\Items(ref="#/definitions/ReportColumnTemplate")
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
        $this->reportColumnTemplateRepository->pushCriteria(new RequestCriteria($request));
        $this->reportColumnTemplateRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reportColumnTemplates = $this->reportColumnTemplateRepository->all();

        return $this->sendResponse($reportColumnTemplates->toArray(), trans('custom.report_column_templates_retrieved_successfully'));
    }

    /**
     * @param CreateReportColumnTemplateAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/reportColumnTemplates",
     *      summary="Store a newly created ReportColumnTemplate in storage",
     *      tags={"ReportColumnTemplate"},
     *      description="Store ReportColumnTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportColumnTemplate that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportColumnTemplate")
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
     *                  ref="#/definitions/ReportColumnTemplate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportColumnTemplateAPIRequest $request)
    {
        $input = $request->all();

        $reportColumnTemplate = $this->reportColumnTemplateRepository->create($input);

        return $this->sendResponse($reportColumnTemplate->toArray(), trans('custom.report_column_template_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportColumnTemplates/{id}",
     *      summary="Display the specified ReportColumnTemplate",
     *      tags={"ReportColumnTemplate"},
     *      description="Get ReportColumnTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportColumnTemplate",
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
     *                  ref="#/definitions/ReportColumnTemplate"
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
        /** @var ReportColumnTemplate $reportColumnTemplate */
        $reportColumnTemplate = $this->reportColumnTemplateRepository->findWithoutFail($id);

        if (empty($reportColumnTemplate)) {
            return $this->sendError(trans('custom.report_column_template_not_found'));
        }

        return $this->sendResponse($reportColumnTemplate->toArray(), trans('custom.report_column_template_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateReportColumnTemplateAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/reportColumnTemplates/{id}",
     *      summary="Update the specified ReportColumnTemplate in storage",
     *      tags={"ReportColumnTemplate"},
     *      description="Update ReportColumnTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportColumnTemplate",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportColumnTemplate that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportColumnTemplate")
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
     *                  ref="#/definitions/ReportColumnTemplate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportColumnTemplateAPIRequest $request)
    {
        $input = $request->all();

        /** @var ReportColumnTemplate $reportColumnTemplate */
        $reportColumnTemplate = $this->reportColumnTemplateRepository->findWithoutFail($id);

        if (empty($reportColumnTemplate)) {
            return $this->sendError(trans('custom.report_column_template_not_found'));
        }

        $reportColumnTemplate = $this->reportColumnTemplateRepository->update($input, $id);

        return $this->sendResponse($reportColumnTemplate->toArray(), trans('custom.reportcolumntemplate_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/reportColumnTemplates/{id}",
     *      summary="Remove the specified ReportColumnTemplate from storage",
     *      tags={"ReportColumnTemplate"},
     *      description="Delete ReportColumnTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportColumnTemplate",
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
        /** @var ReportColumnTemplate $reportColumnTemplate */
        $reportColumnTemplate = $this->reportColumnTemplateRepository->findWithoutFail($id);

        if (empty($reportColumnTemplate)) {
            return $this->sendError(trans('custom.report_column_template_not_found'));
        }

        $reportColumnTemplate->delete();

        return $this->sendResponse($id, trans('custom.report_column_template_deleted_successfully'));
    }
}
