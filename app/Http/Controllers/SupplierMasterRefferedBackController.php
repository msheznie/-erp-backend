<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSupplierMasterRefferedBackRequest;
use App\Http\Requests\UpdateSupplierMasterRefferedBackRequest;
use App\Repositories\SupplierMasterRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SupplierMasterRefferedBackController extends AppBaseController
{
    /** @var  SupplierMasterRefferedBackRepository */
    private $supplierMasterRefferedBackRepository;

    public function __construct(SupplierMasterRefferedBackRepository $supplierMasterRefferedBackRepo)
    {
        $this->supplierMasterRefferedBackRepository = $supplierMasterRefferedBackRepo;
    }

    /**
     * Display a listing of the SupplierMasterRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->supplierMasterRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $supplierMasterRefferedBacks = $this->supplierMasterRefferedBackRepository->all();

        return view('supplier_master_reffered_backs.index')
            ->with('supplierMasterRefferedBacks', $supplierMasterRefferedBacks);
    }

    /**
     * Show the form for creating a new SupplierMasterRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('supplier_master_reffered_backs.create');
    }

    /**
     * Store a newly created SupplierMasterRefferedBack in storage.
     *
     * @param CreateSupplierMasterRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierMasterRefferedBackRequest $request)
    {
        $input = $request->all();

        $supplierMasterRefferedBack = $this->supplierMasterRefferedBackRepository->create($input);

        Flash::success('Supplier Master Reffered Back saved successfully.');

        return redirect(route('supplierMasterRefferedBacks.index'));
    }

    /**
     * Display the specified SupplierMasterRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $supplierMasterRefferedBack = $this->supplierMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($supplierMasterRefferedBack)) {
            Flash::error('Supplier Master Reffered Back not found');

            return redirect(route('supplierMasterRefferedBacks.index'));
        }

        return view('supplier_master_reffered_backs.show')->with('supplierMasterRefferedBack', $supplierMasterRefferedBack);
    }

    /**
     * Show the form for editing the specified SupplierMasterRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $supplierMasterRefferedBack = $this->supplierMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($supplierMasterRefferedBack)) {
            Flash::error('Supplier Master Reffered Back not found');

            return redirect(route('supplierMasterRefferedBacks.index'));
        }

        return view('supplier_master_reffered_backs.edit')->with('supplierMasterRefferedBack', $supplierMasterRefferedBack);
    }

    /**
     * Update the specified SupplierMasterRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateSupplierMasterRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierMasterRefferedBackRequest $request)
    {
        $supplierMasterRefferedBack = $this->supplierMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($supplierMasterRefferedBack)) {
            Flash::error('Supplier Master Reffered Back not found');

            return redirect(route('supplierMasterRefferedBacks.index'));
        }

        $supplierMasterRefferedBack = $this->supplierMasterRefferedBackRepository->update($request->all(), $id);

        Flash::success('Supplier Master Reffered Back updated successfully.');

        return redirect(route('supplierMasterRefferedBacks.index'));
    }

    /**
     * Remove the specified SupplierMasterRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $supplierMasterRefferedBack = $this->supplierMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($supplierMasterRefferedBack)) {
            Flash::error('Supplier Master Reffered Back not found');

            return redirect(route('supplierMasterRefferedBacks.index'));
        }

        $this->supplierMasterRefferedBackRepository->delete($id);

        Flash::success('Supplier Master Reffered Back deleted successfully.');

        return redirect(route('supplierMasterRefferedBacks.index'));
    }
}
