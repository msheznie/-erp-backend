<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFinalReturnIncomeReportDetailsAPIRequest;
use App\Http\Requests\API\UpdateFinalReturnIncomeReportDetailsAPIRequest;
use App\Models\FinalReturnIncomeReportDetails;
use App\Repositories\FinalReturnIncomeReportDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FinalReturnIncomeReportDetailsController
 * @package App\Http\Controllers\API
 */

class FinalReturnIncomeReportDetailsAPIController extends AppBaseController
{
    /** @var  FinalReturnIncomeReportDetailsRepository */
    private $finalReturnIncomeReportDetailsRepository;

    public function __construct(FinalReturnIncomeReportDetailsRepository $finalReturnIncomeReportDetailsRepo)
    {
        $this->finalReturnIncomeReportDetailsRepository = $finalReturnIncomeReportDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/finalReturnIncomeReportDetails",
     *      summary="getFinalReturnIncomeReportDetailsList",
     *      tags={"FinalReturnIncomeReportDetails"},
     *      description="Get all FinalReturnIncomeReportDetails",
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
     *                  @OA\Items(ref="#/definitions/FinalReturnIncomeReportDetails")
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
        $this->finalReturnIncomeReportDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->finalReturnIncomeReportDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $finalReturnIncomeReportDetails = $this->finalReturnIncomeReportDetailsRepository->all();

        return $this->sendResponse($finalReturnIncomeReportDetails->toArray(), 'Final Return Income Report Details retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/finalReturnIncomeReportDetails",
     *      summary="createFinalReturnIncomeReportDetails",
     *      tags={"FinalReturnIncomeReportDetails"},
     *      description="Create FinalReturnIncomeReportDetails",
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
     *                  ref="#/definitions/FinalReturnIncomeReportDetails"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFinalReturnIncomeReportDetailsAPIRequest $request)
    {
        $input = $request->all();

        $finalReturnIncomeReportDetails = $this->finalReturnIncomeReportDetailsRepository->create($input);

        return $this->sendResponse($finalReturnIncomeReportDetails->toArray(), 'Final Return Income Report Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/finalReturnIncomeReportDetails/{id}",
     *      summary="getFinalReturnIncomeReportDetailsItem",
     *      tags={"FinalReturnIncomeReportDetails"},
     *      description="Get FinalReturnIncomeReportDetails",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeReportDetails",
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
     *                  ref="#/definitions/FinalReturnIncomeReportDetails"
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
        /** @var FinalReturnIncomeReportDetails $finalReturnIncomeReportDetails */
        $finalReturnIncomeReportDetails = $this->finalReturnIncomeReportDetailsRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeReportDetails)) {
            return $this->sendError('Final Return Income Report Details not found');
        }

        return $this->sendResponse($finalReturnIncomeReportDetails->toArray(), 'Final Return Income Report Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/finalReturnIncomeReportDetails/{id}",
     *      summary="updateFinalReturnIncomeReportDetails",
     *      tags={"FinalReturnIncomeReportDetails"},
     *      description="Update FinalReturnIncomeReportDetails",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeReportDetails",
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
     *                  ref="#/definitions/FinalReturnIncomeReportDetails"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFinalReturnIncomeReportDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var FinalReturnIncomeReportDetails $finalReturnIncomeReportDetails */
        $finalReturnIncomeReportDetails = $this->finalReturnIncomeReportDetailsRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeReportDetails)) {
            return $this->sendError('Final Return Income Report Details not found');
        }

        $finalReturnIncomeReportDetails = $this->finalReturnIncomeReportDetailsRepository->update($input, $id);

        return $this->sendResponse($finalReturnIncomeReportDetails->toArray(), 'FinalReturnIncomeReportDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/finalReturnIncomeReportDetails/{id}",
     *      summary="deleteFinalReturnIncomeReportDetails",
     *      tags={"FinalReturnIncomeReportDetails"},
     *      description="Delete FinalReturnIncomeReportDetails",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeReportDetails",
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
        /** @var FinalReturnIncomeReportDetails $finalReturnIncomeReportDetails */
        $finalReturnIncomeReportDetails = $this->finalReturnIncomeReportDetailsRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeReportDetails)) {
            return $this->sendError('Final Return Income Report Details not found');
        }

        $finalReturnIncomeReportDetails->delete();

        return $this->sendSuccess('Final Return Income Report Details deleted successfully');
    }
}
