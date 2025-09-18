<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateExchangeSetupDocumentTypeTranslationsAPIRequest;
use App\Http\Requests\API\UpdateExchangeSetupDocumentTypeTranslationsAPIRequest;
use App\Models\ExchangeSetupDocumentTypeTranslations;
use App\Repositories\ExchangeSetupDocumentTypeTranslationsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ExchangeSetupDocumentTypeTranslationsController
 * @package App\Http\Controllers\API
 */

class ExchangeSetupDocumentTypeTranslationsAPIController extends AppBaseController
{
    /** @var  ExchangeSetupDocumentTypeTranslationsRepository */
    private $exchangeSetupDocumentTypeTranslationsRepository;

    public function __construct(ExchangeSetupDocumentTypeTranslationsRepository $exchangeSetupDocumentTypeTranslationsRepo)
    {
        $this->exchangeSetupDocumentTypeTranslationsRepository = $exchangeSetupDocumentTypeTranslationsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/exchangeSetupDocumentTypeTranslations",
     *      summary="getExchangeSetupDocumentTypeTranslationsList",
     *      tags={"ExchangeSetupDocumentTypeTranslations"},
     *      description="Get all ExchangeSetupDocumentTypeTranslations",
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
     *                  @OA\Items(ref="#/definitions/ExchangeSetupDocumentTypeTranslations")
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
        $this->exchangeSetupDocumentTypeTranslationsRepository->pushCriteria(new RequestCriteria($request));
        $this->exchangeSetupDocumentTypeTranslationsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $exchangeSetupDocumentTypeTranslations = $this->exchangeSetupDocumentTypeTranslationsRepository->all();

        return $this->sendResponse($exchangeSetupDocumentTypeTranslations->toArray(), 'Exchange Setup Document Type Translations retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/exchangeSetupDocumentTypeTranslations",
     *      summary="createExchangeSetupDocumentTypeTranslations",
     *      tags={"ExchangeSetupDocumentTypeTranslations"},
     *      description="Create ExchangeSetupDocumentTypeTranslations",
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
     *                  ref="#/definitions/ExchangeSetupDocumentTypeTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateExchangeSetupDocumentTypeTranslationsAPIRequest $request)
    {
        $input = $request->all();

        $exchangeSetupDocumentTypeTranslations = $this->exchangeSetupDocumentTypeTranslationsRepository->create($input);

        return $this->sendResponse($exchangeSetupDocumentTypeTranslations->toArray(), 'Exchange Setup Document Type Translations saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/exchangeSetupDocumentTypeTranslations/{id}",
     *      summary="getExchangeSetupDocumentTypeTranslationsItem",
     *      tags={"ExchangeSetupDocumentTypeTranslations"},
     *      description="Get ExchangeSetupDocumentTypeTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ExchangeSetupDocumentTypeTranslations",
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
     *                  ref="#/definitions/ExchangeSetupDocumentTypeTranslations"
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
        /** @var ExchangeSetupDocumentTypeTranslations $exchangeSetupDocumentTypeTranslations */
        $exchangeSetupDocumentTypeTranslations = $this->exchangeSetupDocumentTypeTranslationsRepository->findWithoutFail($id);

        if (empty($exchangeSetupDocumentTypeTranslations)) {
            return $this->sendError('Exchange Setup Document Type Translations not found');
        }

        return $this->sendResponse($exchangeSetupDocumentTypeTranslations->toArray(), 'Exchange Setup Document Type Translations retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/exchangeSetupDocumentTypeTranslations/{id}",
     *      summary="updateExchangeSetupDocumentTypeTranslations",
     *      tags={"ExchangeSetupDocumentTypeTranslations"},
     *      description="Update ExchangeSetupDocumentTypeTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ExchangeSetupDocumentTypeTranslations",
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
     *                  ref="#/definitions/ExchangeSetupDocumentTypeTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateExchangeSetupDocumentTypeTranslationsAPIRequest $request)
    {
        $input = $request->all();

        /** @var ExchangeSetupDocumentTypeTranslations $exchangeSetupDocumentTypeTranslations */
        $exchangeSetupDocumentTypeTranslations = $this->exchangeSetupDocumentTypeTranslationsRepository->findWithoutFail($id);

        if (empty($exchangeSetupDocumentTypeTranslations)) {
            return $this->sendError('Exchange Setup Document Type Translations not found');
        }

        $exchangeSetupDocumentTypeTranslations = $this->exchangeSetupDocumentTypeTranslationsRepository->update($input, $id);

        return $this->sendResponse($exchangeSetupDocumentTypeTranslations->toArray(), 'ExchangeSetupDocumentTypeTranslations updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/exchangeSetupDocumentTypeTranslations/{id}",
     *      summary="deleteExchangeSetupDocumentTypeTranslations",
     *      tags={"ExchangeSetupDocumentTypeTranslations"},
     *      description="Delete ExchangeSetupDocumentTypeTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ExchangeSetupDocumentTypeTranslations",
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
        /** @var ExchangeSetupDocumentTypeTranslations $exchangeSetupDocumentTypeTranslations */
        $exchangeSetupDocumentTypeTranslations = $this->exchangeSetupDocumentTypeTranslationsRepository->findWithoutFail($id);

        if (empty($exchangeSetupDocumentTypeTranslations)) {
            return $this->sendError('Exchange Setup Document Type Translations not found');
        }

        $exchangeSetupDocumentTypeTranslations->delete();

        return $this->sendSuccess('Exchange Setup Document Type Translations deleted successfully');
    }
}
