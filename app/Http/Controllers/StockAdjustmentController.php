<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStockAdjustmentRequest;
use App\Http\Requests\UpdateStockAdjustmentRequest;
use App\Repositories\StockAdjustmentRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class StockAdjustmentController extends AppBaseController
{
    /** @var  StockAdjustmentRepository */
    private $stockAdjustmentRepository;

    public function __construct(StockAdjustmentRepository $stockAdjustmentRepo)
    {
        $this->stockAdjustmentRepository = $stockAdjustmentRepo;
    }

    /**
     * Display a listing of the StockAdjustment.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->stockAdjustmentRepository->pushCriteria(new RequestCriteria($request));
        $stockAdjustments = $this->stockAdjustmentRepository->all();

        return view('stock_adjustments.index')
            ->with('stockAdjustments', $stockAdjustments);
    }

    /**
     * Show the form for creating a new StockAdjustment.
     *
     * @return Response
     */
    public function create()
    {
        return view('stock_adjustments.create');
    }

    /**
     * Store a newly created StockAdjustment in storage.
     *
     * @param CreateStockAdjustmentRequest $request
     *
     * @return Response
     */
    public function store(CreateStockAdjustmentRequest $request)
    {
        $input = $request->all();

        $stockAdjustment = $this->stockAdjustmentRepository->create($input);

        Flash::success('Stock Adjustment saved successfully.');

        return redirect(route('stockAdjustments.index'));
    }

    /**
     * Display the specified StockAdjustment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $stockAdjustment = $this->stockAdjustmentRepository->findWithoutFail($id);

        if (empty($stockAdjustment)) {
            Flash::error('Stock Adjustment not found');

            return redirect(route('stockAdjustments.index'));
        }

        return view('stock_adjustments.show')->with('stockAdjustment', $stockAdjustment);
    }

    /**
     * Show the form for editing the specified StockAdjustment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $stockAdjustment = $this->stockAdjustmentRepository->findWithoutFail($id);

        if (empty($stockAdjustment)) {
            Flash::error('Stock Adjustment not found');

            return redirect(route('stockAdjustments.index'));
        }

        return view('stock_adjustments.edit')->with('stockAdjustment', $stockAdjustment);
    }

    /**
     * Update the specified StockAdjustment in storage.
     *
     * @param  int              $id
     * @param UpdateStockAdjustmentRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStockAdjustmentRequest $request)
    {
        $stockAdjustment = $this->stockAdjustmentRepository->findWithoutFail($id);

        if (empty($stockAdjustment)) {
            Flash::error('Stock Adjustment not found');

            return redirect(route('stockAdjustments.index'));
        }

        $stockAdjustment = $this->stockAdjustmentRepository->update($request->all(), $id);

        Flash::success('Stock Adjustment updated successfully.');

        return redirect(route('stockAdjustments.index'));
    }

    /**
     * Remove the specified StockAdjustment from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $stockAdjustment = $this->stockAdjustmentRepository->findWithoutFail($id);

        if (empty($stockAdjustment)) {
            Flash::error('Stock Adjustment not found');

            return redirect(route('stockAdjustments.index'));
        }

        $this->stockAdjustmentRepository->delete($id);

        Flash::success('Stock Adjustment deleted successfully.');

        return redirect(route('stockAdjustments.index'));
    }
}
