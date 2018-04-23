<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaxAuthorityRequest;
use App\Http\Requests\UpdateTaxAuthorityRequest;
use App\Repositories\TaxAuthorityRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TaxAuthorityController extends AppBaseController
{
    /** @var  TaxAuthorityRepository */
    private $taxAuthorityRepository;

    public function __construct(TaxAuthorityRepository $taxAuthorityRepo)
    {
        $this->taxAuthorityRepository = $taxAuthorityRepo;
    }

    /**
     * Display a listing of the TaxAuthority.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->taxAuthorityRepository->pushCriteria(new RequestCriteria($request));
        $taxAuthorities = $this->taxAuthorityRepository->all();

        return view('tax_authorities.index')
            ->with('taxAuthorities', $taxAuthorities);
    }

    /**
     * Show the form for creating a new TaxAuthority.
     *
     * @return Response
     */
    public function create()
    {
        return view('tax_authorities.create');
    }

    /**
     * Store a newly created TaxAuthority in storage.
     *
     * @param CreateTaxAuthorityRequest $request
     *
     * @return Response
     */
    public function store(CreateTaxAuthorityRequest $request)
    {
        $input = $request->all();

        $taxAuthority = $this->taxAuthorityRepository->create($input);

        Flash::success('Tax Authority saved successfully.');

        return redirect(route('taxAuthorities.index'));
    }

    /**
     * Display the specified TaxAuthority.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $taxAuthority = $this->taxAuthorityRepository->findWithoutFail($id);

        if (empty($taxAuthority)) {
            Flash::error('Tax Authority not found');

            return redirect(route('taxAuthorities.index'));
        }

        return view('tax_authorities.show')->with('taxAuthority', $taxAuthority);
    }

    /**
     * Show the form for editing the specified TaxAuthority.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $taxAuthority = $this->taxAuthorityRepository->findWithoutFail($id);

        if (empty($taxAuthority)) {
            Flash::error('Tax Authority not found');

            return redirect(route('taxAuthorities.index'));
        }

        return view('tax_authorities.edit')->with('taxAuthority', $taxAuthority);
    }

    /**
     * Update the specified TaxAuthority in storage.
     *
     * @param  int              $id
     * @param UpdateTaxAuthorityRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTaxAuthorityRequest $request)
    {
        $taxAuthority = $this->taxAuthorityRepository->findWithoutFail($id);

        if (empty($taxAuthority)) {
            Flash::error('Tax Authority not found');

            return redirect(route('taxAuthorities.index'));
        }

        $taxAuthority = $this->taxAuthorityRepository->update($request->all(), $id);

        Flash::success('Tax Authority updated successfully.');

        return redirect(route('taxAuthorities.index'));
    }

    /**
     * Remove the specified TaxAuthority from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $taxAuthority = $this->taxAuthorityRepository->findWithoutFail($id);

        if (empty($taxAuthority)) {
            Flash::error('Tax Authority not found');

            return redirect(route('taxAuthorities.index'));
        }

        $this->taxAuthorityRepository->delete($id);

        Flash::success('Tax Authority deleted successfully.');

        return redirect(route('taxAuthorities.index'));
    }
}
