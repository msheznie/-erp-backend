<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCurrencyConversionAPIRequest;
use App\Http\Requests\API\UpdateCurrencyConversionAPIRequest;
use App\Models\CurrencyConversion;
use App\Repositories\CurrencyConversionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CurrencyConversionController
 * @package App\Http\Controllers\API
 */

class CurrencyConversionAPIController extends AppBaseController
{
    /** @var  CurrencyConversionRepository */
    private $currencyConversionRepository;

    public function __construct(CurrencyConversionRepository $currencyConversionRepo)
    {
        $this->currencyConversionRepository = $currencyConversionRepo;
    }

    /**
     * Display a listing of the CurrencyConversion.
     * GET|HEAD /currencyConversions
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->currencyConversionRepository->pushCriteria(new RequestCriteria($request));
        $this->currencyConversionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $currencyConversions = $this->currencyConversionRepository->all();

        return $this->sendResponse($currencyConversions->toArray(), 'Currency Conversions retrieved successfully');
    }

    /**
     * Store a newly created CurrencyConversion in storage.
     * POST /currencyConversions
     *
     * @param CreateCurrencyConversionAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCurrencyConversionAPIRequest $request)
    {
        $input = $request->all();

        $currencyConversions = $this->currencyConversionRepository->create($input);

        return $this->sendResponse($currencyConversions->toArray(), 'Currency Conversion saved successfully');
    }

    /**
     * Display the specified CurrencyConversion.
     * GET|HEAD /currencyConversions/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CurrencyConversion $currencyConversion */
        $currencyConversion = $this->currencyConversionRepository->findWithoutFail($id);

        if (empty($currencyConversion)) {
            return $this->sendError('Currency Conversion not found');
        }

        return $this->sendResponse($currencyConversion->toArray(), 'Currency Conversion retrieved successfully');
    }

    /**
     * Update the specified CurrencyConversion in storage.
     * PUT/PATCH /currencyConversions/{id}
     *
     * @param  int $id
     * @param UpdateCurrencyConversionAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCurrencyConversionAPIRequest $request)
    {
        $input = $request->all();

        /** @var CurrencyConversion $currencyConversion */
        $currencyConversion = $this->currencyConversionRepository->findWithoutFail($id);

        if (empty($currencyConversion)) {
            return $this->sendError('Currency Conversion not found');
        }

        $currencyConversion = $this->currencyConversionRepository->update($input, $id);

        return $this->sendResponse($currencyConversion->toArray(), 'CurrencyConversion updated successfully');
    }

    /**
     * Remove the specified CurrencyConversion from storage.
     * DELETE /currencyConversions/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CurrencyConversion $currencyConversion */
        $currencyConversion = $this->currencyConversionRepository->findWithoutFail($id);

        if (empty($currencyConversion)) {
            return $this->sendError('Currency Conversion not found');
        }

        $currencyConversion->delete();

        return $this->sendResponse($id, 'Currency Conversion deleted successfully');
    }
}
