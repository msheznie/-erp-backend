<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSupplierCategoryICVSubRequest;
use App\Http\Requests\UpdateSupplierCategoryICVSubRequest;
use App\Repositories\SupplierCategoryICVSubRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SupplierCategoryICVSubController extends AppBaseController
{
    /** @var  SupplierCategoryICVSubRepository */
    private $supplierCategoryICVSubRepository;

    public function __construct(SupplierCategoryICVSubRepository $supplierCategoryICVSubRepo)
    {
        $this->supplierCategoryICVSubRepository = $supplierCategoryICVSubRepo;
    }

    /**
     * Display a listing of the SupplierCategoryICVSub.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->supplierCategoryICVSubRepository->pushCriteria(new RequestCriteria($request));
        $supplierCategoryICVSubs = $this->supplierCategoryICVSubRepository->all();

        return view('supplier_category_i_c_v_subs.index')
            ->with('supplierCategoryICVSubs', $supplierCategoryICVSubs);
    }

    /**
     * Show the form for creating a new SupplierCategoryICVSub.
     *
     * @return Response
     */
    public function create()
    {
        return view('supplier_category_i_c_v_subs.create');
    }

    /**
     * Store a newly created SupplierCategoryICVSub in storage.
     *
     * @param CreateSupplierCategoryICVSubRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierCategoryICVSubRequest $request)
    {
        $input = $request->all();

        $supplierCategoryICVSub = $this->supplierCategoryICVSubRepository->create($input);

        Flash::success('Supplier Category I C V Sub saved successfully.');

        return redirect(route('supplierCategoryICVSubs.index'));
    }

    /**
     * Display the specified SupplierCategoryICVSub.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $supplierCategoryICVSub = $this->supplierCategoryICVSubRepository->findWithoutFail($id);

        if (empty($supplierCategoryICVSub)) {
            Flash::error('Supplier Category I C V Sub not found');

            return redirect(route('supplierCategoryICVSubs.index'));
        }

        return view('supplier_category_i_c_v_subs.show')->with('supplierCategoryICVSub', $supplierCategoryICVSub);
    }

    /**
     * Show the form for editing the specified SupplierCategoryICVSub.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $supplierCategoryICVSub = $this->supplierCategoryICVSubRepository->findWithoutFail($id);

        if (empty($supplierCategoryICVSub)) {
            Flash::error('Supplier Category I C V Sub not found');

            return redirect(route('supplierCategoryICVSubs.index'));
        }

        return view('supplier_category_i_c_v_subs.edit')->with('supplierCategoryICVSub', $supplierCategoryICVSub);
    }

    /**
     * Update the specified SupplierCategoryICVSub in storage.
     *
     * @param  int              $id
     * @param UpdateSupplierCategoryICVSubRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierCategoryICVSubRequest $request)
    {
        $supplierCategoryICVSub = $this->supplierCategoryICVSubRepository->findWithoutFail($id);

        if (empty($supplierCategoryICVSub)) {
            Flash::error('Supplier Category I C V Sub not found');

            return redirect(route('supplierCategoryICVSubs.index'));
        }

        $supplierCategoryICVSub = $this->supplierCategoryICVSubRepository->update($request->all(), $id);

        Flash::success('Supplier Category I C V Sub updated successfully.');

        return redirect(route('supplierCategoryICVSubs.index'));
    }

    /**
     * Remove the specified SupplierCategoryICVSub from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $supplierCategoryICVSub = $this->supplierCategoryICVSubRepository->findWithoutFail($id);

        if (empty($supplierCategoryICVSub)) {
            Flash::error('Supplier Category I C V Sub not found');

            return redirect(route('supplierCategoryICVSubs.index'));
        }

        $this->supplierCategoryICVSubRepository->delete($id);

        Flash::success('Supplier Category I C V Sub deleted successfully.');

        return redirect(route('supplierCategoryICVSubs.index'));
    }
}
