<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFinalReturnIncomeTemplateColumnsAPIRequest;
use App\Http\Requests\API\UpdateFinalReturnIncomeTemplateColumnsAPIRequest;
use App\Models\FinalReturnIncomeTemplateColumns;
use App\Repositories\FinalReturnIncomeTemplateColumnsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FinalReturnIncomeTemplateColumnsController
 * @package App\Http\Controllers\API
 */

class FinalReturnIncomeTemplateColumnsAPIController extends AppBaseController
{
    /** @var  FinalReturnIncomeTemplateColumnsRepository */
    private $finalReturnIncomeTemplateColumnsRepository;

    public function __construct(FinalReturnIncomeTemplateColumnsRepository $finalReturnIncomeTemplateColumnsRepo)
    {
        $this->finalReturnIncomeTemplateColumnsRepository = $finalReturnIncomeTemplateColumnsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/finalReturnIncomeTemplateColumns",
     *      summary="getFinalReturnIncomeTemplateColumnsList",
     *      tags={"FinalReturnIncomeTemplateColumns"},
     *      description="Get all FinalReturnIncomeTemplateColumns",
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
     *                  @OA\Items(ref="#/definitions/FinalReturnIncomeTemplateColumns")
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
        $this->finalReturnIncomeTemplateColumnsRepository->pushCriteria(new RequestCriteria($request));
        $this->finalReturnIncomeTemplateColumnsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $finalReturnIncomeTemplateColumns = $this->finalReturnIncomeTemplateColumnsRepository->all();

        return $this->sendResponse($finalReturnIncomeTemplateColumns->toArray(), 'Final Return Income Template Columns retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/finalReturnIncomeTemplateColumns",
     *      summary="createFinalReturnIncomeTemplateColumns",
     *      tags={"FinalReturnIncomeTemplateColumns"},
     *      description="Create FinalReturnIncomeTemplateColumns",
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
     *                  ref="#/definitions/FinalReturnIncomeTemplateColumns"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFinalReturnIncomeTemplateColumnsAPIRequest $request)
    {
        $input = $request->all();

        $finalReturnIncomeTemplateColumns = $this->finalReturnIncomeTemplateColumnsRepository->create($input);

        return $this->sendResponse($finalReturnIncomeTemplateColumns->toArray(), 'Final Return Income Template Columns saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/finalReturnIncomeTemplateColumns/{id}",
     *      summary="getFinalReturnIncomeTemplateColumnsItem",
     *      tags={"FinalReturnIncomeTemplateColumns"},
     *      description="Get FinalReturnIncomeTemplateColumns",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeTemplateColumns",
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
     *                  ref="#/definitions/FinalReturnIncomeTemplateColumns"
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
        /** @var FinalReturnIncomeTemplateColumns $finalReturnIncomeTemplateColumns */
        $finalReturnIncomeTemplateColumns = $this->finalReturnIncomeTemplateColumnsRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeTemplateColumns)) {
            return $this->sendError('Final Return Income Template Columns not found');
        }

        return $this->sendResponse($finalReturnIncomeTemplateColumns->toArray(), 'Final Return Income Template Columns retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/finalReturnIncomeTemplateColumns/{id}",
     *      summary="updateFinalReturnIncomeTemplateColumns",
     *      tags={"FinalReturnIncomeTemplateColumns"},
     *      description="Update FinalReturnIncomeTemplateColumns",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeTemplateColumns",
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
     *                  ref="#/definitions/FinalReturnIncomeTemplateColumns"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFinalReturnIncomeTemplateColumnsAPIRequest $request)
    {
        $input = $request->all();

        /** @var FinalReturnIncomeTemplateColumns $finalReturnIncomeTemplateColumns */
        $finalReturnIncomeTemplateColumns = $this->finalReturnIncomeTemplateColumnsRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeTemplateColumns)) {
            return $this->sendError('Final Return Income Template Columns not found');
        }

        $finalReturnIncomeTemplateColumns = $this->finalReturnIncomeTemplateColumnsRepository->update($input, $id);

        return $this->sendResponse($finalReturnIncomeTemplateColumns->toArray(), 'FinalReturnIncomeTemplateColumns updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/finalReturnIncomeTemplateColumns/{id}",
     *      summary="deleteFinalReturnIncomeTemplateColumns",
     *      tags={"FinalReturnIncomeTemplateColumns"},
     *      description="Delete FinalReturnIncomeTemplateColumns",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeTemplateColumns",
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
        /** @var FinalReturnIncomeTemplateColumns $finalReturnIncomeTemplateColumns */
        $finalReturnIncomeTemplateColumns = $this->finalReturnIncomeTemplateColumnsRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeTemplateColumns)) {
            return $this->sendError('Final Return Income Template Columns not found');
        }

        $finalReturnIncomeTemplateColumns->delete();

        return $this->sendSuccess('Final Return Income Template Columns deleted successfully');
    }
}
