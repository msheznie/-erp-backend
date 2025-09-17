<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReportTemplateColumnsTranslationsAPIRequest;
use App\Http\Requests\API\UpdateReportTemplateColumnsTranslationsAPIRequest;
use App\Models\ReportTemplateColumnsTranslations;
use App\Repositories\ReportTemplateColumnsTranslationsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ReportTemplateColumnsTranslationsController
 * @package App\Http\Controllers\API
 */

class ReportTemplateColumnsTranslationsAPIController extends AppBaseController
{
    /** @var  ReportTemplateColumnsTranslationsRepository */
    private $reportTemplateColumnsTranslationsRepository;

    public function __construct(ReportTemplateColumnsTranslationsRepository $reportTemplateColumnsTranslationsRepo)
    {
        $this->reportTemplateColumnsTranslationsRepository = $reportTemplateColumnsTranslationsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/reportTemplateColumnsTranslations",
     *      summary="getReportTemplateColumnsTranslationsList",
     *      tags={"ReportTemplateColumnsTranslations"},
     *      description="Get all ReportTemplateColumnsTranslations",
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
     *                  @OA\Items(ref="#/definitions/ReportTemplateColumnsTranslations")
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
        $this->reportTemplateColumnsTranslationsRepository->pushCriteria(new RequestCriteria($request));
        $this->reportTemplateColumnsTranslationsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reportTemplateColumnsTranslations = $this->reportTemplateColumnsTranslationsRepository->all();

        return $this->sendResponse($reportTemplateColumnsTranslations->toArray(), 'Report Template Columns Translations retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/reportTemplateColumnsTranslations",
     *      summary="createReportTemplateColumnsTranslations",
     *      tags={"ReportTemplateColumnsTranslations"},
     *      description="Create ReportTemplateColumnsTranslations",
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
     *                  ref="#/definitions/ReportTemplateColumnsTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportTemplateColumnsTranslationsAPIRequest $request)
    {
        $input = $request->all();

        $reportTemplateColumnsTranslations = $this->reportTemplateColumnsTranslationsRepository->create($input);

        return $this->sendResponse($reportTemplateColumnsTranslations->toArray(), 'Report Template Columns Translations saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/reportTemplateColumnsTranslations/{id}",
     *      summary="getReportTemplateColumnsTranslationsItem",
     *      tags={"ReportTemplateColumnsTranslations"},
     *      description="Get ReportTemplateColumnsTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ReportTemplateColumnsTranslations",
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
     *                  ref="#/definitions/ReportTemplateColumnsTranslations"
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
        /** @var ReportTemplateColumnsTranslations $reportTemplateColumnsTranslations */
        $reportTemplateColumnsTranslations = $this->reportTemplateColumnsTranslationsRepository->findWithoutFail($id);

        if (empty($reportTemplateColumnsTranslations)) {
            return $this->sendError('Report Template Columns Translations not found');
        }

        return $this->sendResponse($reportTemplateColumnsTranslations->toArray(), 'Report Template Columns Translations retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/reportTemplateColumnsTranslations/{id}",
     *      summary="updateReportTemplateColumnsTranslations",
     *      tags={"ReportTemplateColumnsTranslations"},
     *      description="Update ReportTemplateColumnsTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ReportTemplateColumnsTranslations",
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
     *                  ref="#/definitions/ReportTemplateColumnsTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportTemplateColumnsTranslationsAPIRequest $request)
    {
        $input = $request->all();

        /** @var ReportTemplateColumnsTranslations $reportTemplateColumnsTranslations */
        $reportTemplateColumnsTranslations = $this->reportTemplateColumnsTranslationsRepository->findWithoutFail($id);

        if (empty($reportTemplateColumnsTranslations)) {
            return $this->sendError('Report Template Columns Translations not found');
        }

        $reportTemplateColumnsTranslations = $this->reportTemplateColumnsTranslationsRepository->update($input, $id);

        return $this->sendResponse($reportTemplateColumnsTranslations->toArray(), 'ReportTemplateColumnsTranslations updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/reportTemplateColumnsTranslations/{id}",
     *      summary="deleteReportTemplateColumnsTranslations",
     *      tags={"ReportTemplateColumnsTranslations"},
     *      description="Delete ReportTemplateColumnsTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ReportTemplateColumnsTranslations",
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
        /** @var ReportTemplateColumnsTranslations $reportTemplateColumnsTranslations */
        $reportTemplateColumnsTranslations = $this->reportTemplateColumnsTranslationsRepository->findWithoutFail($id);

        if (empty($reportTemplateColumnsTranslations)) {
            return $this->sendError('Report Template Columns Translations not found');
        }

        $reportTemplateColumnsTranslations->delete();

        return $this->sendSuccess('Report Template Columns Translations deleted successfully');
    }
}
