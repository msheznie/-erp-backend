<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProcumentOrderDetailRequest;
use App\Http\Requests\UpdateProcumentOrderDetailRequest;
use App\Repositories\ProcumentOrderDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ProcumentOrderDetailController extends AppBaseController
{
    /** @var  ProcumentOrderDetailRepository */
    private $procumentOrderDetailRepository;

    public function __construct(ProcumentOrderDetailRepository $procumentOrderDetailRepo)
    {
        $this->procumentOrderDetailRepository = $procumentOrderDetailRepo;
    }

    /**
     * Display a listing of the ProcumentOrderDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->procumentOrderDetailRepository->pushCriteria(new RequestCriteria($request));
        $procumentOrderDetails = $this->procumentOrderDetailRepository->all();

        return view('procument_order_details.index')
            ->with('procumentOrderDetails', $procumentOrderDetails);
    }

    /**
     * Show the form for creating a new ProcumentOrderDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('procument_order_details.create');
    }

    /**
     * Store a newly created ProcumentOrderDetail in storage.
     *
     * @param CreateProcumentOrderDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateProcumentOrderDetailRequest $request)
    {
        $input = $request->all();

        $procumentOrderDetail = $this->procumentOrderDetailRepository->create($input);

        Flash::success('Procument Order Detail saved successfully.');

        return redirect(route('procumentOrderDetails.index'));
    }

    /**
     * Display the specified ProcumentOrderDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $procumentOrderDetail = $this->procumentOrderDetailRepository->findWithoutFail($id);

        if (empty($procumentOrderDetail)) {
            Flash::error('Procument Order Detail not found');

            return redirect(route('procumentOrderDetails.index'));
        }

        return view('procument_order_details.show')->with('procumentOrderDetail', $procumentOrderDetail);
    }

    /**
     * Show the form for editing the specified ProcumentOrderDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $procumentOrderDetail = $this->procumentOrderDetailRepository->findWithoutFail($id);

        if (empty($procumentOrderDetail)) {
            Flash::error('Procument Order Detail not found');

            return redirect(route('procumentOrderDetails.index'));
        }

        return view('procument_order_details.edit')->with('procumentOrderDetail', $procumentOrderDetail);
    }

    /**
     * Update the specified ProcumentOrderDetail in storage.
     *
     * @param  int              $id
     * @param UpdateProcumentOrderDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProcumentOrderDetailRequest $request)
    {
        $procumentOrderDetail = $this->procumentOrderDetailRepository->findWithoutFail($id);

        if (empty($procumentOrderDetail)) {
            Flash::error('Procument Order Detail not found');

            return redirect(route('procumentOrderDetails.index'));
        }

        $procumentOrderDetail = $this->procumentOrderDetailRepository->update($request->all(), $id);

        Flash::success('Procument Order Detail updated successfully.');

        return redirect(route('procumentOrderDetails.index'));
    }

    /**
     * Remove the specified ProcumentOrderDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $procumentOrderDetail = $this->procumentOrderDetailRepository->findWithoutFail($id);

        if (empty($procumentOrderDetail)) {
            Flash::error('Procument Order Detail not found');

            return redirect(route('procumentOrderDetails.index'));
        }

        $this->procumentOrderDetailRepository->delete($id);

        Flash::success('Procument Order Detail deleted successfully.');

        return redirect(route('procumentOrderDetails.index'));
    }
}
