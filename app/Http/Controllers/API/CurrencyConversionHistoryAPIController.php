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

        return $this->sendResponse($currencyConversionHistories->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.currency_conversion_histories')]));
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

        return $this->sendResponse($currencyConversionHistory->toArray(), trans('custom.save', ['attribute' => trans('custom.currency_conversion_histories')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.currency_conversion_histories')]));
        }

        return $this->sendResponse($currencyConversionHistory->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.currency_conversion_histories')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.currency_conversion_histories')]));
        }

        $currencyConversionHistory = $this->currencyConversionHistoryRepository->update($input, $id);

        return $this->sendResponse($currencyConversionHistory->toArray(), trans('custom.update', ['attribute' => trans('custom.currency_conversion_histories')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.currency_conversion_histories')]));
        }

        $currencyConversionHistory->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.currency_conversion_histories')]));
    }

    public function getCurrencyConversionHistory(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];

        $conversions = CurrencyConversionHistory::where('masterCurrencyID', $input['itemLine']['masterCurrencyID'])
                                                ->where('subCurrencyID', $input['itemLine']['subCurrencyID']);


        $search = $request->input('search.value');
        if ($search) {
            $conversions = $conversions->where(function ($query) use ($search) {
                $query->where('createdBy', 'LIKE', "%{$search}%")
                    ->orWhere('createdpc', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($conversions)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('conversionhistoryID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }
}
