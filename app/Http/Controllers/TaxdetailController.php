<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaxdetailRequest;
use App\Http\Requests\UpdateTaxdetailRequest;
use App\Repositories\TaxdetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TaxdetailController extends AppBaseController
{
    /** @var  TaxdetailRepository */
    private $taxdetailRepository;

    public function __construct(TaxdetailRepository $taxdetailRepo)
    {
        $this->taxdetailRepository = $taxdetailRepo;
    }

    /**
     * Display a listing of the Taxdetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->taxdetailRepository->pushCriteria(new RequestCriteria($request));
        $taxdetails = $this->taxdetailRepository->all();

        return view('taxdetails.index')
            ->with('taxdetails', $taxdetails);
    }

    /**
     * Show the form for creating a new Taxdetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('taxdetails.create');
    }

    /**
     * Store a newly created Taxdetail in storage.
     *
     * @param CreateTaxdetailRequest $request
     *
     * @return Response
     */
    public function store(CreateTaxdetailRequest $request)
    {
        $input = $request->all();

        $taxdetail = $this->taxdetailRepository->create($input);

        Flash::success('Taxdetail saved successfully.');

        return redirect(route('taxdetails.index'));
    }

    /**
     * Display the specified Taxdetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $taxdetail = $this->taxdetailRepository->findWithoutFail($id);

        if (empty($taxdetail)) {
            Flash::error('Taxdetail not found');

            return redirect(route('taxdetails.index'));
        }

        return view('taxdetails.show')->with('taxdetail', $taxdetail);
    }

    /**
     * Show the form for editing the specified Taxdetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $taxdetail = $this->taxdetailRepository->findWithoutFail($id);

        if (empty($taxdetail)) {
            Flash::error('Taxdetail not found');

            return redirect(route('taxdetails.index'));
        }

        return view('taxdetails.edit')->with('taxdetail', $taxdetail);
    }

    /**
     * Update the specified Taxdetail in storage.
     *
     * @param  int              $id
     * @param UpdateTaxdetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTaxdetailRequest $request)
    {
        $taxdetail = $this->taxdetailRepository->findWithoutFail($id);

        if (empty($taxdetail)) {
            Flash::error('Taxdetail not found');

            return redirect(route('taxdetails.index'));
        }

        $taxdetail = $this->taxdetailRepository->update($request->all(), $id);

        Flash::success('Taxdetail updated successfully.');

        return redirect(route('taxdetails.index'));
    }

    /**
     * Remove the specified Taxdetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $taxdetail = $this->taxdetailRepository->findWithoutFail($id);

        if (empty($taxdetail)) {
            Flash::error('Taxdetail not found');

            return redirect(route('taxdetails.index'));
        }

        $this->taxdetailRepository->delete($id);

        Flash::success('Taxdetail deleted successfully.');

        return redirect(route('taxdetails.index'));
    }
}
