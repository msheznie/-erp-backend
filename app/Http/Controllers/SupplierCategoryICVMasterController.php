<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSupplierCategoryICVMasterRequest;
use App\Http\Requests\UpdateSupplierCategoryICVMasterRequest;
use App\Repositories\SupplierCategoryICVMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SupplierCategoryICVMasterController extends AppBaseController
{
    /** @var  SupplierCategoryICVMasterRepository */
    private $supplierCategoryICVMasterRepository;

    public function __construct(SupplierCategoryICVMasterRepository $supplierCategoryICVMasterRepo)
    {
        $this->supplierCategoryICVMasterRepository = $supplierCategoryICVMasterRepo;
    }

    /**
     * Display a listing of the SupplierCategoryICVMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->supplierCategoryICVMasterRepository->pushCriteria(new RequestCriteria($request));
        $supplierCategoryICVMasters = $this->supplierCategoryICVMasterRepository->all();

        return view('supplier_category_i_c_v_masters.index')
            ->with('supplierCategoryICVMasters', $supplierCategoryICVMasters);
    }

    /**
     * Show the form for creating a new SupplierCategoryICVMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('supplier_category_i_c_v_masters.create');
    }

    /**
     * Store a newly created SupplierCategoryICVMaster in storage.
     *
     * @param CreateSupplierCategoryICVMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierCategoryICVMasterRequest $request)
    {
        $input = $request->all();

        $supplierCategoryICVMaster = $this->supplierCategoryICVMasterRepository->create($input);

        Flash::success('Supplier Category I C V Master saved successfully.');

        return redirect(route('supplierCategoryICVMasters.index'));
    }

    /**
     * Display the specified SupplierCategoryICVMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $supplierCategoryICVMaster = $this->supplierCategoryICVMasterRepository->findWithoutFail($id);

        if (empty($supplierCategoryICVMaster)) {
            Flash::error('Supplier Category I C V Master not found');

            return redirect(route('supplierCategoryICVMasters.index'));
        }

        return view('supplier_category_i_c_v_masters.show')->with('supplierCategoryICVMaster', $supplierCategoryICVMaster);
    }

    /**
     * Show the form for editing the specified SupplierCategoryICVMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $supplierCategoryICVMaster = $this->supplierCategoryICVMasterRepository->findWithoutFail($id);

        if (empty($supplierCategoryICVMaster)) {
            Flash::error('Supplier Category I C V Master not found');

            return redirect(route('supplierCategoryICVMasters.index'));
        }

        return view('supplier_category_i_c_v_masters.edit')->with('supplierCategoryICVMaster', $supplierCategoryICVMaster);
    }

    /**
     * Update the specified SupplierCategoryICVMaster in storage.
     *
     * @param  int              $id
     * @param UpdateSupplierCategoryICVMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierCategoryICVMasterRequest $request)
    {
        $supplierCategoryICVMaster = $this->supplierCategoryICVMasterRepository->findWithoutFail($id);

        if (empty($supplierCategoryICVMaster)) {
            Flash::error('Supplier Category I C V Master not found');

            return redirect(route('supplierCategoryICVMasters.index'));
        }

        $supplierCategoryICVMaster = $this->supplierCategoryICVMasterRepository->update($request->all(), $id);

        Flash::success('Supplier Category I C V Master updated successfully.');

        return redirect(route('supplierCategoryICVMasters.index'));
    }

    /**
     * Remove the specified SupplierCategoryICVMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $supplierCategoryICVMaster = $this->supplierCategoryICVMasterRepository->findWithoutFail($id);

        if (empty($supplierCategoryICVMaster)) {
            Flash::error('Supplier Category I C V Master not found');

            return redirect(route('supplierCategoryICVMasters.index'));
        }

        $this->supplierCategoryICVMasterRepository->delete($id);

        Flash::success('Supplier Category I C V Master deleted successfully.');

        return redirect(route('supplierCategoryICVMasters.index'));
    }
}
