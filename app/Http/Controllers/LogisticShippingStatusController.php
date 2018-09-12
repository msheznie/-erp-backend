<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLogisticShippingStatusRequest;
use App\Http\Requests\UpdateLogisticShippingStatusRequest;
use App\Repositories\LogisticShippingStatusRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class LogisticShippingStatusController extends AppBaseController
{
    /** @var  LogisticShippingStatusRepository */
    private $logisticShippingStatusRepository;

    public function __construct(LogisticShippingStatusRepository $logisticShippingStatusRepo)
    {
        $this->logisticShippingStatusRepository = $logisticShippingStatusRepo;
    }

    /**
     * Display a listing of the LogisticShippingStatus.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->logisticShippingStatusRepository->pushCriteria(new RequestCriteria($request));
        $logisticShippingStatuses = $this->logisticShippingStatusRepository->all();

        return view('logistic_shipping_statuses.index')
            ->with('logisticShippingStatuses', $logisticShippingStatuses);
    }

    /**
     * Show the form for creating a new LogisticShippingStatus.
     *
     * @return Response
     */
    public function create()
    {
        return view('logistic_shipping_statuses.create');
    }

    /**
     * Store a newly created LogisticShippingStatus in storage.
     *
     * @param CreateLogisticShippingStatusRequest $request
     *
     * @return Response
     */
    public function store(CreateLogisticShippingStatusRequest $request)
    {
        $input = $request->all();

        $logisticShippingStatus = $this->logisticShippingStatusRepository->create($input);

        Flash::success('Logistic Shipping Status saved successfully.');

        return redirect(route('logisticShippingStatuses.index'));
    }

    /**
     * Display the specified LogisticShippingStatus.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $logisticShippingStatus = $this->logisticShippingStatusRepository->findWithoutFail($id);

        if (empty($logisticShippingStatus)) {
            Flash::error('Logistic Shipping Status not found');

            return redirect(route('logisticShippingStatuses.index'));
        }

        return view('logistic_shipping_statuses.show')->with('logisticShippingStatus', $logisticShippingStatus);
    }

    /**
     * Show the form for editing the specified LogisticShippingStatus.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $logisticShippingStatus = $this->logisticShippingStatusRepository->findWithoutFail($id);

        if (empty($logisticShippingStatus)) {
            Flash::error('Logistic Shipping Status not found');

            return redirect(route('logisticShippingStatuses.index'));
        }

        return view('logistic_shipping_statuses.edit')->with('logisticShippingStatus', $logisticShippingStatus);
    }

    /**
     * Update the specified LogisticShippingStatus in storage.
     *
     * @param  int              $id
     * @param UpdateLogisticShippingStatusRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateLogisticShippingStatusRequest $request)
    {
        $logisticShippingStatus = $this->logisticShippingStatusRepository->findWithoutFail($id);

        if (empty($logisticShippingStatus)) {
            Flash::error('Logistic Shipping Status not found');

            return redirect(route('logisticShippingStatuses.index'));
        }

        $logisticShippingStatus = $this->logisticShippingStatusRepository->update($request->all(), $id);

        Flash::success('Logistic Shipping Status updated successfully.');

        return redirect(route('logisticShippingStatuses.index'));
    }

    /**
     * Remove the specified LogisticShippingStatus from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $logisticShippingStatus = $this->logisticShippingStatusRepository->findWithoutFail($id);

        if (empty($logisticShippingStatus)) {
            Flash::error('Logistic Shipping Status not found');

            return redirect(route('logisticShippingStatuses.index'));
        }

        $this->logisticShippingStatusRepository->delete($id);

        Flash::success('Logistic Shipping Status deleted successfully.');

        return redirect(route('logisticShippingStatuses.index'));
    }
}
