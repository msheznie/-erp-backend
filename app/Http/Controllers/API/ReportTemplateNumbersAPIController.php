<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReportTemplateNumbersAPIRequest;
use App\Http\Requests\API\UpdateReportTemplateNumbersAPIRequest;
use App\Models\ReportTemplateNumbers;
use App\Repositories\ReportTemplateNumbersRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ReportTemplateNumbersController
 * @package App\Http\Controllers\API
 */

class ReportTemplateNumbersAPIController extends AppBaseController
{
    /** @var  ReportTemplateNumbersRepository */
    private $reportTemplateNumbersRepository;

    public function __construct(ReportTemplateNumbersRepository $reportTemplateNumbersRepo)
    {
        $this->reportTemplateNumbersRepository = $reportTemplateNumbersRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateNumbers",
     *      summary="Get a listing of the ReportTemplateNumbers.",
     *      tags={"ReportTemplateNumbers"},
     *      description="Get all ReportTemplateNumbers",
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
     *                  @SWG\Items(ref="#/definitions/ReportTemplateNumbers")
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
        $this->reportTemplateNumbersRepository->pushCriteria(new RequestCriteria($request));
        $this->reportTemplateNumbersRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reportTemplateNumbers = $this->reportTemplateNumbersRepository->all();

        return $this->sendResponse($reportTemplateNumbers->toArray(), trans('custom.report_template_numbers_retrieved_successfully'));
    }

    /**
     * @param CreateReportTemplateNumbersAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/reportTemplateNumbers",
     *      summary="Store a newly created ReportTemplateNumbers in storage",
     *      tags={"ReportTemplateNumbers"},
     *      description="Store ReportTemplateNumbers",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateNumbers that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateNumbers")
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
     *                  ref="#/definitions/ReportTemplateNumbers"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportTemplateNumbersAPIRequest $request)
    {
        $input = $request->all();

        $reportTemplateNumbers = $this->reportTemplateNumbersRepository->create($input);

        return $this->sendResponse($reportTemplateNumbers->toArray(), trans('custom.report_template_numbers_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateNumbers/{id}",
     *      summary="Display the specified ReportTemplateNumbers",
     *      tags={"ReportTemplateNumbers"},
     *      description="Get ReportTemplateNumbers",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateNumbers",
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
     *                  ref="#/definitions/ReportTemplateNumbers"
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
        /** @var ReportTemplateNumbers $reportTemplateNumbers */
        $reportTemplateNumbers = $this->reportTemplateNumbersRepository->findWithoutFail($id);

        if (empty($reportTemplateNumbers)) {
            return $this->sendError(trans('custom.report_template_numbers_not_found'));
        }

        return $this->sendResponse($reportTemplateNumbers->toArray(), trans('custom.report_template_numbers_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateReportTemplateNumbersAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/reportTemplateNumbers/{id}",
     *      summary="Update the specified ReportTemplateNumbers in storage",
     *      tags={"ReportTemplateNumbers"},
     *      description="Update ReportTemplateNumbers",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateNumbers",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateNumbers that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateNumbers")
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
     *                  ref="#/definitions/ReportTemplateNumbers"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportTemplateNumbersAPIRequest $request)
    {
        $input = $request->all();

        /** @var ReportTemplateNumbers $reportTemplateNumbers */
        $reportTemplateNumbers = $this->reportTemplateNumbersRepository->findWithoutFail($id);

        if (empty($reportTemplateNumbers)) {
            return $this->sendError(trans('custom.report_template_numbers_not_found'));
        }

        $reportTemplateNumbers = $this->reportTemplateNumbersRepository->update($input, $id);

        return $this->sendResponse($reportTemplateNumbers->toArray(), trans('custom.reporttemplatenumbers_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/reportTemplateNumbers/{id}",
     *      summary="Remove the specified ReportTemplateNumbers from storage",
     *      tags={"ReportTemplateNumbers"},
     *      description="Delete ReportTemplateNumbers",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateNumbers",
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
        /** @var ReportTemplateNumbers $reportTemplateNumbers */
        $reportTemplateNumbers = $this->reportTemplateNumbersRepository->findWithoutFail($id);

        if (empty($reportTemplateNumbers)) {
            return $this->sendError(trans('custom.report_template_numbers_not_found'));
        }

        $reportTemplateNumbers->delete();

        return $this->sendResponse($id, trans('custom.report_template_numbers_deleted_successfully'));
    }
}
