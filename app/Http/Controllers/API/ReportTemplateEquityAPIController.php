<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReportTemplateEquityAPIRequest;
use App\Http\Requests\API\UpdateReportTemplateEquityAPIRequest;
use App\Models\ReportTemplateEquity;
use App\Repositories\ReportTemplateEquityRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\ReportTemplateLinks;


/**
 * Class ReportTemplateEquityController
 * @package App\Http\Controllers\API
 */

class ReportTemplateEquityAPIController extends AppBaseController
{
    /** @var  ReportTemplateEquityRepository */
    private $reportTemplateEquityRepository;

    public function __construct(ReportTemplateEquityRepository $reportTemplateEquityRepo)
    {
        $this->reportTemplateEquityRepository = $reportTemplateEquityRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/reportTemplateEquities",
     *      summary="getReportTemplateEquityList",
     *      tags={"ReportTemplateEquity"},
     *      description="Get all ReportTemplateEquities",
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
     *                  @OA\Items(ref="#/definitions/ReportTemplateEquity")
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
        $this->reportTemplateEquityRepository->pushCriteria(new RequestCriteria($request));
        $this->reportTemplateEquityRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reportTemplateEquities = $this->reportTemplateEquityRepository->all();

        return $this->sendResponse($reportTemplateEquities->toArray(), trans('custom.report_template_equities_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/reportTemplateEquities",
     *      summary="createReportTemplateEquity",
     *      tags={"ReportTemplateEquity"},
     *      description="Create ReportTemplateEquity",
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
     *                  ref="#/definitions/ReportTemplateEquity"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportTemplateEquityAPIRequest $request)
    {
        try {
            $input = $request->all();
            $reportTemplateEquity = $this->reportTemplateEquityRepository->create($input);
    
            return $this->sendResponse($reportTemplateEquity->toArray(), trans('custom.report_template_equity_saved_successfully'));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/reportTemplateEquities/{id}",
     *      summary="getReportTemplateEquityItem",
     *      tags={"ReportTemplateEquity"},
     *      description="Get ReportTemplateEquity",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ReportTemplateEquity",
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
     *                  ref="#/definitions/ReportTemplateEquity"
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
        /** @var ReportTemplateEquity $reportTemplateEquity */
        $reportTemplateEquity = $this->reportTemplateEquityRepository->findWithoutFail($id);

        if (empty($reportTemplateEquity)) {
            return $this->sendError(trans('custom.report_template_equity_not_found'));
        }

        return $this->sendResponse($reportTemplateEquity->toArray(), trans('custom.report_template_equity_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/reportTemplateEquities/{id}",
     *      summary="updateReportTemplateEquity",
     *      tags={"ReportTemplateEquity"},
     *      description="Update ReportTemplateEquity",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ReportTemplateEquity",
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
     *                  ref="#/definitions/ReportTemplateEquity"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportTemplateEquityAPIRequest $request)
    {
        $input = $request->all();

        /** @var ReportTemplateEquity $reportTemplateEquity */
        $reportTemplateEquity = $this->reportTemplateEquityRepository->findWithoutFail($id);

        if (empty($reportTemplateEquity)) {
            return $this->sendError(trans('custom.report_template_equity_not_found'));
        }

        $reportTemplateEquity = $this->reportTemplateEquityRepository->update($input, $id);

        return $this->sendResponse($reportTemplateEquity->toArray(), trans('custom.reporttemplateequity_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/reportTemplateEquities/{id}",
     *      summary="deleteReportTemplateEquity",
     *      tags={"ReportTemplateEquity"},
     *      description="Delete ReportTemplateEquity",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ReportTemplateEquity",
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
        /** @var ReportTemplateEquity $reportTemplateEquity */
        $reportTemplateEquity = $this->reportTemplateEquityRepository->findWithoutFail($id);
      

        if (empty($reportTemplateEquity)) {
            return $this->sendError(trans('custom.report_template_equity_not_found'));
        }
        $masterId = $reportTemplateEquity->templateMasterID;
        $reportTemplateEquity->delete();

        ReportTemplateLinks::where('templateMasterID',$masterId)->where('templateDetailID',$id)->delete();

        return $this->sendResponse(true, trans('custom.report_template_equity_deleted_successfully'));

    }
}
