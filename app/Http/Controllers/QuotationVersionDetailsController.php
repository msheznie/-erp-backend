<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateQuotationVersionDetailsRequest;
use App\Http\Requests\UpdateQuotationVersionDetailsRequest;
use App\Repositories\QuotationVersionDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class QuotationVersionDetailsController extends AppBaseController
{
    /** @var  QuotationVersionDetailsRepository */
    private $quotationVersionDetailsRepository;

    public function __construct(QuotationVersionDetailsRepository $quotationVersionDetailsRepo)
    {
        $this->quotationVersionDetailsRepository = $quotationVersionDetailsRepo;
    }

    /**
     * Display a listing of the QuotationVersionDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->quotationVersionDetailsRepository->pushCriteria(new RequestCriteria($request));
        $quotationVersionDetails = $this->quotationVersionDetailsRepository->all();

        return view('quotation_version_details.index')
            ->with('quotationVersionDetails', $quotationVersionDetails);
    }

    /**
     * Show the form for creating a new QuotationVersionDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('quotation_version_details.create');
    }

    /**
     * Store a newly created QuotationVersionDetails in storage.
     *
     * @param CreateQuotationVersionDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateQuotationVersionDetailsRequest $request)
    {
        $input = $request->all();

        $quotationVersionDetails = $this->quotationVersionDetailsRepository->create($input);

        Flash::success('Quotation Version Details saved successfully.');

        return redirect(route('quotationVersionDetails.index'));
    }

    /**
     * Display the specified QuotationVersionDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $quotationVersionDetails = $this->quotationVersionDetailsRepository->findWithoutFail($id);

        if (empty($quotationVersionDetails)) {
            Flash::error('Quotation Version Details not found');

            return redirect(route('quotationVersionDetails.index'));
        }

        return view('quotation_version_details.show')->with('quotationVersionDetails', $quotationVersionDetails);
    }

    /**
     * Show the form for editing the specified QuotationVersionDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $quotationVersionDetails = $this->quotationVersionDetailsRepository->findWithoutFail($id);

        if (empty($quotationVersionDetails)) {
            Flash::error('Quotation Version Details not found');

            return redirect(route('quotationVersionDetails.index'));
        }

        return view('quotation_version_details.edit')->with('quotationVersionDetails', $quotationVersionDetails);
    }

    /**
     * Update the specified QuotationVersionDetails in storage.
     *
     * @param  int              $id
     * @param UpdateQuotationVersionDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateQuotationVersionDetailsRequest $request)
    {
        $quotationVersionDetails = $this->quotationVersionDetailsRepository->findWithoutFail($id);

        if (empty($quotationVersionDetails)) {
            Flash::error('Quotation Version Details not found');

            return redirect(route('quotationVersionDetails.index'));
        }

        $quotationVersionDetails = $this->quotationVersionDetailsRepository->update($request->all(), $id);

        Flash::success('Quotation Version Details updated successfully.');

        return redirect(route('quotationVersionDetails.index'));
    }

    /**
     * Remove the specified QuotationVersionDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $quotationVersionDetails = $this->quotationVersionDetailsRepository->findWithoutFail($id);

        if (empty($quotationVersionDetails)) {
            Flash::error('Quotation Version Details not found');

            return redirect(route('quotationVersionDetails.index'));
        }

        $this->quotationVersionDetailsRepository->delete($id);

        Flash::success('Quotation Version Details deleted successfully.');

        return redirect(route('quotationVersionDetails.index'));
    }
}
