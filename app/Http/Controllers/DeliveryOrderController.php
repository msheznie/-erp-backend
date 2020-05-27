<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDeliveryOrderRequest;
use App\Http\Requests\UpdateDeliveryOrderRequest;
use App\Repositories\DeliveryOrderRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DeliveryOrderController extends AppBaseController
{
    /** @var  DeliveryOrderRepository */
    private $deliveryOrderRepository;

    public function __construct(DeliveryOrderRepository $deliveryOrderRepo)
    {
        $this->deliveryOrderRepository = $deliveryOrderRepo;
    }

    /**
     * Display a listing of the DeliveryOrder.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->deliveryOrderRepository->pushCriteria(new RequestCriteria($request));
        $deliveryOrders = $this->deliveryOrderRepository->all();

        return view('delivery_orders.index')
            ->with('deliveryOrders', $deliveryOrders);
    }

    /**
     * Show the form for creating a new DeliveryOrder.
     *
     * @return Response
     */
    public function create()
    {
        return view('delivery_orders.create');
    }

    /**
     * Store a newly created DeliveryOrder in storage.
     *
     * @param CreateDeliveryOrderRequest $request
     *
     * @return Response
     */
    public function store(CreateDeliveryOrderRequest $request)
    {
        $input = $request->all();

        $deliveryOrder = $this->deliveryOrderRepository->create($input);

        Flash::success('Delivery Order saved successfully.');

        return redirect(route('deliveryOrders.index'));
    }

    /**
     * Display the specified DeliveryOrder.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $deliveryOrder = $this->deliveryOrderRepository->findWithoutFail($id);

        if (empty($deliveryOrder)) {
            Flash::error('Delivery Order not found');

            return redirect(route('deliveryOrders.index'));
        }

        return view('delivery_orders.show')->with('deliveryOrder', $deliveryOrder);
    }

    /**
     * Show the form for editing the specified DeliveryOrder.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $deliveryOrder = $this->deliveryOrderRepository->findWithoutFail($id);

        if (empty($deliveryOrder)) {
            Flash::error('Delivery Order not found');

            return redirect(route('deliveryOrders.index'));
        }

        return view('delivery_orders.edit')->with('deliveryOrder', $deliveryOrder);
    }

    /**
     * Update the specified DeliveryOrder in storage.
     *
     * @param  int              $id
     * @param UpdateDeliveryOrderRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDeliveryOrderRequest $request)
    {
        $deliveryOrder = $this->deliveryOrderRepository->findWithoutFail($id);

        if (empty($deliveryOrder)) {
            Flash::error('Delivery Order not found');

            return redirect(route('deliveryOrders.index'));
        }

        $deliveryOrder = $this->deliveryOrderRepository->update($request->all(), $id);

        Flash::success('Delivery Order updated successfully.');

        return redirect(route('deliveryOrders.index'));
    }

    /**
     * Remove the specified DeliveryOrder from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $deliveryOrder = $this->deliveryOrderRepository->findWithoutFail($id);

        if (empty($deliveryOrder)) {
            Flash::error('Delivery Order not found');

            return redirect(route('deliveryOrders.index'));
        }

        $this->deliveryOrderRepository->delete($id);

        Flash::success('Delivery Order deleted successfully.');

        return redirect(route('deliveryOrders.index'));
    }
}
