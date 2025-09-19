<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLogisticModeOfImportTranslationsAPIRequest;
use App\Http\Requests\API\UpdateLogisticModeOfImportTranslationsAPIRequest;
use App\Models\LogisticModeOfImportTranslations;
use App\Repositories\LogisticModeOfImportTranslationsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LogisticModeOfImportTranslationsController
 * @package App\Http\Controllers\API
 */

class LogisticModeOfImportTranslationsAPIController extends AppBaseController
{
    /** @var  LogisticModeOfImportTranslationsRepository */
    private $logisticModeOfImportTranslationsRepository;

    public function __construct(LogisticModeOfImportTranslationsRepository $logisticModeOfImportTranslationsRepo)
    {
        $this->logisticModeOfImportTranslationsRepository = $logisticModeOfImportTranslationsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/logisticModeOfImportTranslations",
     *      summary="getLogisticModeOfImportTranslationsList",
     *      tags={"LogisticModeOfImportTranslations"},
     *      description="Get all LogisticModeOfImportTranslations",
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
     *                  @OA\Items(ref="#/definitions/LogisticModeOfImportTranslations")
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
        $this->logisticModeOfImportTranslationsRepository->pushCriteria(new RequestCriteria($request));
        $this->logisticModeOfImportTranslationsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $logisticModeOfImportTranslations = $this->logisticModeOfImportTranslationsRepository->all();

        return $this->sendResponse($logisticModeOfImportTranslations->toArray(), 'Logistic Mode Of Import Translations retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/logisticModeOfImportTranslations",
     *      summary="createLogisticModeOfImportTranslations",
     *      tags={"LogisticModeOfImportTranslations"},
     *      description="Create LogisticModeOfImportTranslations",
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
     *                  ref="#/definitions/LogisticModeOfImportTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLogisticModeOfImportTranslationsAPIRequest $request)
    {
        $input = $request->all();

        $logisticModeOfImportTranslations = $this->logisticModeOfImportTranslationsRepository->create($input);

        return $this->sendResponse($logisticModeOfImportTranslations->toArray(), 'Logistic Mode Of Import Translations saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/logisticModeOfImportTranslations/{id}",
     *      summary="getLogisticModeOfImportTranslationsItem",
     *      tags={"LogisticModeOfImportTranslations"},
     *      description="Get LogisticModeOfImportTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of LogisticModeOfImportTranslations",
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
     *                  ref="#/definitions/LogisticModeOfImportTranslations"
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
        /** @var LogisticModeOfImportTranslations $logisticModeOfImportTranslations */
        $logisticModeOfImportTranslations = $this->logisticModeOfImportTranslationsRepository->findWithoutFail($id);

        if (empty($logisticModeOfImportTranslations)) {
            return $this->sendError('Logistic Mode Of Import Translations not found');
        }

        return $this->sendResponse($logisticModeOfImportTranslations->toArray(), 'Logistic Mode Of Import Translations retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/logisticModeOfImportTranslations/{id}",
     *      summary="updateLogisticModeOfImportTranslations",
     *      tags={"LogisticModeOfImportTranslations"},
     *      description="Update LogisticModeOfImportTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of LogisticModeOfImportTranslations",
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
     *                  ref="#/definitions/LogisticModeOfImportTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLogisticModeOfImportTranslationsAPIRequest $request)
    {
        $input = $request->all();

        /** @var LogisticModeOfImportTranslations $logisticModeOfImportTranslations */
        $logisticModeOfImportTranslations = $this->logisticModeOfImportTranslationsRepository->findWithoutFail($id);

        if (empty($logisticModeOfImportTranslations)) {
            return $this->sendError('Logistic Mode Of Import Translations not found');
        }

        $logisticModeOfImportTranslations = $this->logisticModeOfImportTranslationsRepository->update($input, $id);

        return $this->sendResponse($logisticModeOfImportTranslations->toArray(), 'LogisticModeOfImportTranslations updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/logisticModeOfImportTranslations/{id}",
     *      summary="deleteLogisticModeOfImportTranslations",
     *      tags={"LogisticModeOfImportTranslations"},
     *      description="Delete LogisticModeOfImportTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of LogisticModeOfImportTranslations",
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
        /** @var LogisticModeOfImportTranslations $logisticModeOfImportTranslations */
        $logisticModeOfImportTranslations = $this->logisticModeOfImportTranslationsRepository->findWithoutFail($id);

        if (empty($logisticModeOfImportTranslations)) {
            return $this->sendError('Logistic Mode Of Import Translations not found');
        }

        $logisticModeOfImportTranslations->delete();

        return $this->sendSuccess('Logistic Mode Of Import Translations deleted successfully');
    }
}
