<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateQuotationMasterRefferedbackRequest;
use App\Http\Requests\UpdateQuotationMasterRefferedbackRequest;
use App\Repositories\QuotationMasterRefferedbackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class QuotationMasterRefferedbackController extends AppBaseController
{
    /** @var  QuotationMasterRefferedbackRepository */
    private $quotationMasterRefferedbackRepository;

    public function __construct(QuotationMasterRefferedbackRepository $quotationMasterRefferedbackRepo)
    {
        $this->quotationMasterRefferedbackRepository = $quotationMasterRefferedbackRepo;
    }

    /**
     * Display a listing of the QuotationMasterRefferedback.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->quotationMasterRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $quotationMasterRefferedbacks = $this->quotationMasterRefferedbackRepository->all();

        return view('quotation_master_refferedbacks.index')
            ->with('quotationMasterRefferedbacks', $quotationMasterRefferedbacks);
    }

    /**
     * Show the form for creating a new QuotationMasterRefferedback.
     *
     * @return Response
     */
    public function create()
    {
        return view('quotation_master_refferedbacks.create');
    }

    /**
     * Store a newly created QuotationMasterRefferedback in storage.
     *
     * @param CreateQuotationMasterRefferedbackRequest $request
     *
     * @return Response
     */
    public function store(CreateQuotationMasterRefferedbackRequest $request)
    {
        $input = $request->all();

        $quotationMasterRefferedback = $this->quotationMasterRefferedbackRepository->create($input);

        Flash::success('Quotation Master Refferedback saved successfully.');

        return redirect(route('quotationMasterRefferedbacks.index'));
    }

    /**
     * Display the specified QuotationMasterRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $quotationMasterRefferedback = $this->quotationMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($quotationMasterRefferedback)) {
            Flash::error('Quotation Master Refferedback not found');

            return redirect(route('quotationMasterRefferedbacks.index'));
        }

        return view('quotation_master_refferedbacks.show')->with('quotationMasterRefferedback', $quotationMasterRefferedback);
    }

    /**
     * Show the form for editing the specified QuotationMasterRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $quotationMasterRefferedback = $this->quotationMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($quotationMasterRefferedback)) {
            Flash::error('Quotation Master Refferedback not found');

            return redirect(route('quotationMasterRefferedbacks.index'));
        }

        return view('quotation_master_refferedbacks.edit')->with('quotationMasterRefferedback', $quotationMasterRefferedback);
    }

    /**
     * Update the specified QuotationMasterRefferedback in storage.
     *
     * @param  int              $id
     * @param UpdateQuotationMasterRefferedbackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateQuotationMasterRefferedbackRequest $request)
    {
        $quotationMasterRefferedback = $this->quotationMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($quotationMasterRefferedback)) {
            Flash::error('Quotation Master Refferedback not found');

            return redirect(route('quotationMasterRefferedbacks.index'));
        }

        $quotationMasterRefferedback = $this->quotationMasterRefferedbackRepository->update($request->all(), $id);

        Flash::success('Quotation Master Refferedback updated successfully.');

        return redirect(route('quotationMasterRefferedbacks.index'));
    }

    /**
     * Remove the specified QuotationMasterRefferedback from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $quotationMasterRefferedback = $this->quotationMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($quotationMasterRefferedback)) {
            Flash::error('Quotation Master Refferedback not found');

            return redirect(route('quotationMasterRefferedbacks.index'));
        }

        $this->quotationMasterRefferedbackRepository->delete($id);

        Flash::success('Quotation Master Refferedback deleted successfully.');

        return redirect(route('quotationMasterRefferedbacks.index'));
    }
}
