<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReportCustomColumnTranslationsAPIRequest;
use App\Http\Requests\API\UpdateReportCustomColumnTranslationsAPIRequest;
use App\Models\ReportCustomColumnTranslations;
use App\Repositories\ReportCustomColumnTranslationsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ReportCustomColumnTranslationsController
 * @package App\Http\Controllers\API
 */

class ReportCustomColumnTranslationsAPIController extends AppBaseController
{
    /** @var  ReportCustomColumnTranslationsRepository */
    private $reportCustomColumnTranslationsRepository;

    public function __construct(ReportCustomColumnTranslationsRepository $reportCustomColumnTranslationsRepo)
    {
        $this->reportCustomColumnTranslationsRepository = $reportCustomColumnTranslationsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/reportCustomColumnTranslations",
     *      summary="getReportCustomColumnTranslationsList",
     *      tags={"ReportCustomColumnTranslations"},
     *      description="Get all ReportCustomColumnTranslations",
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
     *                  @OA\Items(ref="#/definitions/ReportCustomColumnTranslations")
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
        $this->reportCustomColumnTranslationsRepository->pushCriteria(new RequestCriteria($request));
        $this->reportCustomColumnTranslationsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reportCustomColumnTranslations = $this->reportCustomColumnTranslationsRepository->all();

        return $this->sendResponse($reportCustomColumnTranslations->toArray(), 'Report Custom Column Translations retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/reportCustomColumnTranslations",
     *      summary="createReportCustomColumnTranslations",
     *      tags={"ReportCustomColumnTranslations"},
     *      description="Create ReportCustomColumnTranslations",
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
     *                  ref="#/definitions/ReportCustomColumnTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportCustomColumnTranslationsAPIRequest $request)
    {
        $input = $request->all();

        $reportCustomColumnTranslations = $this->reportCustomColumnTranslationsRepository->create($input);

        return $this->sendResponse($reportCustomColumnTranslations->toArray(), 'Report Custom Column Translations saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/reportCustomColumnTranslations/{id}",
     *      summary="getReportCustomColumnTranslationsItem",
     *      tags={"ReportCustomColumnTranslations"},
     *      description="Get ReportCustomColumnTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ReportCustomColumnTranslations",
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
     *                  ref="#/definitions/ReportCustomColumnTranslations"
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
        /** @var ReportCustomColumnTranslations $reportCustomColumnTranslations */
        $reportCustomColumnTranslations = $this->reportCustomColumnTranslationsRepository->findWithoutFail($id);

        if (empty($reportCustomColumnTranslations)) {
            return $this->sendError('Report Custom Column Translations not found');
        }

        return $this->sendResponse($reportCustomColumnTranslations->toArray(), 'Report Custom Column Translations retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/reportCustomColumnTranslations/{id}",
     *      summary="updateReportCustomColumnTranslations",
     *      tags={"ReportCustomColumnTranslations"},
     *      description="Update ReportCustomColumnTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ReportCustomColumnTranslations",
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
     *                  ref="#/definitions/ReportCustomColumnTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportCustomColumnTranslationsAPIRequest $request)
    {
        $input = $request->all();

        /** @var ReportCustomColumnTranslations $reportCustomColumnTranslations */
        $reportCustomColumnTranslations = $this->reportCustomColumnTranslationsRepository->findWithoutFail($id);

        if (empty($reportCustomColumnTranslations)) {
            return $this->sendError('Report Custom Column Translations not found');
        }

        $reportCustomColumnTranslations = $this->reportCustomColumnTranslationsRepository->update($input, $id);

        return $this->sendResponse($reportCustomColumnTranslations->toArray(), 'ReportCustomColumnTranslations updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/reportCustomColumnTranslations/{id}",
     *      summary="deleteReportCustomColumnTranslations",
     *      tags={"ReportCustomColumnTranslations"},
     *      description="Delete ReportCustomColumnTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ReportCustomColumnTranslations",
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
        /** @var ReportCustomColumnTranslations $reportCustomColumnTranslations */
        $reportCustomColumnTranslations = $this->reportCustomColumnTranslationsRepository->findWithoutFail($id);

        if (empty($reportCustomColumnTranslations)) {
            return $this->sendError('Report Custom Column Translations not found');
        }

        $reportCustomColumnTranslations->delete();

        return $this->sendSuccess('Report Custom Column Translations deleted successfully');
    }
}
