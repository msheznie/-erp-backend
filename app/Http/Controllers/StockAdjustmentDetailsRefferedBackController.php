<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStockAdjustmentDetailsRefferedBackRequest;
use App\Http\Requests\UpdateStockAdjustmentDetailsRefferedBackRequest;
use App\Repositories\StockAdjustmentDetailsRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class StockAdjustmentDetailsRefferedBackController extends AppBaseController
{
    /** @var  StockAdjustmentDetailsRefferedBackRepository */
    private $stockAdjustmentDetailsRefferedBackRepository;

    public function __construct(StockAdjustmentDetailsRefferedBackRepository $stockAdjustmentDetailsRefferedBackRepo)
    {
        $this->stockAdjustmentDetailsRefferedBackRepository = $stockAdjustmentDetailsRefferedBackRepo;
    }

    /**
     * Display a listing of the StockAdjustmentDetailsRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->stockAdjustmentDetailsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $stockAdjustmentDetailsRefferedBacks = $this->stockAdjustmentDetailsRefferedBackRepository->all();

        return view('stock_adjustment_details_reffered_backs.index')
            ->with('stockAdjustmentDetailsRefferedBacks', $stockAdjustmentDetailsRefferedBacks);
    }

    /**
     * Show the form for creating a new StockAdjustmentDetailsRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('stock_adjustment_details_reffered_backs.create');
    }

    /**
     * Store a newly created StockAdjustmentDetailsRefferedBack in storage.
     *
     * @param CreateStockAdjustmentDetailsRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateStockAdjustmentDetailsRefferedBackRequest $request)
    {
        $input = $request->all();

        $stockAdjustmentDetailsRefferedBack = $this->stockAdjustmentDetailsRefferedBackRepository->create($input);

        Flash::success('Stock Adjustment Details Reffered Back saved successfully.');

        return redirect(route('stockAdjustmentDetailsRefferedBacks.index'));
    }

    /**
     * Display the specified StockAdjustmentDetailsRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $stockAdjustmentDetailsRefferedBack = $this->stockAdjustmentDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockAdjustmentDetailsRefferedBack)) {
            Flash::error('Stock Adjustment Details Reffered Back not found');

            return redirect(route('stockAdjustmentDetailsRefferedBacks.index'));
        }

        return view('stock_adjustment_details_reffered_backs.show')->with('stockAdjustmentDetailsRefferedBack', $stockAdjustmentDetailsRefferedBack);
    }

    /**
     * Show the form for editing the specified StockAdjustmentDetailsRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $stockAdjustmentDetailsRefferedBack = $this->stockAdjustmentDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockAdjustmentDetailsRefferedBack)) {
            Flash::error('Stock Adjustment Details Reffered Back not found');

            return redirect(route('stockAdjustmentDetailsRefferedBacks.index'));
        }

        return view('stock_adjustment_details_reffered_backs.edit')->with('stockAdjustmentDetailsRefferedBack', $stockAdjustmentDetailsRefferedBack);
    }

    /**
     * Update the specified StockAdjustmentDetailsRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateStockAdjustmentDetailsRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStockAdjustmentDetailsRefferedBackRequest $request)
    {
        $stockAdjustmentDetailsRefferedBack = $this->stockAdjustmentDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockAdjustmentDetailsRefferedBack)) {
            Flash::error('Stock Adjustment Details Reffered Back not found');

            return redirect(route('stockAdjustmentDetailsRefferedBacks.index'));
        }

        $stockAdjustmentDetailsRefferedBack = $this->stockAdjustmentDetailsRefferedBackRepository->update($request->all(), $id);

        Flash::success('Stock Adjustment Details Reffered Back updated successfully.');

        return redirect(route('stockAdjustmentDetailsRefferedBacks.index'));
    }

    /**
     * Remove the specified StockAdjustmentDetailsRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $stockAdjustmentDetailsRefferedBack = $this->stockAdjustmentDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockAdjustmentDetailsRefferedBack)) {
            Flash::error('Stock Adjustment Details Reffered Back not found');

            return redirect(route('stockAdjustmentDetailsRefferedBacks.index'));
        }

        $this->stockAdjustmentDetailsRefferedBackRepository->delete($id);

        Flash::success('Stock Adjustment Details Reffered Back deleted successfully.');

        return redirect(route('stockAdjustmentDetailsRefferedBacks.index'));
    }
}
