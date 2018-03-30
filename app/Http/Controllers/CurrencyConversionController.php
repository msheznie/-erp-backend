<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCurrencyConversionRequest;
use App\Http\Requests\UpdateCurrencyConversionRequest;
use App\Repositories\CurrencyConversionRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CurrencyConversionController extends AppBaseController
{
    /** @var  CurrencyConversionRepository */
    private $currencyConversionRepository;

    public function __construct(CurrencyConversionRepository $currencyConversionRepo)
    {
        $this->currencyConversionRepository = $currencyConversionRepo;
    }

    /**
     * Display a listing of the CurrencyConversion.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->currencyConversionRepository->pushCriteria(new RequestCriteria($request));
        $currencyConversions = $this->currencyConversionRepository->all();

        return view('currency_conversions.index')
            ->with('currencyConversions', $currencyConversions);
    }

    /**
     * Show the form for creating a new CurrencyConversion.
     *
     * @return Response
     */
    public function create()
    {
        return view('currency_conversions.create');
    }

    /**
     * Store a newly created CurrencyConversion in storage.
     *
     * @param CreateCurrencyConversionRequest $request
     *
     * @return Response
     */
    public function store(CreateCurrencyConversionRequest $request)
    {
        $input = $request->all();

        $currencyConversion = $this->currencyConversionRepository->create($input);

        Flash::success('Currency Conversion saved successfully.');

        return redirect(route('currencyConversions.index'));
    }

    /**
     * Display the specified CurrencyConversion.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $currencyConversion = $this->currencyConversionRepository->findWithoutFail($id);

        if (empty($currencyConversion)) {
            Flash::error('Currency Conversion not found');

            return redirect(route('currencyConversions.index'));
        }

        return view('currency_conversions.show')->with('currencyConversion', $currencyConversion);
    }

    /**
     * Show the form for editing the specified CurrencyConversion.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $currencyConversion = $this->currencyConversionRepository->findWithoutFail($id);

        if (empty($currencyConversion)) {
            Flash::error('Currency Conversion not found');

            return redirect(route('currencyConversions.index'));
        }

        return view('currency_conversions.edit')->with('currencyConversion', $currencyConversion);
    }

    /**
     * Update the specified CurrencyConversion in storage.
     *
     * @param  int              $id
     * @param UpdateCurrencyConversionRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCurrencyConversionRequest $request)
    {
        $currencyConversion = $this->currencyConversionRepository->findWithoutFail($id);

        if (empty($currencyConversion)) {
            Flash::error('Currency Conversion not found');

            return redirect(route('currencyConversions.index'));
        }

        $currencyConversion = $this->currencyConversionRepository->update($request->all(), $id);

        Flash::success('Currency Conversion updated successfully.');

        return redirect(route('currencyConversions.index'));
    }

    /**
     * Remove the specified CurrencyConversion from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $currencyConversion = $this->currencyConversionRepository->findWithoutFail($id);

        if (empty($currencyConversion)) {
            Flash::error('Currency Conversion not found');

            return redirect(route('currencyConversions.index'));
        }

        $this->currencyConversionRepository->delete($id);

        Flash::success('Currency Conversion deleted successfully.');

        return redirect(route('currencyConversions.index'));
    }
}
