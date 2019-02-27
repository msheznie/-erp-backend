<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGposInvoiceDetailRequest;
use App\Http\Requests\UpdateGposInvoiceDetailRequest;
use App\Repositories\GposInvoiceDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class GposInvoiceDetailController extends AppBaseController
{
    /** @var  GposInvoiceDetailRepository */
    private $gposInvoiceDetailRepository;

    public function __construct(GposInvoiceDetailRepository $gposInvoiceDetailRepo)
    {
        $this->gposInvoiceDetailRepository = $gposInvoiceDetailRepo;
    }

    /**
     * Display a listing of the GposInvoiceDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->gposInvoiceDetailRepository->pushCriteria(new RequestCriteria($request));
        $gposInvoiceDetails = $this->gposInvoiceDetailRepository->all();

        return view('gpos_invoice_details.index')
            ->with('gposInvoiceDetails', $gposInvoiceDetails);
    }

    /**
     * Show the form for creating a new GposInvoiceDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('gpos_invoice_details.create');
    }

    /**
     * Store a newly created GposInvoiceDetail in storage.
     *
     * @param CreateGposInvoiceDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateGposInvoiceDetailRequest $request)
    {
        $input = $request->all();

        $gposInvoiceDetail = $this->gposInvoiceDetailRepository->create($input);

        Flash::success('Gpos Invoice Detail saved successfully.');

        return redirect(route('gposInvoiceDetails.index'));
    }

    /**
     * Display the specified GposInvoiceDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $gposInvoiceDetail = $this->gposInvoiceDetailRepository->findWithoutFail($id);

        if (empty($gposInvoiceDetail)) {
            Flash::error('Gpos Invoice Detail not found');

            return redirect(route('gposInvoiceDetails.index'));
        }

        return view('gpos_invoice_details.show')->with('gposInvoiceDetail', $gposInvoiceDetail);
    }

    /**
     * Show the form for editing the specified GposInvoiceDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $gposInvoiceDetail = $this->gposInvoiceDetailRepository->findWithoutFail($id);

        if (empty($gposInvoiceDetail)) {
            Flash::error('Gpos Invoice Detail not found');

            return redirect(route('gposInvoiceDetails.index'));
        }

        return view('gpos_invoice_details.edit')->with('gposInvoiceDetail', $gposInvoiceDetail);
    }

    /**
     * Update the specified GposInvoiceDetail in storage.
     *
     * @param  int              $id
     * @param UpdateGposInvoiceDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGposInvoiceDetailRequest $request)
    {
        $gposInvoiceDetail = $this->gposInvoiceDetailRepository->findWithoutFail($id);

        if (empty($gposInvoiceDetail)) {
            Flash::error('Gpos Invoice Detail not found');

            return redirect(route('gposInvoiceDetails.index'));
        }

        $gposInvoiceDetail = $this->gposInvoiceDetailRepository->update($request->all(), $id);

        Flash::success('Gpos Invoice Detail updated successfully.');

        return redirect(route('gposInvoiceDetails.index'));
    }

    /**
     * Remove the specified GposInvoiceDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $gposInvoiceDetail = $this->gposInvoiceDetailRepository->findWithoutFail($id);

        if (empty($gposInvoiceDetail)) {
            Flash::error('Gpos Invoice Detail not found');

            return redirect(route('gposInvoiceDetails.index'));
        }

        $this->gposInvoiceDetailRepository->delete($id);

        Flash::success('Gpos Invoice Detail deleted successfully.');

        return redirect(route('gposInvoiceDetails.index'));
    }
}
