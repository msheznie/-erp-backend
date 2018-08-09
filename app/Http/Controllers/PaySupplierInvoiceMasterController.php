<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaySupplierInvoiceMasterRequest;
use App\Http\Requests\UpdatePaySupplierInvoiceMasterRequest;
use App\Repositories\PaySupplierInvoiceMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PaySupplierInvoiceMasterController extends AppBaseController
{
    /** @var  PaySupplierInvoiceMasterRepository */
    private $paySupplierInvoiceMasterRepository;

    public function __construct(PaySupplierInvoiceMasterRepository $paySupplierInvoiceMasterRepo)
    {
        $this->paySupplierInvoiceMasterRepository = $paySupplierInvoiceMasterRepo;
    }

    /**
     * Display a listing of the PaySupplierInvoiceMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->paySupplierInvoiceMasterRepository->pushCriteria(new RequestCriteria($request));
        $paySupplierInvoiceMasters = $this->paySupplierInvoiceMasterRepository->all();

        return view('pay_supplier_invoice_masters.index')
            ->with('paySupplierInvoiceMasters', $paySupplierInvoiceMasters);
    }

    /**
     * Show the form for creating a new PaySupplierInvoiceMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('pay_supplier_invoice_masters.create');
    }

    /**
     * Store a newly created PaySupplierInvoiceMaster in storage.
     *
     * @param CreatePaySupplierInvoiceMasterRequest $request
     *
     * @return Response
     */
    public function store(CreatePaySupplierInvoiceMasterRequest $request)
    {
        $input = $request->all();

        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->create($input);

        Flash::success('Pay Supplier Invoice Master saved successfully.');

        return redirect(route('paySupplierInvoiceMasters.index'));
    }

    /**
     * Display the specified PaySupplierInvoiceMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMaster)) {
            Flash::error('Pay Supplier Invoice Master not found');

            return redirect(route('paySupplierInvoiceMasters.index'));
        }

        return view('pay_supplier_invoice_masters.show')->with('paySupplierInvoiceMaster', $paySupplierInvoiceMaster);
    }

    /**
     * Show the form for editing the specified PaySupplierInvoiceMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMaster)) {
            Flash::error('Pay Supplier Invoice Master not found');

            return redirect(route('paySupplierInvoiceMasters.index'));
        }

        return view('pay_supplier_invoice_masters.edit')->with('paySupplierInvoiceMaster', $paySupplierInvoiceMaster);
    }

    /**
     * Update the specified PaySupplierInvoiceMaster in storage.
     *
     * @param  int              $id
     * @param UpdatePaySupplierInvoiceMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePaySupplierInvoiceMasterRequest $request)
    {
        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMaster)) {
            Flash::error('Pay Supplier Invoice Master not found');

            return redirect(route('paySupplierInvoiceMasters.index'));
        }

        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->update($request->all(), $id);

        Flash::success('Pay Supplier Invoice Master updated successfully.');

        return redirect(route('paySupplierInvoiceMasters.index'));
    }

    /**
     * Remove the specified PaySupplierInvoiceMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMaster)) {
            Flash::error('Pay Supplier Invoice Master not found');

            return redirect(route('paySupplierInvoiceMasters.index'));
        }

        $this->paySupplierInvoiceMasterRepository->delete($id);

        Flash::success('Pay Supplier Invoice Master deleted successfully.');

        return redirect(route('paySupplierInvoiceMasters.index'));
    }
}
