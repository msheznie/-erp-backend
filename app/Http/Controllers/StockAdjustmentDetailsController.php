<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStockAdjustmentDetailsRequest;
use App\Http\Requests\UpdateStockAdjustmentDetailsRequest;
use App\Repositories\StockAdjustmentDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class StockAdjustmentDetailsController extends AppBaseController
{
    /** @var  StockAdjustmentDetailsRepository */
    private $stockAdjustmentDetailsRepository;

    public function __construct(StockAdjustmentDetailsRepository $stockAdjustmentDetailsRepo)
    {
        $this->stockAdjustmentDetailsRepository = $stockAdjustmentDetailsRepo;
    }

    /**
     * Display a listing of the StockAdjustmentDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->stockAdjustmentDetailsRepository->pushCriteria(new RequestCriteria($request));
        $stockAdjustmentDetails = $this->stockAdjustmentDetailsRepository->all();

        return view('stock_adjustment_details.index')
            ->with('stockAdjustmentDetails', $stockAdjustmentDetails);
    }

    /**
     * Show the form for creating a new StockAdjustmentDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('stock_adjustment_details.create');
    }

    /**
     * Store a newly created StockAdjustmentDetails in storage.
     *
     * @param CreateStockAdjustmentDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateStockAdjustmentDetailsRequest $request)
    {
        $input = $request->all();

        $stockAdjustmentDetails = $this->stockAdjustmentDetailsRepository->create($input);

        Flash::success('Stock Adjustment Details saved successfully.');

        return redirect(route('stockAdjustmentDetails.index'));
    }

    /**
     * Display the specified StockAdjustmentDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $stockAdjustmentDetails = $this->stockAdjustmentDetailsRepository->findWithoutFail($id);

        if (empty($stockAdjustmentDetails)) {
            Flash::error('Stock Adjustment Details not found');

            return redirect(route('stockAdjustmentDetails.index'));
        }

        return view('stock_adjustment_details.show')->with('stockAdjustmentDetails', $stockAdjustmentDetails);
    }

    /**
     * Show the form for editing the specified StockAdjustmentDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $stockAdjustmentDetails = $this->stockAdjustmentDetailsRepository->findWithoutFail($id);

        if (empty($stockAdjustmentDetails)) {
            Flash::error('Stock Adjustment Details not found');

            return redirect(route('stockAdjustmentDetails.index'));
        }

        return view('stock_adjustment_details.edit')->with('stockAdjustmentDetails', $stockAdjustmentDetails);
    }

    /**
     * Update the specified StockAdjustmentDetails in storage.
     *
     * @param  int              $id
     * @param UpdateStockAdjustmentDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStockAdjustmentDetailsRequest $request)
    {
        $stockAdjustmentDetails = $this->stockAdjustmentDetailsRepository->findWithoutFail($id);

        if (empty($stockAdjustmentDetails)) {
            Flash::error('Stock Adjustment Details not found');

            return redirect(route('stockAdjustmentDetails.index'));
        }

        $stockAdjustmentDetails = $this->stockAdjustmentDetailsRepository->update($request->all(), $id);

        Flash::success('Stock Adjustment Details updated successfully.');

        return redirect(route('stockAdjustmentDetails.index'));
    }

    /**
     * Remove the specified StockAdjustmentDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $stockAdjustmentDetails = $this->stockAdjustmentDetailsRepository->findWithoutFail($id);

        if (empty($stockAdjustmentDetails)) {
            Flash::error('Stock Adjustment Details not found');

            return redirect(route('stockAdjustmentDetails.index'));
        }

        $this->stockAdjustmentDetailsRepository->delete($id);

        Flash::success('Stock Adjustment Details deleted successfully.');

        return redirect(route('stockAdjustmentDetails.index'));
    }
}
