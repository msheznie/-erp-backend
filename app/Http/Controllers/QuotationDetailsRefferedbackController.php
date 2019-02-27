<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateQuotationDetailsRefferedbackRequest;
use App\Http\Requests\UpdateQuotationDetailsRefferedbackRequest;
use App\Repositories\QuotationDetailsRefferedbackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class QuotationDetailsRefferedbackController extends AppBaseController
{
    /** @var  QuotationDetailsRefferedbackRepository */
    private $quotationDetailsRefferedbackRepository;

    public function __construct(QuotationDetailsRefferedbackRepository $quotationDetailsRefferedbackRepo)
    {
        $this->quotationDetailsRefferedbackRepository = $quotationDetailsRefferedbackRepo;
    }

    /**
     * Display a listing of the QuotationDetailsRefferedback.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->quotationDetailsRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $quotationDetailsRefferedbacks = $this->quotationDetailsRefferedbackRepository->all();

        return view('quotation_details_refferedbacks.index')
            ->with('quotationDetailsRefferedbacks', $quotationDetailsRefferedbacks);
    }

    /**
     * Show the form for creating a new QuotationDetailsRefferedback.
     *
     * @return Response
     */
    public function create()
    {
        return view('quotation_details_refferedbacks.create');
    }

    /**
     * Store a newly created QuotationDetailsRefferedback in storage.
     *
     * @param CreateQuotationDetailsRefferedbackRequest $request
     *
     * @return Response
     */
    public function store(CreateQuotationDetailsRefferedbackRequest $request)
    {
        $input = $request->all();

        $quotationDetailsRefferedback = $this->quotationDetailsRefferedbackRepository->create($input);

        Flash::success('Quotation Details Refferedback saved successfully.');

        return redirect(route('quotationDetailsRefferedbacks.index'));
    }

    /**
     * Display the specified QuotationDetailsRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $quotationDetailsRefferedback = $this->quotationDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($quotationDetailsRefferedback)) {
            Flash::error('Quotation Details Refferedback not found');

            return redirect(route('quotationDetailsRefferedbacks.index'));
        }

        return view('quotation_details_refferedbacks.show')->with('quotationDetailsRefferedback', $quotationDetailsRefferedback);
    }

    /**
     * Show the form for editing the specified QuotationDetailsRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $quotationDetailsRefferedback = $this->quotationDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($quotationDetailsRefferedback)) {
            Flash::error('Quotation Details Refferedback not found');

            return redirect(route('quotationDetailsRefferedbacks.index'));
        }

        return view('quotation_details_refferedbacks.edit')->with('quotationDetailsRefferedback', $quotationDetailsRefferedback);
    }

    /**
     * Update the specified QuotationDetailsRefferedback in storage.
     *
     * @param  int              $id
     * @param UpdateQuotationDetailsRefferedbackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateQuotationDetailsRefferedbackRequest $request)
    {
        $quotationDetailsRefferedback = $this->quotationDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($quotationDetailsRefferedback)) {
            Flash::error('Quotation Details Refferedback not found');

            return redirect(route('quotationDetailsRefferedbacks.index'));
        }

        $quotationDetailsRefferedback = $this->quotationDetailsRefferedbackRepository->update($request->all(), $id);

        Flash::success('Quotation Details Refferedback updated successfully.');

        return redirect(route('quotationDetailsRefferedbacks.index'));
    }

    /**
     * Remove the specified QuotationDetailsRefferedback from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $quotationDetailsRefferedback = $this->quotationDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($quotationDetailsRefferedback)) {
            Flash::error('Quotation Details Refferedback not found');

            return redirect(route('quotationDetailsRefferedbacks.index'));
        }

        $this->quotationDetailsRefferedbackRepository->delete($id);

        Flash::success('Quotation Details Refferedback deleted successfully.');

        return redirect(route('quotationDetailsRefferedbacks.index'));
    }
}
