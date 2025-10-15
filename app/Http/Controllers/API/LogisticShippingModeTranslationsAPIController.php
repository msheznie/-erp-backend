<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLogisticShippingModeTranslationsAPIRequest;
use App\Http\Requests\API\UpdateLogisticShippingModeTranslationsAPIRequest;
use App\Models\LogisticShippingModeTranslations;
use App\Repositories\LogisticShippingModeTranslationsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LogisticShippingModeTranslationsController
 * @package App\Http\Controllers\API
 */

class LogisticShippingModeTranslationsAPIController extends AppBaseController
{
    /** @var  LogisticShippingModeTranslationsRepository */
    private $logisticShippingModeTranslationsRepository;

    public function __construct(LogisticShippingModeTranslationsRepository $logisticShippingModeTranslationsRepo)
    {
        $this->logisticShippingModeTranslationsRepository = $logisticShippingModeTranslationsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/logisticShippingModeTranslations",
     *      summary="getLogisticShippingModeTranslationsList",
     *      tags={"LogisticShippingModeTranslations"},
     *      description="Get all LogisticShippingModeTranslations",
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
     *                  @OA\Items(ref="#/definitions/LogisticShippingModeTranslations")
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
        $this->logisticShippingModeTranslationsRepository->pushCriteria(new RequestCriteria($request));
        $this->logisticShippingModeTranslationsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $logisticShippingModeTranslations = $this->logisticShippingModeTranslationsRepository->all();

        return $this->sendResponse($logisticShippingModeTranslations->toArray(), 'Logistic Shipping Mode Translations retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/logisticShippingModeTranslations",
     *      summary="createLogisticShippingModeTranslations",
     *      tags={"LogisticShippingModeTranslations"},
     *      description="Create LogisticShippingModeTranslations",
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
     *                  ref="#/definitions/LogisticShippingModeTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLogisticShippingModeTranslationsAPIRequest $request)
    {
        $input = $request->all();

        $logisticShippingModeTranslations = $this->logisticShippingModeTranslationsRepository->create($input);

        return $this->sendResponse($logisticShippingModeTranslations->toArray(), 'Logistic Shipping Mode Translations saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/logisticShippingModeTranslations/{id}",
     *      summary="getLogisticShippingModeTranslationsItem",
     *      tags={"LogisticShippingModeTranslations"},
     *      description="Get LogisticShippingModeTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of LogisticShippingModeTranslations",
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
     *                  ref="#/definitions/LogisticShippingModeTranslations"
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
        /** @var LogisticShippingModeTranslations $logisticShippingModeTranslations */
        $logisticShippingModeTranslations = $this->logisticShippingModeTranslationsRepository->findWithoutFail($id);

        if (empty($logisticShippingModeTranslations)) {
            return $this->sendError('Logistic Shipping Mode Translations not found');
        }

        return $this->sendResponse($logisticShippingModeTranslations->toArray(), 'Logistic Shipping Mode Translations retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/logisticShippingModeTranslations/{id}",
     *      summary="updateLogisticShippingModeTranslations",
     *      tags={"LogisticShippingModeTranslations"},
     *      description="Update LogisticShippingModeTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of LogisticShippingModeTranslations",
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
     *                  ref="#/definitions/LogisticShippingModeTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLogisticShippingModeTranslationsAPIRequest $request)
    {
        $input = $request->all();

        /** @var LogisticShippingModeTranslations $logisticShippingModeTranslations */
        $logisticShippingModeTranslations = $this->logisticShippingModeTranslationsRepository->findWithoutFail($id);

        if (empty($logisticShippingModeTranslations)) {
            return $this->sendError('Logistic Shipping Mode Translations not found');
        }

        $logisticShippingModeTranslations = $this->logisticShippingModeTranslationsRepository->update($input, $id);

        return $this->sendResponse($logisticShippingModeTranslations->toArray(), 'LogisticShippingModeTranslations updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/logisticShippingModeTranslations/{id}",
     *      summary="deleteLogisticShippingModeTranslations",
     *      tags={"LogisticShippingModeTranslations"},
     *      description="Delete LogisticShippingModeTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of LogisticShippingModeTranslations",
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
        /** @var LogisticShippingModeTranslations $logisticShippingModeTranslations */
        $logisticShippingModeTranslations = $this->logisticShippingModeTranslationsRepository->findWithoutFail($id);

        if (empty($logisticShippingModeTranslations)) {
            return $this->sendError('Logistic Shipping Mode Translations not found');
        }

        $logisticShippingModeTranslations->delete();

        return $this->sendSuccess('Logistic Shipping Mode Translations deleted successfully');
    }
}
