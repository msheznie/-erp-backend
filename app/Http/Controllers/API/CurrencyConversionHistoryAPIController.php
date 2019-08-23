<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCurrencyConversionHistoryAPIRequest;
use App\Http\Requests\API\UpdateCurrencyConversionHistoryAPIRequest;
use App\Models\CurrencyConversionHistory;
use App\Repositories\CurrencyConversionHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CurrencyConversionHistoryController
 * @package App\Http\Controllers\API
 */

class CurrencyConversionHistoryAPIController extends AppBaseController
{
    /** @var  CurrencyConversionHistoryRepository */
    private $currencyConversionHistoryRepository;

    public function __construct(CurrencyConversionHistoryRepository $currencyConversionHistoryRepo)
    {
        $this->currencyConversionHistoryRepository = $currencyConversionHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/currencyConversionHistories",
     *      summary="Get a listing of the CurrencyConversionHistories.",
     *      tags={"CurrencyConversionHistory"},
     *      description="Get all CurrencyConversionHistories",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/CurrencyConversionHistory")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->currencyConversionHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->currencyConversionHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $currencyConversionHistories = $this->currencyConversionHistoryRepository->all();

        return $this->sendResponse($currencyConversionHistories->toArray(), 'Currency Conversion Histories retrieved successfully');
    }

    /**
     * @param CreateCurrencyConversionHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/currencyConversionHistories",
     *      summary="Store a newly created CurrencyConversionHistory in storage",
     *      tags={"CurrencyConversionHistory"},
     *      description="Store CurrencyConversionHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CurrencyConversionHistory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CurrencyConversionHistory")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/CurrencyConversionHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCurrencyConversionHistoryAPIRequest $request)
    {
        $input = $request->all();

        $currencyConversionHistory = $this->currencyConversionHistoryRepository->create($input);

        return $this->sendResponse($currencyConversionHistory->toArray(), 'Currency Conversion History saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/currencyConversionHistories/{id}",
     *      summary="Display the specified CurrencyConversionHistory",
     *      tags={"CurrencyConversionHistory"},
     *      description="Get CurrencyConversionHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CurrencyConversionHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/CurrencyConversionHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var CurrencyConversionHistory $currencyConversionHistory */
        $currencyConversionHistory = $this->currencyConversionHistoryRepository->findWithoutFail($id);

        if (empty($currencyConversionHistory)) {
            return $this->sendError('Currency Conversion History not found');
        }

        return $this->sendResponse($currencyConversionHistory->toArray(), 'Currency Conversion History retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCurrencyConversionHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/currencyConversionHistories/{id}",
     *      summary="Update the specified CurrencyConversionHistory in storage",
     *      tags={"CurrencyConversionHistory"},
     *      description="Update CurrencyConversionHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CurrencyConversionHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CurrencyConversionHistory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CurrencyConversionHistory")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/CurrencyConversionHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCurrencyConversionHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var CurrencyConversionHistory $currencyConversionHistory */
        $currencyConversionHistory = $this->currencyConversionHistoryRepository->findWithoutFail($id);

        if (empty($currencyConversionHistory)) {
            return $this->sendError('Currency Conversion History not found');
        }

        $currencyConversionHistory = $this->currencyConversionHistoryRepository->update($input, $id);

        return $this->sendResponse($currencyConversionHistory->toArray(), 'CurrencyConversionHistory updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/currencyConversionHistories/{id}",
     *      summary="Remove the specified CurrencyConversionHistory from storage",
     *      tags={"CurrencyConversionHistory"},
     *      description="Delete CurrencyConversionHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CurrencyConversionHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var CurrencyConversionHistory $currencyConversionHistory */
        $currencyConversionHistory = $this->currencyConversionHistoryRepository->findWithoutFail($id);

        if (empty($currencyConversionHistory)) {
            return $this->sendError('Currency Conversion History not found');
        }

        $currencyConversionHistory->delete();

        return $this->sendResponse($id, 'Currency Conversion History deleted successfully');
    }
}
