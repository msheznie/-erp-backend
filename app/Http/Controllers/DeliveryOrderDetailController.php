<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDeliveryOrderDetailRequest;
use App\Http\Requests\UpdateDeliveryOrderDetailRequest;
use App\Repositories\DeliveryOrderDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DeliveryOrderDetailController extends AppBaseController
{
    /** @var  DeliveryOrderDetailRepository */
    private $deliveryOrderDetailRepository;

    public function __construct(DeliveryOrderDetailRepository $deliveryOrderDetailRepo)
    {
        $this->deliveryOrderDetailRepository = $deliveryOrderDetailRepo;
    }

    /**
     * Display a listing of the DeliveryOrderDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->deliveryOrderDetailRepository->pushCriteria(new RequestCriteria($request));
        $deliveryOrderDetails = $this->deliveryOrderDetailRepository->all();

        return view('delivery_order_details.index')
            ->with('deliveryOrderDetails', $deliveryOrderDetails);
    }

    /**
     * Show the form for creating a new DeliveryOrderDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('delivery_order_details.create');
    }

    /**
     * Store a newly created DeliveryOrderDetail in storage.
     *
     * @param CreateDeliveryOrderDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateDeliveryOrderDetailRequest $request)
    {
        $input = $request->all();

        $deliveryOrderDetail = $this->deliveryOrderDetailRepository->create($input);

        Flash::success('Delivery Order Detail saved successfully.');

        return redirect(route('deliveryOrderDetails.index'));
    }

    /**
     * Display the specified DeliveryOrderDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $deliveryOrderDetail = $this->deliveryOrderDetailRepository->findWithoutFail($id);

        if (empty($deliveryOrderDetail)) {
            Flash::error('Delivery Order Detail not found');

            return redirect(route('deliveryOrderDetails.index'));
        }

        return view('delivery_order_details.show')->with('deliveryOrderDetail', $deliveryOrderDetail);
    }

    /**
     * Show the form for editing the specified DeliveryOrderDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $deliveryOrderDetail = $this->deliveryOrderDetailRepository->findWithoutFail($id);

        if (empty($deliveryOrderDetail)) {
            Flash::error('Delivery Order Detail not found');

            return redirect(route('deliveryOrderDetails.index'));
        }

        return view('delivery_order_details.edit')->with('deliveryOrderDetail', $deliveryOrderDetail);
    }

    /**
     * Update the specified DeliveryOrderDetail in storage.
     *
     * @param  int              $id
     * @param UpdateDeliveryOrderDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDeliveryOrderDetailRequest $request)
    {
        $deliveryOrderDetail = $this->deliveryOrderDetailRepository->findWithoutFail($id);

        if (empty($deliveryOrderDetail)) {
            Flash::error('Delivery Order Detail not found');

            return redirect(route('deliveryOrderDetails.index'));
        }

        $deliveryOrderDetail = $this->deliveryOrderDetailRepository->update($request->all(), $id);

        Flash::success('Delivery Order Detail updated successfully.');

        return redirect(route('deliveryOrderDetails.index'));
    }

    /**
     * Remove the specified DeliveryOrderDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $deliveryOrderDetail = $this->deliveryOrderDetailRepository->findWithoutFail($id);

        if (empty($deliveryOrderDetail)) {
            Flash::error('Delivery Order Detail not found');

            return redirect(route('deliveryOrderDetails.index'));
        }

        $this->deliveryOrderDetailRepository->delete($id);

        Flash::success('Delivery Order Detail deleted successfully.');

        return redirect(route('deliveryOrderDetails.index'));
    }
}
