<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSalesPersonMasterRequest;
use App\Http\Requests\UpdateSalesPersonMasterRequest;
use App\Repositories\SalesPersonMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SalesPersonMasterController extends AppBaseController
{
    /** @var  SalesPersonMasterRepository */
    private $salesPersonMasterRepository;

    public function __construct(SalesPersonMasterRepository $salesPersonMasterRepo)
    {
        $this->salesPersonMasterRepository = $salesPersonMasterRepo;
    }

    /**
     * Display a listing of the SalesPersonMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->salesPersonMasterRepository->pushCriteria(new RequestCriteria($request));
        $salesPersonMasters = $this->salesPersonMasterRepository->all();

        return view('sales_person_masters.index')
            ->with('salesPersonMasters', $salesPersonMasters);
    }

    /**
     * Show the form for creating a new SalesPersonMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('sales_person_masters.create');
    }

    /**
     * Store a newly created SalesPersonMaster in storage.
     *
     * @param CreateSalesPersonMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateSalesPersonMasterRequest $request)
    {
        $input = $request->all();

        $salesPersonMaster = $this->salesPersonMasterRepository->create($input);

        Flash::success('Sales Person Master saved successfully.');

        return redirect(route('salesPersonMasters.index'));
    }

    /**
     * Display the specified SalesPersonMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $salesPersonMaster = $this->salesPersonMasterRepository->findWithoutFail($id);

        if (empty($salesPersonMaster)) {
            Flash::error('Sales Person Master not found');

            return redirect(route('salesPersonMasters.index'));
        }

        return view('sales_person_masters.show')->with('salesPersonMaster', $salesPersonMaster);
    }

    /**
     * Show the form for editing the specified SalesPersonMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $salesPersonMaster = $this->salesPersonMasterRepository->findWithoutFail($id);

        if (empty($salesPersonMaster)) {
            Flash::error('Sales Person Master not found');

            return redirect(route('salesPersonMasters.index'));
        }

        return view('sales_person_masters.edit')->with('salesPersonMaster', $salesPersonMaster);
    }

    /**
     * Update the specified SalesPersonMaster in storage.
     *
     * @param  int              $id
     * @param UpdateSalesPersonMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSalesPersonMasterRequest $request)
    {
        $salesPersonMaster = $this->salesPersonMasterRepository->findWithoutFail($id);

        if (empty($salesPersonMaster)) {
            Flash::error('Sales Person Master not found');

            return redirect(route('salesPersonMasters.index'));
        }

        $salesPersonMaster = $this->salesPersonMasterRepository->update($request->all(), $id);

        Flash::success('Sales Person Master updated successfully.');

        return redirect(route('salesPersonMasters.index'));
    }

    /**
     * Remove the specified SalesPersonMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $salesPersonMaster = $this->salesPersonMasterRepository->findWithoutFail($id);

        if (empty($salesPersonMaster)) {
            Flash::error('Sales Person Master not found');

            return redirect(route('salesPersonMasters.index'));
        }

        $this->salesPersonMasterRepository->delete($id);

        Flash::success('Sales Person Master deleted successfully.');

        return redirect(route('salesPersonMasters.index'));
    }
}
