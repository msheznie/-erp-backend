<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLogisticShippingModeRequest;
use App\Http\Requests\UpdateLogisticShippingModeRequest;
use App\Repositories\LogisticShippingModeRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class LogisticShippingModeController extends AppBaseController
{
    /** @var  LogisticShippingModeRepository */
    private $logisticShippingModeRepository;

    public function __construct(LogisticShippingModeRepository $logisticShippingModeRepo)
    {
        $this->logisticShippingModeRepository = $logisticShippingModeRepo;
    }

    /**
     * Display a listing of the LogisticShippingMode.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->logisticShippingModeRepository->pushCriteria(new RequestCriteria($request));
        $logisticShippingModes = $this->logisticShippingModeRepository->all();

        return view('logistic_shipping_modes.index')
            ->with('logisticShippingModes', $logisticShippingModes);
    }

    /**
     * Show the form for creating a new LogisticShippingMode.
     *
     * @return Response
     */
    public function create()
    {
        return view('logistic_shipping_modes.create');
    }

    /**
     * Store a newly created LogisticShippingMode in storage.
     *
     * @param CreateLogisticShippingModeRequest $request
     *
     * @return Response
     */
    public function store(CreateLogisticShippingModeRequest $request)
    {
        $input = $request->all();

        $logisticShippingMode = $this->logisticShippingModeRepository->create($input);

        Flash::success('Logistic Shipping Mode saved successfully.');

        return redirect(route('logisticShippingModes.index'));
    }

    /**
     * Display the specified LogisticShippingMode.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $logisticShippingMode = $this->logisticShippingModeRepository->findWithoutFail($id);

        if (empty($logisticShippingMode)) {
            Flash::error('Logistic Shipping Mode not found');

            return redirect(route('logisticShippingModes.index'));
        }

        return view('logistic_shipping_modes.show')->with('logisticShippingMode', $logisticShippingMode);
    }

    /**
     * Show the form for editing the specified LogisticShippingMode.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $logisticShippingMode = $this->logisticShippingModeRepository->findWithoutFail($id);

        if (empty($logisticShippingMode)) {
            Flash::error('Logistic Shipping Mode not found');

            return redirect(route('logisticShippingModes.index'));
        }

        return view('logistic_shipping_modes.edit')->with('logisticShippingMode', $logisticShippingMode);
    }

    /**
     * Update the specified LogisticShippingMode in storage.
     *
     * @param  int              $id
     * @param UpdateLogisticShippingModeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateLogisticShippingModeRequest $request)
    {
        $logisticShippingMode = $this->logisticShippingModeRepository->findWithoutFail($id);

        if (empty($logisticShippingMode)) {
            Flash::error('Logistic Shipping Mode not found');

            return redirect(route('logisticShippingModes.index'));
        }

        $logisticShippingMode = $this->logisticShippingModeRepository->update($request->all(), $id);

        Flash::success('Logistic Shipping Mode updated successfully.');

        return redirect(route('logisticShippingModes.index'));
    }

    /**
     * Remove the specified LogisticShippingMode from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $logisticShippingMode = $this->logisticShippingModeRepository->findWithoutFail($id);

        if (empty($logisticShippingMode)) {
            Flash::error('Logistic Shipping Mode not found');

            return redirect(route('logisticShippingModes.index'));
        }

        $this->logisticShippingModeRepository->delete($id);

        Flash::success('Logistic Shipping Mode deleted successfully.');

        return redirect(route('logisticShippingModes.index'));
    }
}
