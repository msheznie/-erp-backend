<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaxFormulaDetailRequest;
use App\Http\Requests\UpdateTaxFormulaDetailRequest;
use App\Repositories\TaxFormulaDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TaxFormulaDetailController extends AppBaseController
{
    /** @var  TaxFormulaDetailRepository */
    private $taxFormulaDetailRepository;

    public function __construct(TaxFormulaDetailRepository $taxFormulaDetailRepo)
    {
        $this->taxFormulaDetailRepository = $taxFormulaDetailRepo;
    }

    /**
     * Display a listing of the TaxFormulaDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->taxFormulaDetailRepository->pushCriteria(new RequestCriteria($request));
        $taxFormulaDetails = $this->taxFormulaDetailRepository->all();

        return view('tax_formula_details.index')
            ->with('taxFormulaDetails', $taxFormulaDetails);
    }

    /**
     * Show the form for creating a new TaxFormulaDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('tax_formula_details.create');
    }

    /**
     * Store a newly created TaxFormulaDetail in storage.
     *
     * @param CreateTaxFormulaDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateTaxFormulaDetailRequest $request)
    {
        $input = $request->all();

        $taxFormulaDetail = $this->taxFormulaDetailRepository->create($input);

        Flash::success('Tax Formula Detail saved successfully.');

        return redirect(route('taxFormulaDetails.index'));
    }

    /**
     * Display the specified TaxFormulaDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $taxFormulaDetail = $this->taxFormulaDetailRepository->findWithoutFail($id);

        if (empty($taxFormulaDetail)) {
            Flash::error('Tax Formula Detail not found');

            return redirect(route('taxFormulaDetails.index'));
        }

        return view('tax_formula_details.show')->with('taxFormulaDetail', $taxFormulaDetail);
    }

    /**
     * Show the form for editing the specified TaxFormulaDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $taxFormulaDetail = $this->taxFormulaDetailRepository->findWithoutFail($id);

        if (empty($taxFormulaDetail)) {
            Flash::error('Tax Formula Detail not found');

            return redirect(route('taxFormulaDetails.index'));
        }

        return view('tax_formula_details.edit')->with('taxFormulaDetail', $taxFormulaDetail);
    }

    /**
     * Update the specified TaxFormulaDetail in storage.
     *
     * @param  int              $id
     * @param UpdateTaxFormulaDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTaxFormulaDetailRequest $request)
    {
        $taxFormulaDetail = $this->taxFormulaDetailRepository->findWithoutFail($id);

        if (empty($taxFormulaDetail)) {
            Flash::error('Tax Formula Detail not found');

            return redirect(route('taxFormulaDetails.index'));
        }

        $taxFormulaDetail = $this->taxFormulaDetailRepository->update($request->all(), $id);

        Flash::success('Tax Formula Detail updated successfully.');

        return redirect(route('taxFormulaDetails.index'));
    }

    /**
     * Remove the specified TaxFormulaDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $taxFormulaDetail = $this->taxFormulaDetailRepository->findWithoutFail($id);

        if (empty($taxFormulaDetail)) {
            Flash::error('Tax Formula Detail not found');

            return redirect(route('taxFormulaDetails.index'));
        }

        $this->taxFormulaDetailRepository->delete($id);

        Flash::success('Tax Formula Detail deleted successfully.');

        return redirect(route('taxFormulaDetails.index'));
    }
}
