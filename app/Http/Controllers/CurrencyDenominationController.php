<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCurrencyDenominationRequest;
use App\Http\Requests\UpdateCurrencyDenominationRequest;
use App\Repositories\CurrencyDenominationRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CurrencyDenominationController extends AppBaseController
{
    /** @var  CurrencyDenominationRepository */
    private $currencyDenominationRepository;

    public function __construct(CurrencyDenominationRepository $currencyDenominationRepo)
    {
        $this->currencyDenominationRepository = $currencyDenominationRepo;
    }

    /**
     * Display a listing of the CurrencyDenomination.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->currencyDenominationRepository->pushCriteria(new RequestCriteria($request));
        $currencyDenominations = $this->currencyDenominationRepository->all();

        return view('currency_denominations.index')
            ->with('currencyDenominations', $currencyDenominations);
    }

    /**
     * Show the form for creating a new CurrencyDenomination.
     *
     * @return Response
     */
    public function create()
    {
        return view('currency_denominations.create');
    }

    /**
     * Store a newly created CurrencyDenomination in storage.
     *
     * @param CreateCurrencyDenominationRequest $request
     *
     * @return Response
     */
    public function store(CreateCurrencyDenominationRequest $request)
    {
        $input = $request->all();

        $currencyDenomination = $this->currencyDenominationRepository->create($input);

        Flash::success('Currency Denomination saved successfully.');

        return redirect(route('currencyDenominations.index'));
    }

    /**
     * Display the specified CurrencyDenomination.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $currencyDenomination = $this->currencyDenominationRepository->findWithoutFail($id);

        if (empty($currencyDenomination)) {
            Flash::error('Currency Denomination not found');

            return redirect(route('currencyDenominations.index'));
        }

        return view('currency_denominations.show')->with('currencyDenomination', $currencyDenomination);
    }

    /**
     * Show the form for editing the specified CurrencyDenomination.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $currencyDenomination = $this->currencyDenominationRepository->findWithoutFail($id);

        if (empty($currencyDenomination)) {
            Flash::error('Currency Denomination not found');

            return redirect(route('currencyDenominations.index'));
        }

        return view('currency_denominations.edit')->with('currencyDenomination', $currencyDenomination);
    }

    /**
     * Update the specified CurrencyDenomination in storage.
     *
     * @param  int              $id
     * @param UpdateCurrencyDenominationRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCurrencyDenominationRequest $request)
    {
        $currencyDenomination = $this->currencyDenominationRepository->findWithoutFail($id);

        if (empty($currencyDenomination)) {
            Flash::error('Currency Denomination not found');

            return redirect(route('currencyDenominations.index'));
        }

        $currencyDenomination = $this->currencyDenominationRepository->update($request->all(), $id);

        Flash::success('Currency Denomination updated successfully.');

        return redirect(route('currencyDenominations.index'));
    }

    /**
     * Remove the specified CurrencyDenomination from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $currencyDenomination = $this->currencyDenominationRepository->findWithoutFail($id);

        if (empty($currencyDenomination)) {
            Flash::error('Currency Denomination not found');

            return redirect(route('currencyDenominations.index'));
        }

        $this->currencyDenominationRepository->delete($id);

        Flash::success('Currency Denomination deleted successfully.');

        return redirect(route('currencyDenominations.index'));
    }
}
