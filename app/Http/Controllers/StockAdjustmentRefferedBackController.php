<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStockAdjustmentRefferedBackRequest;
use App\Http\Requests\UpdateStockAdjustmentRefferedBackRequest;
use App\Repositories\StockAdjustmentRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class StockAdjustmentRefferedBackController extends AppBaseController
{
    /** @var  StockAdjustmentRefferedBackRepository */
    private $stockAdjustmentRefferedBackRepository;

    public function __construct(StockAdjustmentRefferedBackRepository $stockAdjustmentRefferedBackRepo)
    {
        $this->stockAdjustmentRefferedBackRepository = $stockAdjustmentRefferedBackRepo;
    }

    /**
     * Display a listing of the StockAdjustmentRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->stockAdjustmentRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $stockAdjustmentRefferedBacks = $this->stockAdjustmentRefferedBackRepository->all();

        return view('stock_adjustment_reffered_backs.index')
            ->with('stockAdjustmentRefferedBacks', $stockAdjustmentRefferedBacks);
    }

    /**
     * Show the form for creating a new StockAdjustmentRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('stock_adjustment_reffered_backs.create');
    }

    /**
     * Store a newly created StockAdjustmentRefferedBack in storage.
     *
     * @param CreateStockAdjustmentRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateStockAdjustmentRefferedBackRequest $request)
    {
        $input = $request->all();

        $stockAdjustmentRefferedBack = $this->stockAdjustmentRefferedBackRepository->create($input);

        Flash::success('Stock Adjustment Reffered Back saved successfully.');

        return redirect(route('stockAdjustmentRefferedBacks.index'));
    }

    /**
     * Display the specified StockAdjustmentRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $stockAdjustmentRefferedBack = $this->stockAdjustmentRefferedBackRepository->findWithoutFail($id);

        if (empty($stockAdjustmentRefferedBack)) {
            Flash::error('Stock Adjustment Reffered Back not found');

            return redirect(route('stockAdjustmentRefferedBacks.index'));
        }

        return view('stock_adjustment_reffered_backs.show')->with('stockAdjustmentRefferedBack', $stockAdjustmentRefferedBack);
    }

    /**
     * Show the form for editing the specified StockAdjustmentRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $stockAdjustmentRefferedBack = $this->stockAdjustmentRefferedBackRepository->findWithoutFail($id);

        if (empty($stockAdjustmentRefferedBack)) {
            Flash::error('Stock Adjustment Reffered Back not found');

            return redirect(route('stockAdjustmentRefferedBacks.index'));
        }

        return view('stock_adjustment_reffered_backs.edit')->with('stockAdjustmentRefferedBack', $stockAdjustmentRefferedBack);
    }

    /**
     * Update the specified StockAdjustmentRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateStockAdjustmentRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStockAdjustmentRefferedBackRequest $request)
    {
        $stockAdjustmentRefferedBack = $this->stockAdjustmentRefferedBackRepository->findWithoutFail($id);

        if (empty($stockAdjustmentRefferedBack)) {
            Flash::error('Stock Adjustment Reffered Back not found');

            return redirect(route('stockAdjustmentRefferedBacks.index'));
        }

        $stockAdjustmentRefferedBack = $this->stockAdjustmentRefferedBackRepository->update($request->all(), $id);

        Flash::success('Stock Adjustment Reffered Back updated successfully.');

        return redirect(route('stockAdjustmentRefferedBacks.index'));
    }

    /**
     * Remove the specified StockAdjustmentRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $stockAdjustmentRefferedBack = $this->stockAdjustmentRefferedBackRepository->findWithoutFail($id);

        if (empty($stockAdjustmentRefferedBack)) {
            Flash::error('Stock Adjustment Reffered Back not found');

            return redirect(route('stockAdjustmentRefferedBacks.index'));
        }

        $this->stockAdjustmentRefferedBackRepository->delete($id);

        Flash::success('Stock Adjustment Reffered Back deleted successfully.');

        return redirect(route('stockAdjustmentRefferedBacks.index'));
    }
}
