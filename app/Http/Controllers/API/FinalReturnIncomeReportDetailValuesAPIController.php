<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFinalReturnIncomeReportDetailValuesAPIRequest;
use App\Http\Requests\API\UpdateFinalReturnIncomeReportDetailValuesAPIRequest;
use App\Models\FinalReturnIncomeReportDetailValues;
use App\Repositories\FinalReturnIncomeReportDetailValuesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FinalReturnIncomeReportDetailValuesController
 * @package App\Http\Controllers\API
 */

class FinalReturnIncomeReportDetailValuesAPIController extends AppBaseController
{
    /** @var  FinalReturnIncomeReportDetailValuesRepository */
    private $finalReturnIncomeReportDetailValuesRepository;

    public function __construct(FinalReturnIncomeReportDetailValuesRepository $finalReturnIncomeReportDetailValuesRepo)
    {
        $this->finalReturnIncomeReportDetailValuesRepository = $finalReturnIncomeReportDetailValuesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/finalReturnIncomeReportDetailValues",
     *      summary="getFinalReturnIncomeReportDetailValuesList",
     *      tags={"FinalReturnIncomeReportDetailValues"},
     *      description="Get all FinalReturnIncomeReportDetailValues",
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
     *                  @OA\Items(ref="#/definitions/FinalReturnIncomeReportDetailValues")
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
        $this->finalReturnIncomeReportDetailValuesRepository->pushCriteria(new RequestCriteria($request));
        $this->finalReturnIncomeReportDetailValuesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $finalReturnIncomeReportDetailValues = $this->finalReturnIncomeReportDetailValuesRepository->all();

        return $this->sendResponse($finalReturnIncomeReportDetailValues->toArray(), trans('custom.final_return_income_report_detail_values_retrieved'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/finalReturnIncomeReportDetailValues",
     *      summary="createFinalReturnIncomeReportDetailValues",
     *      tags={"FinalReturnIncomeReportDetailValues"},
     *      description="Create FinalReturnIncomeReportDetailValues",
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
     *                  ref="#/definitions/FinalReturnIncomeReportDetailValues"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $finalReturnIncomeReportDetailValues = FinalReturnIncomeReportDetailValues::updateOrCreate(
                [
                    'reportId' => $input['report_id'],
                    'report_detail_id' => $input['report_detail_id'],
                    'column_id' => $input['column_id'] 
                ],
                [
                    'amount' => $input['amount'] ? $input['amount'] : null,
                    'timestamp' => now()
                ]
            );

        return $this->sendResponse($finalReturnIncomeReportDetailValues->toArray(), trans('custom.final_return_income_report_detail_values_saved_suc'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/finalReturnIncomeReportDetailValues/{id}",
     *      summary="getFinalReturnIncomeReportDetailValuesItem",
     *      tags={"FinalReturnIncomeReportDetailValues"},
     *      description="Get FinalReturnIncomeReportDetailValues",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeReportDetailValues",
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
     *                  ref="#/definitions/FinalReturnIncomeReportDetailValues"
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
        /** @var FinalReturnIncomeReportDetailValues $finalReturnIncomeReportDetailValues */
        $finalReturnIncomeReportDetailValues = $this->finalReturnIncomeReportDetailValuesRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeReportDetailValues)) {
            return $this->sendError(trans('custom.final_return_income_report_detail_values_not_found'));
        }

        return $this->sendResponse($finalReturnIncomeReportDetailValues->toArray(), trans('custom.final_return_income_report_detail_values_retrieved'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/finalReturnIncomeReportDetailValues/{id}",
     *      summary="updateFinalReturnIncomeReportDetailValues",
     *      tags={"FinalReturnIncomeReportDetailValues"},
     *      description="Update FinalReturnIncomeReportDetailValues",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeReportDetailValues",
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
     *                  ref="#/definitions/FinalReturnIncomeReportDetailValues"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFinalReturnIncomeReportDetailValuesAPIRequest $request)
    {
        $input = $request->all();

        /** @var FinalReturnIncomeReportDetailValues $finalReturnIncomeReportDetailValues */
        $finalReturnIncomeReportDetailValues = $this->finalReturnIncomeReportDetailValuesRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeReportDetailValues)) {
            return $this->sendError(trans('custom.final_return_income_report_detail_values_not_found'));
        }

        $finalReturnIncomeReportDetailValues = $this->finalReturnIncomeReportDetailValuesRepository->update($input, $id);

        return $this->sendResponse($finalReturnIncomeReportDetailValues->toArray(), trans('custom.finalreturnincomereportdetailvalues_updated_succes'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/finalReturnIncomeReportDetailValues/{id}",
     *      summary="deleteFinalReturnIncomeReportDetailValues",
     *      tags={"FinalReturnIncomeReportDetailValues"},
     *      description="Delete FinalReturnIncomeReportDetailValues",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeReportDetailValues",
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
        /** @var FinalReturnIncomeReportDetailValues $finalReturnIncomeReportDetailValues */
        $finalReturnIncomeReportDetailValues = $this->finalReturnIncomeReportDetailValuesRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeReportDetailValues)) {
            return $this->sendError(trans('custom.final_return_income_report_detail_values_not_found'));
        }

        $finalReturnIncomeReportDetailValues->delete();

        return $this->sendSuccess('Final Return Income Report Detail Values deleted successfully');
    }
}
