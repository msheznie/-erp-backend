<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGposInvoicePaymentsRequest;
use App\Http\Requests\UpdateGposInvoicePaymentsRequest;
use App\Repositories\GposInvoicePaymentsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class GposInvoicePaymentsController extends AppBaseController
{
    /** @var  GposInvoicePaymentsRepository */
    private $gposInvoicePaymentsRepository;

    public function __construct(GposInvoicePaymentsRepository $gposInvoicePaymentsRepo)
    {
        $this->gposInvoicePaymentsRepository = $gposInvoicePaymentsRepo;
    }

    /**
     * Display a listing of the GposInvoicePayments.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->gposInvoicePaymentsRepository->pushCriteria(new RequestCriteria($request));
        $gposInvoicePayments = $this->gposInvoicePaymentsRepository->all();

        return view('gpos_invoice_payments.index')
            ->with('gposInvoicePayments', $gposInvoicePayments);
    }

    /**
     * Show the form for creating a new GposInvoicePayments.
     *
     * @return Response
     */
    public function create()
    {
        return view('gpos_invoice_payments.create');
    }

    /**
     * Store a newly created GposInvoicePayments in storage.
     *
     * @param CreateGposInvoicePaymentsRequest $request
     *
     * @return Response
     */
    public function store(CreateGposInvoicePaymentsRequest $request)
    {
        $input = $request->all();

        $gposInvoicePayments = $this->gposInvoicePaymentsRepository->create($input);

        Flash::success('Gpos Invoice Payments saved successfully.');

        return redirect(route('gposInvoicePayments.index'));
    }

    /**
     * Display the specified GposInvoicePayments.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $gposInvoicePayments = $this->gposInvoicePaymentsRepository->findWithoutFail($id);

        if (empty($gposInvoicePayments)) {
            Flash::error('Gpos Invoice Payments not found');

            return redirect(route('gposInvoicePayments.index'));
        }

        return view('gpos_invoice_payments.show')->with('gposInvoicePayments', $gposInvoicePayments);
    }

    /**
     * Show the form for editing the specified GposInvoicePayments.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $gposInvoicePayments = $this->gposInvoicePaymentsRepository->findWithoutFail($id);

        if (empty($gposInvoicePayments)) {
            Flash::error('Gpos Invoice Payments not found');

            return redirect(route('gposInvoicePayments.index'));
        }

        return view('gpos_invoice_payments.edit')->with('gposInvoicePayments', $gposInvoicePayments);
    }

    /**
     * Update the specified GposInvoicePayments in storage.
     *
     * @param  int              $id
     * @param UpdateGposInvoicePaymentsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGposInvoicePaymentsRequest $request)
    {
        $gposInvoicePayments = $this->gposInvoicePaymentsRepository->findWithoutFail($id);

        if (empty($gposInvoicePayments)) {
            Flash::error('Gpos Invoice Payments not found');

            return redirect(route('gposInvoicePayments.index'));
        }

        $gposInvoicePayments = $this->gposInvoicePaymentsRepository->update($request->all(), $id);

        Flash::success('Gpos Invoice Payments updated successfully.');

        return redirect(route('gposInvoicePayments.index'));
    }

    /**
     * Remove the specified GposInvoicePayments from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $gposInvoicePayments = $this->gposInvoicePaymentsRepository->findWithoutFail($id);

        if (empty($gposInvoicePayments)) {
            Flash::error('Gpos Invoice Payments not found');

            return redirect(route('gposInvoicePayments.index'));
        }

        $this->gposInvoicePaymentsRepository->delete($id);

        Flash::success('Gpos Invoice Payments deleted successfully.');

        return redirect(route('gposInvoicePayments.index'));
    }
}
