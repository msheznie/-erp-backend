<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGposInvoiceRequest;
use App\Http\Requests\UpdateGposInvoiceRequest;
use App\Repositories\GposInvoiceRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class GposInvoiceController extends AppBaseController
{
    /** @var  GposInvoiceRepository */
    private $gposInvoiceRepository;

    public function __construct(GposInvoiceRepository $gposInvoiceRepo)
    {
        $this->gposInvoiceRepository = $gposInvoiceRepo;
    }

    /**
     * Display a listing of the GposInvoice.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->gposInvoiceRepository->pushCriteria(new RequestCriteria($request));
        $gposInvoices = $this->gposInvoiceRepository->all();

        return view('gpos_invoices.index')
            ->with('gposInvoices', $gposInvoices);
    }

    /**
     * Show the form for creating a new GposInvoice.
     *
     * @return Response
     */
    public function create()
    {
        return view('gpos_invoices.create');
    }

    /**
     * Store a newly created GposInvoice in storage.
     *
     * @param CreateGposInvoiceRequest $request
     *
     * @return Response
     */
    public function store(CreateGposInvoiceRequest $request)
    {
        $input = $request->all();

        $gposInvoice = $this->gposInvoiceRepository->create($input);

        Flash::success('Gpos Invoice saved successfully.');

        return redirect(route('gposInvoices.index'));
    }

    /**
     * Display the specified GposInvoice.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $gposInvoice = $this->gposInvoiceRepository->findWithoutFail($id);

        if (empty($gposInvoice)) {
            Flash::error('Gpos Invoice not found');

            return redirect(route('gposInvoices.index'));
        }

        return view('gpos_invoices.show')->with('gposInvoice', $gposInvoice);
    }

    /**
     * Show the form for editing the specified GposInvoice.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $gposInvoice = $this->gposInvoiceRepository->findWithoutFail($id);

        if (empty($gposInvoice)) {
            Flash::error('Gpos Invoice not found');

            return redirect(route('gposInvoices.index'));
        }

        return view('gpos_invoices.edit')->with('gposInvoice', $gposInvoice);
    }

    /**
     * Update the specified GposInvoice in storage.
     *
     * @param  int              $id
     * @param UpdateGposInvoiceRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGposInvoiceRequest $request)
    {
        $gposInvoice = $this->gposInvoiceRepository->findWithoutFail($id);

        if (empty($gposInvoice)) {
            Flash::error('Gpos Invoice not found');

            return redirect(route('gposInvoices.index'));
        }

        $gposInvoice = $this->gposInvoiceRepository->update($request->all(), $id);

        Flash::success('Gpos Invoice updated successfully.');

        return redirect(route('gposInvoices.index'));
    }

    /**
     * Remove the specified GposInvoice from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $gposInvoice = $this->gposInvoiceRepository->findWithoutFail($id);

        if (empty($gposInvoice)) {
            Flash::error('Gpos Invoice not found');

            return redirect(route('gposInvoices.index'));
        }

        $this->gposInvoiceRepository->delete($id);

        Flash::success('Gpos Invoice deleted successfully.');

        return redirect(route('gposInvoices.index'));
    }
}
