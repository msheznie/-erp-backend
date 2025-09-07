<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReportTemplateDocumentAPIRequest;
use App\Http\Requests\API\UpdateReportTemplateDocumentAPIRequest;
use App\Models\ReportTemplateDocument;
use App\Repositories\ReportTemplateDocumentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ReportTemplateDocumentController
 * @package App\Http\Controllers\API
 */

class ReportTemplateDocumentAPIController extends AppBaseController
{
    /** @var  ReportTemplateDocumentRepository */
    private $reportTemplateDocumentRepository;

    public function __construct(ReportTemplateDocumentRepository $reportTemplateDocumentRepo)
    {
        $this->reportTemplateDocumentRepository = $reportTemplateDocumentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateDocuments",
     *      summary="Get a listing of the ReportTemplateDocuments.",
     *      tags={"ReportTemplateDocument"},
     *      description="Get all ReportTemplateDocuments",
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
     *                  @SWG\Items(ref="#/definitions/ReportTemplateDocument")
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
        $this->reportTemplateDocumentRepository->pushCriteria(new RequestCriteria($request));
        $this->reportTemplateDocumentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reportTemplateDocuments = $this->reportTemplateDocumentRepository->all();

        return $this->sendResponse($reportTemplateDocuments->toArray(), trans('custom.report_template_documents_retrieved_successfully'));
    }

    /**
     * @param CreateReportTemplateDocumentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/reportTemplateDocuments",
     *      summary="Store a newly created ReportTemplateDocument in storage",
     *      tags={"ReportTemplateDocument"},
     *      description="Store ReportTemplateDocument",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateDocument that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateDocument")
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
     *                  ref="#/definitions/ReportTemplateDocument"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportTemplateDocumentAPIRequest $request)
    {
        $input = $request->all();

        $reportTemplateDocuments = $this->reportTemplateDocumentRepository->create($input);

        return $this->sendResponse($reportTemplateDocuments->toArray(), trans('custom.report_template_document_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateDocuments/{id}",
     *      summary="Display the specified ReportTemplateDocument",
     *      tags={"ReportTemplateDocument"},
     *      description="Get ReportTemplateDocument",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateDocument",
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
     *                  ref="#/definitions/ReportTemplateDocument"
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
        /** @var ReportTemplateDocument $reportTemplateDocument */
        $reportTemplateDocument = $this->reportTemplateDocumentRepository->findWithoutFail($id);

        if (empty($reportTemplateDocument)) {
            return $this->sendError(trans('custom.report_template_document_not_found'));
        }

        return $this->sendResponse($reportTemplateDocument->toArray(), trans('custom.report_template_document_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateReportTemplateDocumentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/reportTemplateDocuments/{id}",
     *      summary="Update the specified ReportTemplateDocument in storage",
     *      tags={"ReportTemplateDocument"},
     *      description="Update ReportTemplateDocument",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateDocument",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateDocument that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateDocument")
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
     *                  ref="#/definitions/ReportTemplateDocument"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportTemplateDocumentAPIRequest $request)
    {
        $input = $request->all();

        /** @var ReportTemplateDocument $reportTemplateDocument */
        $reportTemplateDocument = $this->reportTemplateDocumentRepository->findWithoutFail($id);

        if (empty($reportTemplateDocument)) {
            return $this->sendError(trans('custom.report_template_document_not_found'));
        }

        $reportTemplateDocument = $this->reportTemplateDocumentRepository->update($input, $id);

        return $this->sendResponse($reportTemplateDocument->toArray(), trans('custom.reporttemplatedocument_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/reportTemplateDocuments/{id}",
     *      summary="Remove the specified ReportTemplateDocument from storage",
     *      tags={"ReportTemplateDocument"},
     *      description="Delete ReportTemplateDocument",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateDocument",
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
        /** @var ReportTemplateDocument $reportTemplateDocument */
        $reportTemplateDocument = $this->reportTemplateDocumentRepository->findWithoutFail($id);

        if (empty($reportTemplateDocument)) {
            return $this->sendError(trans('custom.report_template_document_not_found'));
        }

        $reportTemplateDocument->delete();

        return $this->sendResponse($id, trans('custom.report_template_document_deleted_successfully'));
    }
}
