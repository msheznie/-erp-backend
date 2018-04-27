<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaxFormulaMasterRequest;
use App\Http\Requests\UpdateTaxFormulaMasterRequest;
use App\Repositories\TaxFormulaMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TaxFormulaMasterController extends AppBaseController
{
    /** @var  TaxFormulaMasterRepository */
    private $taxFormulaMasterRepository;

    public function __construct(TaxFormulaMasterRepository $taxFormulaMasterRepo)
    {
        $this->taxFormulaMasterRepository = $taxFormulaMasterRepo;
    }

    /**
     * Display a listing of the TaxFormulaMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->taxFormulaMasterRepository->pushCriteria(new RequestCriteria($request));
        $taxFormulaMasters = $this->taxFormulaMasterRepository->all();

        return view('tax_formula_masters.index')
            ->with('taxFormulaMasters', $taxFormulaMasters);
    }

    /**
     * Show the form for creating a new TaxFormulaMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('tax_formula_masters.create');
    }

    /**
     * Store a newly created TaxFormulaMaster in storage.
     *
     * @param CreateTaxFormulaMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateTaxFormulaMasterRequest $request)
    {
        $input = $request->all();

        $taxFormulaMaster = $this->taxFormulaMasterRepository->create($input);

        Flash::success('Tax Formula Master saved successfully.');

        return redirect(route('taxFormulaMasters.index'));
    }

    /**
     * Display the specified TaxFormulaMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $taxFormulaMaster = $this->taxFormulaMasterRepository->findWithoutFail($id);

        if (empty($taxFormulaMaster)) {
            Flash::error('Tax Formula Master not found');

            return redirect(route('taxFormulaMasters.index'));
        }

        return view('tax_formula_masters.show')->with('taxFormulaMaster', $taxFormulaMaster);
    }

    /**
     * Show the form for editing the specified TaxFormulaMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $taxFormulaMaster = $this->taxFormulaMasterRepository->findWithoutFail($id);

        if (empty($taxFormulaMaster)) {
            Flash::error('Tax Formula Master not found');

            return redirect(route('taxFormulaMasters.index'));
        }

        return view('tax_formula_masters.edit')->with('taxFormulaMaster', $taxFormulaMaster);
    }

    /**
     * Update the specified TaxFormulaMaster in storage.
     *
     * @param  int              $id
     * @param UpdateTaxFormulaMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTaxFormulaMasterRequest $request)
    {
        $taxFormulaMaster = $this->taxFormulaMasterRepository->findWithoutFail($id);

        if (empty($taxFormulaMaster)) {
            Flash::error('Tax Formula Master not found');

            return redirect(route('taxFormulaMasters.index'));
        }

        $taxFormulaMaster = $this->taxFormulaMasterRepository->update($request->all(), $id);

        Flash::success('Tax Formula Master updated successfully.');

        return redirect(route('taxFormulaMasters.index'));
    }

    /**
     * Remove the specified TaxFormulaMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $taxFormulaMaster = $this->taxFormulaMasterRepository->findWithoutFail($id);

        if (empty($taxFormulaMaster)) {
            Flash::error('Tax Formula Master not found');

            return redirect(route('taxFormulaMasters.index'));
        }

        $this->taxFormulaMasterRepository->delete($id);

        Flash::success('Tax Formula Master deleted successfully.');

        return redirect(route('taxFormulaMasters.index'));
    }
}
