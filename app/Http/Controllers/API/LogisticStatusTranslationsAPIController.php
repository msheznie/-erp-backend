<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLogisticStatusTranslationsAPIRequest;
use App\Http\Requests\API\UpdateLogisticStatusTranslationsAPIRequest;
use App\Models\LogisticStatusTranslations;
use App\Repositories\LogisticStatusTranslationsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LogisticStatusTranslationsController
 * @package App\Http\Controllers\API
 */

class LogisticStatusTranslationsAPIController extends AppBaseController
{
    /** @var  LogisticStatusTranslationsRepository */
    private $logisticStatusTranslationsRepository;

    public function __construct(LogisticStatusTranslationsRepository $logisticStatusTranslationsRepo)
    {
        $this->logisticStatusTranslationsRepository = $logisticStatusTranslationsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/logisticStatusTranslations",
     *      summary="getLogisticStatusTranslationsList",
     *      tags={"LogisticStatusTranslations"},
     *      description="Get all LogisticStatusTranslations",
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
     *                  @OA\Items(ref="#/definitions/LogisticStatusTranslations")
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
        $this->logisticStatusTranslationsRepository->pushCriteria(new RequestCriteria($request));
        $this->logisticStatusTranslationsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $logisticStatusTranslations = $this->logisticStatusTranslationsRepository->all();

        return $this->sendResponse($logisticStatusTranslations->toArray(), 'Logistic Status Translations retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/logisticStatusTranslations",
     *      summary="createLogisticStatusTranslations",
     *      tags={"LogisticStatusTranslations"},
     *      description="Create LogisticStatusTranslations",
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
     *                  ref="#/definitions/LogisticStatusTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLogisticStatusTranslationsAPIRequest $request)
    {
        $input = $request->all();

        $logisticStatusTranslations = $this->logisticStatusTranslationsRepository->create($input);

        return $this->sendResponse($logisticStatusTranslations->toArray(), 'Logistic Status Translations saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/logisticStatusTranslations/{id}",
     *      summary="getLogisticStatusTranslationsItem",
     *      tags={"LogisticStatusTranslations"},
     *      description="Get LogisticStatusTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of LogisticStatusTranslations",
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
     *                  ref="#/definitions/LogisticStatusTranslations"
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
        /** @var LogisticStatusTranslations $logisticStatusTranslations */
        $logisticStatusTranslations = $this->logisticStatusTranslationsRepository->findWithoutFail($id);

        if (empty($logisticStatusTranslations)) {
            return $this->sendError('Logistic Status Translations not found');
        }

        return $this->sendResponse($logisticStatusTranslations->toArray(), 'Logistic Status Translations retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/logisticStatusTranslations/{id}",
     *      summary="updateLogisticStatusTranslations",
     *      tags={"LogisticStatusTranslations"},
     *      description="Update LogisticStatusTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of LogisticStatusTranslations",
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
     *                  ref="#/definitions/LogisticStatusTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLogisticStatusTranslationsAPIRequest $request)
    {
        $input = $request->all();

        /** @var LogisticStatusTranslations $logisticStatusTranslations */
        $logisticStatusTranslations = $this->logisticStatusTranslationsRepository->findWithoutFail($id);

        if (empty($logisticStatusTranslations)) {
            return $this->sendError('Logistic Status Translations not found');
        }

        $logisticStatusTranslations = $this->logisticStatusTranslationsRepository->update($input, $id);

        return $this->sendResponse($logisticStatusTranslations->toArray(), 'LogisticStatusTranslations updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/logisticStatusTranslations/{id}",
     *      summary="deleteLogisticStatusTranslations",
     *      tags={"LogisticStatusTranslations"},
     *      description="Delete LogisticStatusTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of LogisticStatusTranslations",
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
        /** @var LogisticStatusTranslations $logisticStatusTranslations */
        $logisticStatusTranslations = $this->logisticStatusTranslationsRepository->findWithoutFail($id);

        if (empty($logisticStatusTranslations)) {
            return $this->sendError('Logistic Status Translations not found');
        }

        $logisticStatusTranslations->delete();

        return $this->sendSuccess('Logistic Status Translations deleted successfully');
    }
}
