<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateQuotationDetailsRequest;
use App\Http\Requests\UpdateQuotationDetailsRequest;
use App\Repositories\QuotationDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class QuotationDetailsController extends AppBaseController
{
    /** @var  QuotationDetailsRepository */
    private $quotationDetailsRepository;

    public function __construct(QuotationDetailsRepository $quotationDetailsRepo)
    {
        $this->quotationDetailsRepository = $quotationDetailsRepo;
    }

    /**
     * Display a listing of the QuotationDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->quotationDetailsRepository->pushCriteria(new RequestCriteria($request));
        $quotationDetails = $this->quotationDetailsRepository->all();

        return view('quotation_details.index')
            ->with('quotationDetails', $quotationDetails);
    }

    /**
     * Show the form for creating a new QuotationDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('quotation_details.create');
    }

    /**
     * Store a newly created QuotationDetails in storage.
     *
     * @param CreateQuotationDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateQuotationDetailsRequest $request)
    {
        $input = $request->all();

        $quotationDetails = $this->quotationDetailsRepository->create($input);

        Flash::success('Quotation Details saved successfully.');

        return redirect(route('quotationDetails.index'));
    }

    /**
     * Display the specified QuotationDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $quotationDetails = $this->quotationDetailsRepository->findWithoutFail($id);

        if (empty($quotationDetails)) {
            Flash::error('Quotation Details not found');

            return redirect(route('quotationDetails.index'));
        }

        return view('quotation_details.show')->with('quotationDetails', $quotationDetails);
    }

    /**
     * Show the form for editing the specified QuotationDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $quotationDetails = $this->quotationDetailsRepository->findWithoutFail($id);

        if (empty($quotationDetails)) {
            Flash::error('Quotation Details not found');

            return redirect(route('quotationDetails.index'));
        }

        return view('quotation_details.edit')->with('quotationDetails', $quotationDetails);
    }

    /**
     * Update the specified QuotationDetails in storage.
     *
     * @param  int              $id
     * @param UpdateQuotationDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateQuotationDetailsRequest $request)
    {
        $quotationDetails = $this->quotationDetailsRepository->findWithoutFail($id);

        if (empty($quotationDetails)) {
            Flash::error('Quotation Details not found');

            return redirect(route('quotationDetails.index'));
        }

        $quotationDetails = $this->quotationDetailsRepository->update($request->all(), $id);

        Flash::success('Quotation Details updated successfully.');

        return redirect(route('quotationDetails.index'));
    }

    /**
     * Remove the specified QuotationDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $quotationDetails = $this->quotationDetailsRepository->findWithoutFail($id);

        if (empty($quotationDetails)) {
            Flash::error('Quotation Details not found');

            return redirect(route('quotationDetails.index'));
        }

        $this->quotationDetailsRepository->delete($id);

        Flash::success('Quotation Details deleted successfully.');

        return redirect(route('quotationDetails.index'));
    }
}
