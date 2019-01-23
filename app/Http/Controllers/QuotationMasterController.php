<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateQuotationMasterRequest;
use App\Http\Requests\UpdateQuotationMasterRequest;
use App\Repositories\QuotationMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class QuotationMasterController extends AppBaseController
{
    /** @var  QuotationMasterRepository */
    private $quotationMasterRepository;

    public function __construct(QuotationMasterRepository $quotationMasterRepo)
    {
        $this->quotationMasterRepository = $quotationMasterRepo;
    }

    /**
     * Display a listing of the QuotationMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->quotationMasterRepository->pushCriteria(new RequestCriteria($request));
        $quotationMasters = $this->quotationMasterRepository->all();

        return view('quotation_masters.index')
            ->with('quotationMasters', $quotationMasters);
    }

    /**
     * Show the form for creating a new QuotationMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('quotation_masters.create');
    }

    /**
     * Store a newly created QuotationMaster in storage.
     *
     * @param CreateQuotationMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateQuotationMasterRequest $request)
    {
        $input = $request->all();

        $quotationMaster = $this->quotationMasterRepository->create($input);

        Flash::success('Quotation Master saved successfully.');

        return redirect(route('quotationMasters.index'));
    }

    /**
     * Display the specified QuotationMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $quotationMaster = $this->quotationMasterRepository->findWithoutFail($id);

        if (empty($quotationMaster)) {
            Flash::error('Quotation Master not found');

            return redirect(route('quotationMasters.index'));
        }

        return view('quotation_masters.show')->with('quotationMaster', $quotationMaster);
    }

    /**
     * Show the form for editing the specified QuotationMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $quotationMaster = $this->quotationMasterRepository->findWithoutFail($id);

        if (empty($quotationMaster)) {
            Flash::error('Quotation Master not found');

            return redirect(route('quotationMasters.index'));
        }

        return view('quotation_masters.edit')->with('quotationMaster', $quotationMaster);
    }

    /**
     * Update the specified QuotationMaster in storage.
     *
     * @param  int              $id
     * @param UpdateQuotationMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateQuotationMasterRequest $request)
    {
        $quotationMaster = $this->quotationMasterRepository->findWithoutFail($id);

        if (empty($quotationMaster)) {
            Flash::error('Quotation Master not found');

            return redirect(route('quotationMasters.index'));
        }

        $quotationMaster = $this->quotationMasterRepository->update($request->all(), $id);

        Flash::success('Quotation Master updated successfully.');

        return redirect(route('quotationMasters.index'));
    }

    /**
     * Remove the specified QuotationMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $quotationMaster = $this->quotationMasterRepository->findWithoutFail($id);

        if (empty($quotationMaster)) {
            Flash::error('Quotation Master not found');

            return redirect(route('quotationMasters.index'));
        }

        $this->quotationMasterRepository->delete($id);

        Flash::success('Quotation Master deleted successfully.');

        return redirect(route('quotationMasters.index'));
    }
}
