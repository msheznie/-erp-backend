<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDirectInvoiceDetailsRequest;
use App\Http\Requests\UpdateDirectInvoiceDetailsRequest;
use App\Repositories\DirectInvoiceDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DirectInvoiceDetailsController extends AppBaseController
{
    /** @var  DirectInvoiceDetailsRepository */
    private $directInvoiceDetailsRepository;

    public function __construct(DirectInvoiceDetailsRepository $directInvoiceDetailsRepo)
    {
        $this->directInvoiceDetailsRepository = $directInvoiceDetailsRepo;
    }

    /**
     * Display a listing of the DirectInvoiceDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->directInvoiceDetailsRepository->pushCriteria(new RequestCriteria($request));
        $directInvoiceDetails = $this->directInvoiceDetailsRepository->all();

        return view('direct_invoice_details.index')
            ->with('directInvoiceDetails', $directInvoiceDetails);
    }

    /**
     * Show the form for creating a new DirectInvoiceDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('direct_invoice_details.create');
    }

    /**
     * Store a newly created DirectInvoiceDetails in storage.
     *
     * @param CreateDirectInvoiceDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateDirectInvoiceDetailsRequest $request)
    {
        $input = $request->all();

        $directInvoiceDetails = $this->directInvoiceDetailsRepository->create($input);

        Flash::success('Direct Invoice Details saved successfully.');

        return redirect(route('directInvoiceDetails.index'));
    }

    /**
     * Display the specified DirectInvoiceDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $directInvoiceDetails = $this->directInvoiceDetailsRepository->findWithoutFail($id);

        if (empty($directInvoiceDetails)) {
            Flash::error('Direct Invoice Details not found');

            return redirect(route('directInvoiceDetails.index'));
        }

        return view('direct_invoice_details.show')->with('directInvoiceDetails', $directInvoiceDetails);
    }

    /**
     * Show the form for editing the specified DirectInvoiceDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $directInvoiceDetails = $this->directInvoiceDetailsRepository->findWithoutFail($id);

        if (empty($directInvoiceDetails)) {
            Flash::error('Direct Invoice Details not found');

            return redirect(route('directInvoiceDetails.index'));
        }

        return view('direct_invoice_details.edit')->with('directInvoiceDetails', $directInvoiceDetails);
    }

    /**
     * Update the specified DirectInvoiceDetails in storage.
     *
     * @param  int              $id
     * @param UpdateDirectInvoiceDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDirectInvoiceDetailsRequest $request)
    {
        $directInvoiceDetails = $this->directInvoiceDetailsRepository->findWithoutFail($id);

        if (empty($directInvoiceDetails)) {
            Flash::error('Direct Invoice Details not found');

            return redirect(route('directInvoiceDetails.index'));
        }

        $directInvoiceDetails = $this->directInvoiceDetailsRepository->update($request->all(), $id);

        Flash::success('Direct Invoice Details updated successfully.');

        return redirect(route('directInvoiceDetails.index'));
    }

    /**
     * Remove the specified DirectInvoiceDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $directInvoiceDetails = $this->directInvoiceDetailsRepository->findWithoutFail($id);

        if (empty($directInvoiceDetails)) {
            Flash::error('Direct Invoice Details not found');

            return redirect(route('directInvoiceDetails.index'));
        }

        $this->directInvoiceDetailsRepository->delete($id);

        Flash::success('Direct Invoice Details deleted successfully.');

        return redirect(route('directInvoiceDetails.index'));
    }
}
