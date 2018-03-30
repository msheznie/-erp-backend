<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProcumentOrderRequest;
use App\Http\Requests\UpdateProcumentOrderRequest;
use App\Repositories\ProcumentOrderRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ProcumentOrderController extends AppBaseController
{
    /** @var  ProcumentOrderRepository */
    private $procumentOrderRepository;

    public function __construct(ProcumentOrderRepository $procumentOrderRepo)
    {
        $this->procumentOrderRepository = $procumentOrderRepo;
    }

    /**
     * Display a listing of the ProcumentOrder.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->procumentOrderRepository->pushCriteria(new RequestCriteria($request));
        $procumentOrders = $this->procumentOrderRepository->all();

        return view('procument_orders.index')
            ->with('procumentOrders', $procumentOrders);
    }

    /**
     * Show the form for creating a new ProcumentOrder.
     *
     * @return Response
     */
    public function create()
    {
        return view('procument_orders.create');
    }

    /**
     * Store a newly created ProcumentOrder in storage.
     *
     * @param CreateProcumentOrderRequest $request
     *
     * @return Response
     */
    public function store(CreateProcumentOrderRequest $request)
    {
        $input = $request->all();

        $procumentOrder = $this->procumentOrderRepository->create($input);

        Flash::success('Procument Order saved successfully.');

        return redirect(route('procumentOrders.index'));
    }

    /**
     * Display the specified ProcumentOrder.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $procumentOrder = $this->procumentOrderRepository->findWithoutFail($id);

        if (empty($procumentOrder)) {
            Flash::error('Procument Order not found');

            return redirect(route('procumentOrders.index'));
        }

        return view('procument_orders.show')->with('procumentOrder', $procumentOrder);
    }

    /**
     * Show the form for editing the specified ProcumentOrder.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $procumentOrder = $this->procumentOrderRepository->findWithoutFail($id);

        if (empty($procumentOrder)) {
            Flash::error('Procument Order not found');

            return redirect(route('procumentOrders.index'));
        }

        return view('procument_orders.edit')->with('procumentOrder', $procumentOrder);
    }

    /**
     * Update the specified ProcumentOrder in storage.
     *
     * @param  int              $id
     * @param UpdateProcumentOrderRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProcumentOrderRequest $request)
    {
        $procumentOrder = $this->procumentOrderRepository->findWithoutFail($id);

        if (empty($procumentOrder)) {
            Flash::error('Procument Order not found');

            return redirect(route('procumentOrders.index'));
        }

        $procumentOrder = $this->procumentOrderRepository->update($request->all(), $id);

        Flash::success('Procument Order updated successfully.');

        return redirect(route('procumentOrders.index'));
    }

    /**
     * Remove the specified ProcumentOrder from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $procumentOrder = $this->procumentOrderRepository->findWithoutFail($id);

        if (empty($procumentOrder)) {
            Flash::error('Procument Order not found');

            return redirect(route('procumentOrders.index'));
        }

        $this->procumentOrderRepository->delete($id);

        Flash::success('Procument Order deleted successfully.');

        return redirect(route('procumentOrders.index'));
    }
}
