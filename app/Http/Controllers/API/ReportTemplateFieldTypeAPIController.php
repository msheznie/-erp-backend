<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReportTemplateFieldTypeAPIRequest;
use App\Http\Requests\API\UpdateReportTemplateFieldTypeAPIRequest;
use App\Models\ReportTemplateFieldType;
use App\Repositories\ReportTemplateFieldTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ReportTemplateFieldTypeController
 * @package App\Http\Controllers\API
 */

class ReportTemplateFieldTypeAPIController extends AppBaseController
{
    /** @var  ReportTemplateFieldTypeRepository */
    private $reportTemplateFieldTypeRepository;

    public function __construct(ReportTemplateFieldTypeRepository $reportTemplateFieldTypeRepo)
    {
        $this->reportTemplateFieldTypeRepository = $reportTemplateFieldTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateFieldTypes",
     *      summary="Get a listing of the ReportTemplateFieldTypes.",
     *      tags={"ReportTemplateFieldType"},
     *      description="Get all ReportTemplateFieldTypes",
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
     *                  @SWG\Items(ref="#/definitions/ReportTemplateFieldType")
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
        $this->reportTemplateFieldTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->reportTemplateFieldTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reportTemplateFieldTypes = $this->reportTemplateFieldTypeRepository->all();

        return $this->sendResponse($reportTemplateFieldTypes->toArray(), trans('custom.report_template_field_types_retrieved_successfully'));
    }

    /**
     * @param CreateReportTemplateFieldTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/reportTemplateFieldTypes",
     *      summary="Store a newly created ReportTemplateFieldType in storage",
     *      tags={"ReportTemplateFieldType"},
     *      description="Store ReportTemplateFieldType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateFieldType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateFieldType")
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
     *                  ref="#/definitions/ReportTemplateFieldType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportTemplateFieldTypeAPIRequest $request)
    {
        $input = $request->all();

        $reportTemplateFieldTypes = $this->reportTemplateFieldTypeRepository->create($input);

        return $this->sendResponse($reportTemplateFieldTypes->toArray(), trans('custom.report_template_field_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateFieldTypes/{id}",
     *      summary="Display the specified ReportTemplateFieldType",
     *      tags={"ReportTemplateFieldType"},
     *      description="Get ReportTemplateFieldType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateFieldType",
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
     *                  ref="#/definitions/ReportTemplateFieldType"
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
        /** @var ReportTemplateFieldType $reportTemplateFieldType */
        $reportTemplateFieldType = $this->reportTemplateFieldTypeRepository->findWithoutFail($id);

        if (empty($reportTemplateFieldType)) {
            return $this->sendError(trans('custom.report_template_field_type_not_found'));
        }

        return $this->sendResponse($reportTemplateFieldType->toArray(), trans('custom.report_template_field_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateReportTemplateFieldTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/reportTemplateFieldTypes/{id}",
     *      summary="Update the specified ReportTemplateFieldType in storage",
     *      tags={"ReportTemplateFieldType"},
     *      description="Update ReportTemplateFieldType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateFieldType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateFieldType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateFieldType")
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
     *                  ref="#/definitions/ReportTemplateFieldType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportTemplateFieldTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var ReportTemplateFieldType $reportTemplateFieldType */
        $reportTemplateFieldType = $this->reportTemplateFieldTypeRepository->findWithoutFail($id);

        if (empty($reportTemplateFieldType)) {
            return $this->sendError(trans('custom.report_template_field_type_not_found'));
        }

        $reportTemplateFieldType = $this->reportTemplateFieldTypeRepository->update($input, $id);

        return $this->sendResponse($reportTemplateFieldType->toArray(), trans('custom.reporttemplatefieldtype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/reportTemplateFieldTypes/{id}",
     *      summary="Remove the specified ReportTemplateFieldType from storage",
     *      tags={"ReportTemplateFieldType"},
     *      description="Delete ReportTemplateFieldType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateFieldType",
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
        /** @var ReportTemplateFieldType $reportTemplateFieldType */
        $reportTemplateFieldType = $this->reportTemplateFieldTypeRepository->findWithoutFail($id);

        if (empty($reportTemplateFieldType)) {
            return $this->sendError(trans('custom.report_template_field_type_not_found'));
        }

        $reportTemplateFieldType->delete();

        return $this->sendResponse($id, trans('custom.report_template_field_type_deleted_successfully'));
    }
}
