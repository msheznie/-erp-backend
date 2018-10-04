<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDirectInvoiceDetailsRefferedBackRequest;
use App\Http\Requests\UpdateDirectInvoiceDetailsRefferedBackRequest;
use App\Repositories\DirectInvoiceDetailsRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DirectInvoiceDetailsRefferedBackController extends AppBaseController
{
    /** @var  DirectInvoiceDetailsRefferedBackRepository */
    private $directInvoiceDetailsRefferedBackRepository;

    public function __construct(DirectInvoiceDetailsRefferedBackRepository $directInvoiceDetailsRefferedBackRepo)
    {
        $this->directInvoiceDetailsRefferedBackRepository = $directInvoiceDetailsRefferedBackRepo;
    }

    /**
     * Display a listing of the DirectInvoiceDetailsRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->directInvoiceDetailsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $directInvoiceDetailsRefferedBacks = $this->directInvoiceDetailsRefferedBackRepository->all();

        return view('direct_invoice_details_reffered_backs.index')
            ->with('directInvoiceDetailsRefferedBacks', $directInvoiceDetailsRefferedBacks);
    }

    /**
     * Show the form for creating a new DirectInvoiceDetailsRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('direct_invoice_details_reffered_backs.create');
    }

    /**
     * Store a newly created DirectInvoiceDetailsRefferedBack in storage.
     *
     * @param CreateDirectInvoiceDetailsRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateDirectInvoiceDetailsRefferedBackRequest $request)
    {
        $input = $request->all();

        $directInvoiceDetailsRefferedBack = $this->directInvoiceDetailsRefferedBackRepository->create($input);

        Flash::success('Direct Invoice Details Reffered Back saved successfully.');

        return redirect(route('directInvoiceDetailsRefferedBacks.index'));
    }

    /**
     * Display the specified DirectInvoiceDetailsRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $directInvoiceDetailsRefferedBack = $this->directInvoiceDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($directInvoiceDetailsRefferedBack)) {
            Flash::error('Direct Invoice Details Reffered Back not found');

            return redirect(route('directInvoiceDetailsRefferedBacks.index'));
        }

        return view('direct_invoice_details_reffered_backs.show')->with('directInvoiceDetailsRefferedBack', $directInvoiceDetailsRefferedBack);
    }

    /**
     * Show the form for editing the specified DirectInvoiceDetailsRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $directInvoiceDetailsRefferedBack = $this->directInvoiceDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($directInvoiceDetailsRefferedBack)) {
            Flash::error('Direct Invoice Details Reffered Back not found');

            return redirect(route('directInvoiceDetailsRefferedBacks.index'));
        }

        return view('direct_invoice_details_reffered_backs.edit')->with('directInvoiceDetailsRefferedBack', $directInvoiceDetailsRefferedBack);
    }

    /**
     * Update the specified DirectInvoiceDetailsRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateDirectInvoiceDetailsRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDirectInvoiceDetailsRefferedBackRequest $request)
    {
        $directInvoiceDetailsRefferedBack = $this->directInvoiceDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($directInvoiceDetailsRefferedBack)) {
            Flash::error('Direct Invoice Details Reffered Back not found');

            return redirect(route('directInvoiceDetailsRefferedBacks.index'));
        }

        $directInvoiceDetailsRefferedBack = $this->directInvoiceDetailsRefferedBackRepository->update($request->all(), $id);

        Flash::success('Direct Invoice Details Reffered Back updated successfully.');

        return redirect(route('directInvoiceDetailsRefferedBacks.index'));
    }

    /**
     * Remove the specified DirectInvoiceDetailsRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $directInvoiceDetailsRefferedBack = $this->directInvoiceDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($directInvoiceDetailsRefferedBack)) {
            Flash::error('Direct Invoice Details Reffered Back not found');

            return redirect(route('directInvoiceDetailsRefferedBacks.index'));
        }

        $this->directInvoiceDetailsRefferedBackRepository->delete($id);

        Flash::success('Direct Invoice Details Reffered Back deleted successfully.');

        return redirect(route('directInvoiceDetailsRefferedBacks.index'));
    }
}
