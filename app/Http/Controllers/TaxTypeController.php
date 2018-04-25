<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaxTypeRequest;
use App\Http\Requests\UpdateTaxTypeRequest;
use App\Repositories\TaxTypeRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TaxTypeController extends AppBaseController
{
    /** @var  TaxTypeRepository */
    private $taxTypeRepository;

    public function __construct(TaxTypeRepository $taxTypeRepo)
    {
        $this->taxTypeRepository = $taxTypeRepo;
    }

    /**
     * Display a listing of the TaxType.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->taxTypeRepository->pushCriteria(new RequestCriteria($request));
        $taxTypes = $this->taxTypeRepository->all();

        return view('tax_types.index')
            ->with('taxTypes', $taxTypes);
    }

    /**
     * Show the form for creating a new TaxType.
     *
     * @return Response
     */
    public function create()
    {
        return view('tax_types.create');
    }

    /**
     * Store a newly created TaxType in storage.
     *
     * @param CreateTaxTypeRequest $request
     *
     * @return Response
     */
    public function store(CreateTaxTypeRequest $request)
    {
        $input = $request->all();

        $taxType = $this->taxTypeRepository->create($input);

        Flash::success('Tax Type saved successfully.');

        return redirect(route('taxTypes.index'));
    }

    /**
     * Display the specified TaxType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $taxType = $this->taxTypeRepository->findWithoutFail($id);

        if (empty($taxType)) {
            Flash::error('Tax Type not found');

            return redirect(route('taxTypes.index'));
        }

        return view('tax_types.show')->with('taxType', $taxType);
    }

    /**
     * Show the form for editing the specified TaxType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $taxType = $this->taxTypeRepository->findWithoutFail($id);

        if (empty($taxType)) {
            Flash::error('Tax Type not found');

            return redirect(route('taxTypes.index'));
        }

        return view('tax_types.edit')->with('taxType', $taxType);
    }

    /**
     * Update the specified TaxType in storage.
     *
     * @param  int              $id
     * @param UpdateTaxTypeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTaxTypeRequest $request)
    {
        $taxType = $this->taxTypeRepository->findWithoutFail($id);

        if (empty($taxType)) {
            Flash::error('Tax Type not found');

            return redirect(route('taxTypes.index'));
        }

        $taxType = $this->taxTypeRepository->update($request->all(), $id);

        Flash::success('Tax Type updated successfully.');

        return redirect(route('taxTypes.index'));
    }

    /**
     * Remove the specified TaxType from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $taxType = $this->taxTypeRepository->findWithoutFail($id);

        if (empty($taxType)) {
            Flash::error('Tax Type not found');

            return redirect(route('taxTypes.index'));
        }

        $this->taxTypeRepository->delete($id);

        Flash::success('Tax Type deleted successfully.');

        return redirect(route('taxTypes.index'));
    }
}
