<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaxRequest;
use App\Http\Requests\UpdateTaxRequest;
use App\Repositories\TaxRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TaxController extends AppBaseController
{
    /** @var  TaxRepository */
    private $taxRepository;

    public function __construct(TaxRepository $taxRepo)
    {
        $this->taxRepository = $taxRepo;
    }

    /**
     * Display a listing of the Tax.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->taxRepository->pushCriteria(new RequestCriteria($request));
        $taxes = $this->taxRepository->all();

        return view('taxes.index')
            ->with('taxes', $taxes);
    }

    /**
     * Show the form for creating a new Tax.
     *
     * @return Response
     */
    public function create()
    {
        return view('taxes.create');
    }

    /**
     * Store a newly created Tax in storage.
     *
     * @param CreateTaxRequest $request
     *
     * @return Response
     */
    public function store(CreateTaxRequest $request)
    {
        $input = $request->all();

        $tax = $this->taxRepository->create($input);

        Flash::success('Tax saved successfully.');

        return redirect(route('taxes.index'));
    }

    /**
     * Display the specified Tax.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $tax = $this->taxRepository->findWithoutFail($id);

        if (empty($tax)) {
            Flash::error('Tax not found');

            return redirect(route('taxes.index'));
        }

        return view('taxes.show')->with('tax', $tax);
    }

    /**
     * Show the form for editing the specified Tax.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $tax = $this->taxRepository->findWithoutFail($id);

        if (empty($tax)) {
            Flash::error('Tax not found');

            return redirect(route('taxes.index'));
        }

        return view('taxes.edit')->with('tax', $tax);
    }

    /**
     * Update the specified Tax in storage.
     *
     * @param  int              $id
     * @param UpdateTaxRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTaxRequest $request)
    {
        $tax = $this->taxRepository->findWithoutFail($id);

        if (empty($tax)) {
            Flash::error('Tax not found');

            return redirect(route('taxes.index'));
        }

        $tax = $this->taxRepository->update($request->all(), $id);

        Flash::success('Tax updated successfully.');

        return redirect(route('taxes.index'));
    }

    /**
     * Remove the specified Tax from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $tax = $this->taxRepository->findWithoutFail($id);

        if (empty($tax)) {
            Flash::error('Tax not found');

            return redirect(route('taxes.index'));
        }

        $this->taxRepository->delete($id);

        Flash::success('Tax deleted successfully.');

        return redirect(route('taxes.index'));
    }
}
