<?php
/**
 * =============================================
 * -- File Name : SalesPersonTargetController.php
 * -- Project Name : ERP
 * -- Module Name :  SalesPersonTargetController
 * -- Author : Mohamed Nazir
 * -- Create date : 21 - January 2019
 * -- Description : This file contains the all CRUD for Sales Person Master
 * -- REVISION HISTORY
 * -- Date: 20-January 2019 By: Nazir Description: Added new function getAllCustomerCategories(),
 */


namespace App\Http\Controllers;

use App\Http\Requests\CreateSalesPersonTargetRequest;
use App\Http\Requests\UpdateSalesPersonTargetRequest;
use App\Repositories\SalesPersonTargetRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SalesPersonTargetController extends AppBaseController
{
    /** @var  SalesPersonTargetRepository */
    private $salesPersonTargetRepository;

    public function __construct(SalesPersonTargetRepository $salesPersonTargetRepo)
    {
        $this->salesPersonTargetRepository = $salesPersonTargetRepo;
    }

    /**
     * Display a listing of the SalesPersonTarget.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->salesPersonTargetRepository->pushCriteria(new RequestCriteria($request));
        $salesPersonTargets = $this->salesPersonTargetRepository->all();

        return view('sales_person_targets.index')
            ->with('salesPersonTargets', $salesPersonTargets);
    }

    /**
     * Show the form for creating a new SalesPersonTarget.
     *
     * @return Response
     */
    public function create()
    {
        return view('sales_person_targets.create');
    }

    /**
     * Store a newly created SalesPersonTarget in storage.
     *
     * @param CreateSalesPersonTargetRequest $request
     *
     * @return Response
     */
    public function store(CreateSalesPersonTargetRequest $request)
    {
        $input = $request->all();

        $salesPersonTarget = $this->salesPersonTargetRepository->create($input);

        Flash::success('Sales Person Target saved successfully.');

        return redirect(route('salesPersonTargets.index'));
    }

    /**
     * Display the specified SalesPersonTarget.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $salesPersonTarget = $this->salesPersonTargetRepository->findWithoutFail($id);

        if (empty($salesPersonTarget)) {
            Flash::error('Sales Person Target not found');

            return redirect(route('salesPersonTargets.index'));
        }

        return view('sales_person_targets.show')->with('salesPersonTarget', $salesPersonTarget);
    }

    /**
     * Show the form for editing the specified SalesPersonTarget.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $salesPersonTarget = $this->salesPersonTargetRepository->findWithoutFail($id);

        if (empty($salesPersonTarget)) {
            Flash::error('Sales Person Target not found');

            return redirect(route('salesPersonTargets.index'));
        }

        return view('sales_person_targets.edit')->with('salesPersonTarget', $salesPersonTarget);
    }

    /**
     * Update the specified SalesPersonTarget in storage.
     *
     * @param  int              $id
     * @param UpdateSalesPersonTargetRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSalesPersonTargetRequest $request)
    {
        $salesPersonTarget = $this->salesPersonTargetRepository->findWithoutFail($id);

        if (empty($salesPersonTarget)) {
            Flash::error('Sales Person Target not found');

            return redirect(route('salesPersonTargets.index'));
        }

        $salesPersonTarget = $this->salesPersonTargetRepository->update($request->all(), $id);

        Flash::success('Sales Person Target updated successfully.');

        return redirect(route('salesPersonTargets.index'));
    }

    /**
     * Remove the specified SalesPersonTarget from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $salesPersonTarget = $this->salesPersonTargetRepository->findWithoutFail($id);

        if (empty($salesPersonTarget)) {
            Flash::error('Sales Person Target not found');

            return redirect(route('salesPersonTargets.index'));
        }

        $this->salesPersonTargetRepository->delete($id);

        Flash::success('Sales Person Target deleted successfully.');

        return redirect(route('salesPersonTargets.index'));
    }
}
